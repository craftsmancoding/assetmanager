<?php
/**
 * The media library is organized thusly:
 *
 *  1. User uploads original full-sized assets, the Asset class organizes these files into
 *      the sub-folders in the library_path according to the date (yyyy/mm/dd).  This must 
 *      be done using the interface.
 *  2. Thumbnails or any resized images are stored inside a folder dedicated to a particular
 *      asset id: library_path/resized/{asset_id}/ 
 *  3. The thumbnail_url is a calculated field using System Settings but it can overridden
 *      the thumbnail size.  This allows a user to change the global thumbnail dimensions and no
 *      database records need to be changed. The behavior of the thumbnail_url field is:
 *          a) blank (normal): return/calc. the thumbnail image for the global WxH settings.
 *          b) full url (override): this must use full protocol (http://), e.g to use an off-site image as the 
 *              thumbnail.
 *          c) fallback (relative URL): assume the file/url exists somewhere on the current site. Prepend the 
 *              MODX_BASE_URL and return the stored value.
 *
 * For simplicity in the data UI, virtual fields are added to each record for thumbnail_width and thumbnail_height.
 *
 * 
 */

require_once dirname(dirname(dirname(__FILE__))).'/vendor/autoload.php';

class Asset extends xPDOObject {

    public $default_sort_col = 'seq';
    // You can't search on calculated columns
    public $search_columns = array('title','alt','stub');
    public static $errors = array(); // used when crawling dirs
    public static $x;
    /**
     * Ye olde Calculated fields (aka Accessors)
     */
    public function __construct(xPDO & $xpdo) { 
        parent::__construct($xpdo);
        self::$x = $xpdo;
        $this->_fields['url'] = $this->get('url');
        $this->_fields['path'] = $this->get('path');
        $this->_fields['basename'] = $this->get('basename');
        $this->_fields['thumbnail_url'] = $this->get('thumbnail_url');
        $this->_fields['thumbnail_width'] = $this->get('thumbnail_width');
        $this->_fields['thumbnail_height'] = $this->get('thumbnail_height');
    }

    /**
     * Make sure we're allowed to upload files of this type
     * @param string $filename
     * @throws Exception
     * @return true
     */
    public function _canUpload($filename) {
        // Are we allowed to upload this file type?
        $ext = ltrim(strtolower(strrchr($filename, '.')),'.');
        $uploadable = explode(',',$this->xpdo->getOption('upload_files'));
        $uploadable = array_map('trim', $uploadable);
        if (!in_array($ext, $uploadable)) {
            throw new \Exception('Uploads not allowed for this file type ('.$ext.')! <a href="?a=70">Adjust the allowed extensions</a> for the <code>upload_files</code> Setting.');
        }
        return true;
    }
    
    /**
     * We house our exceptional tantrums here.
     * Use isNew()  getPK() -- gets the name  getPrimaryKey() -- gets the value
     */
    public function _validFile($src) {
        if (!is_scalar($src)) {
            throw new \Exception('Invalid data type for path');
        }
        if (!file_exists($src)) {
            throw new \Exception('File not found '.$src);
        }
        if (is_dir($src)) {
            throw new \Exception('File must not be a directory '.$src);
        }    
    }

    /**
     * Retrive "all" records matching the filter $args.
     *
     * We use getIterator, but we have to work around the "feature" (bug?) that
     * it will not return an empty array if it has no results. See
     * https://github.com/modxcms/revolution/issues/11373
     *
     * @param $args (including filters)
     * @param boolean $debug
     * @throws Exception
     * @return mixed xPDO iterator (i.e. a collection, but memory efficient) or SQL query string
     */
    public function all($args,$debug=false) {
    
        // If you get this error: "Call to a member function getOption() on a non-object", it could mean:
        // 1) you tried to call this method statically, e.g. Product::all()
        // 2) you forgot to initialize the class and pass a modx instance to the contructor (dependency injection!)
        $limit = (int) $this->xpdo->getOption('limit',$args,$this->xpdo->getOption('assman.default_per_page','',$this->xpdo->getOption('default_per_page')));
        $offset = (int) $this->xpdo->getOption('offset',$args,0);
        $sort = $this->quoteSort($this->xpdo->getOption('sort',$args,$this->default_sort_col));
        $dir = $this->xpdo->getOption('dir',$args,$this->default_sort_dir);
        $select_cols = $this->xpdo->getOption('select',$args);
        
        // Clear out non-filter criteria
        $args = $this->getFilters($args); 
            
        $criteria = $this->xpdo->newQuery('Asset');

        if ($args) {
            if (isset($args['searchterm'])) {
                $searchterm = $args['searchterm'];
                unset($args['searchterm']);
                $search_c = array();
                $first = array_shift($this->search_columns);
                $search_c[$first.':LIKE'] = '%'.$searchterm.'%';
                foreach ($this->search_columns as $c) {
                    $search_c['OR:'.$c.':LIKE'] = '%'.$searchterm.'%'; 
                }
                $criteria->where($search_c);
            }
            else {
                $criteria->where($args);
            }
        }
        

        if ($args) {
            $criteria->where($args);
        }
        
        if ($limit) {
            $criteria->limit($limit, $offset); 
            $criteria->sortby($sort,$dir);
        }
    
        if ($debug) {
            $criteria->prepare();
            return $criteria->toSQL();
        }

        // Both array and string input seem to work
        if (!empty($select_cols)) {
            $criteria->select($select_cols);
        }
        // Workaround for issue https://github.com/modxcms/revolution/issues/11373
        $collection = $this->xpdo->getIterator('Asset',$criteria);
        foreach ($collection as $c) {
            $collection->rewind();           
            return $collection;
        }
        return array();
    }

    /**
     * Get a collection as an array, not as an iterator, e.g. to print as JSON
     *
     */
    public function allAsArray($args) {
        // Package up assest as json
        $Assets = array();
        $results = $this->all($args);
        foreach($results as $r) {
            $Assets[] = $r->toArray();
        }    
        return $Assets;
    }


    /**
     * Dictate related assets (e.g. to a current page).
     * The $data array should describe the *relations*, not the parent asset.  E.g. PageAsset objects,
     * not the Asset objects themselves.  Because this requires a current id (e.g. page id), this can
     * only be run after a page/product etc. has been saved and an id is present.
     *
     * This will remove all assets not in the given $data, add any new pivots, and order them (seq)
     * Additional parameters are made available here for any 3rd party extension to associate assets
     * with an object type other than PageAsset.
     *
     * @param array $data of associated records
     * @param integer $this_id ID of thing being joined to, e.g. this page id
     * @param string $id_name default 'page_id'
     * @param string $join ProductAsset
     * @return bool
     */
    public function dictateRelations(array $data, $this_id, $id_name='page_id', $join='PageAsset') {

        $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Dictating Asset Relations for id '.$this_id.' with join '.$join.' : '.print_r($data,true),'',__CLASS__,__FILE__,__LINE__);
        
        $dictated = array();
        $seq = 0;
        foreach ($data as $r) {
            $dictated[] = $r['asset_id'];
            // Exists aready?
            if (!$Rel = $this->xpdo->getObject($join, array($id_name => $this_id, 'asset_id'=> $r['asset_id']))) {
                $Rel = $this->xpdo->newObject($join, array($id_name => $this_id, 'asset_id'=> $r['asset_id']));
            }
            $Rel->fromArray($r);
            $Rel->set('seq', $seq);
            $seq++;
            if(!$Rel->save()) {
                $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Error saving relation for asset_id '.$Rel->get('asset_id'). ' and '.$id_name.' '.$this_id,'',__CLASS__,__FILE__,__LINE__);
            }
        }

        // Remove un-mentioned
        $existing = $this->xpdo->getIterator($join, array($id_name => $this_id));
        foreach ($existing as $e) {
            if (!in_array($e->get('asset_id'), $dictated)) {
                if (!$e->remove()) {
                    $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Error removing relation for asset_id '.$e->get('asset_id'). ' and '.$id_name.' '.$this_id,'',__CLASS__,__FILE__,__LINE__);
                }
            }
        }
        
        return true;
    }

    /**
     * Create a new asset object and upload the file into an organized directory structure.
     * This was built to handle form submissions and the $_FILES array. Use indexedToRecordset()
     * to process the array for iterating over this function.
     *
     * $FILE = Array
     *           (
     *               [name] => madness.jpg                                (required) 
     *               [type] => image/jpeg
     *               [tmp_name] => /Applications/MAMP/tmp/php/phpNpESmV   (required)
     *               [error] => 0
     *               [size] => 81367                                      (optional: calc'd if ommitted)
     *           )
     *   
     * This needs to handle both uploaded files and existing files (e.g. manually uploaded).
     * If the file has just been uploaded, we move it to a temporary directory $tmpdir.
     *
     * CONFIG (modx system setttings):
     *
     *      assets_path : full path to MODX's assets directory
     *      assman.library_path : path relative to assets_path where Assman will store its images
     *      assman.thumbnail_width
     *      assman.thumbnail_height
     *      assman.thumbnail_dir
     *
     * @param array $FILES structure mimics part of the $_FILES array, see above.
     * @param boolean $force_create if true, a duplicate asset will be created. False will trigger a search for existing asset.
     * @throws Exception
     * @return object Asset instance on success
     */
    public function fromFile($FILE, $force_create=false) {
        if (!is_array($FILE)) {
            throw new \Exception('Invalid data type.');
        }
        if (!isset($FILE['tmp_name']) || !isset($FILE['name'])) {
            throw new \Exception('Missing required keys in FILE array.');        
        }
        $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, print_r($FILE,true),'',__CLASS__,__FUNCTION__,__LINE__);
        
        // From Config
        $storage_basedir = $this->xpdo->getOption('assets_path').rtrim($this->xpdo->getOption('assman.library_path'),'/').'/';
        $w = $this->xpdo->getOption('assman.thumbnail_width');
        $h = $this->xpdo->getOption('assman.thumbnail_height');
        $subdir = $this->xpdo->getOption('assman.thumbnail_dir');
        
        $src = $FILE['tmp_name']; // source file
        $this->_validFile($src);
        $this->_canUpload($FILE['name']);

        $sig = md5_file($src);

        // File already exists?
        if (!$force_create) {
            if ($existing = $this->xpdo->getObject('Asset', array('sig'=>$sig))) {
                $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Existing Asset found with matching signature: '.$existing->get('asset_id'),'',__CLASS__,__FUNCTION__,__LINE__); 
                return $existing;
            }
        }
                
        $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Creating Asset from File in Storage Directory: '.$storage_basedir,'',__CLASS__,__FUNCTION__,__LINE__);
        $target_dir = $this->preparePath($storage_basedir.$this->getCalculatedSubdir());
        
        $basename = $FILE['name'];
        $dst = $this->getUniqueFilename($target_dir.$basename);
        $this->fromArray($FILE); // get any defaults
        $size = (isset($FILE['size'])) ? $FILE['size'] : filesize($src);
        $this->set('sig', $sig);
        $this->set('size', $size);
        
        // Fail if content type cannot be found
        $C = $this->getContentType($FILE);
        
        if(!@rename($src,$dst)) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Failed to move asset file from '.$src.' to '.$dst,'',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Could not move file from '.$src.' to '.$dst);
        }
        
        $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Moved file from '.$src.' to '.$dst,'',__CLASS__,__FILE__,__LINE__);
        @chmod($dst, 0666); // <-- config?
        $stub = $this->getRelPath($dst, $storage_basedir);
        $this->set('content_type_id', $C->get('id'));
        $this->set('stub', $stub);
        if ($info = $this->getMediaInfo($dst)) {
            $this->set('is_image', 1);
            $this->set('width', $info['width']);
            $this->set('height', $info['height']);
            $this->set('duration', $info['duration']);
        }
        else {
            $this->set('is_image', 0);
        }
        
        if(!$this->save()) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Failed to save Asset. Errors: '.print_r($this->errors,true). ' '.print_r($this->toArray(),true),'',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Error saving to database.');
        }
        
        $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Saved Asset: '.print_r($this->toArray(), true),'',__CLASS__,__FUNCTION__,__LINE__);
        return $this;
    }
    
    
    /**
     * Accessors / Modifiers: 
     * 
     * Special behavior here for thumbnails.  If the asset is a remote asset 
     * (e.g. a full http link to imgur.com etc), then no thumbnail should be 
     * 
     * We need to do the mods here at the lowest level so that they will work
     * when complex queries (e.g. getCollectionGraph) are run.
     *
     */
    public function get($k, $format = null, $formatTemplate= null) {
        $raw  = parent::get($k, $format, $formatTemplate);
        if ($k=='url') {
            $manual_url = $this->get('manual_url');
            if(!empty($manual_url)) {
                if (filter_var($manual_url, FILTER_VALIDATE_URL)) {
                    return $manual_url; // Best to include a fully-qualified URL 
                }
                else {
                    return $this->xpdo('site_url').$manual_url; // local relative URL (?) fallback
                }
            }
            elseif ($this->xpdo->getOption('assman.url_override')) {
                return $this->xpdo->getOption('assman.site_url') . $this->xpdo->getOption('assman.library_path').$this->get('stub');
            }
            else {

                return $this->xpdo->getOption('assets_url') . $this->xpdo->getOption('assman.library_path').$this->get('stub');
            }
        }
        elseif ($k=='path') {
            if ($stub = $this->get('stub')) {
                $path = $this->xpdo->getOption('assets_path') . $this->xpdo->getOption('assman.library_path').$stub;
                if (file_exists($path)) {
                    return $path;
                }
                $this->xpdo->log(\modX::LOG_LEVEL_ERROR,'Asset does not exist: '.$path,'',__CLASS__.'::'.__FUNCTION__,__LINE__);
            }
            return null; // No path yet
        }
        elseif ($k=='basename') {
            return basename($this->get('stub'));
        }
        elseif ($k=='thumbnail_url') {
            $override = $this->get('thumbnail_manual_url');
            if (empty($override)) {
                return $this->getThumbnailURL();
            }
            // Passthru if the user has set a full URL
            elseif(filter_var($override, FILTER_VALIDATE_URL)) {
                return $override;
            }
            // relative URL (?) fallback
            return $this->xpdo->getOption('site_url').ltrim($override,'/');
        }
        elseif ($k=='thumbnail_width') {
            return $this->xpdo->getOption('assman.thumbnail_width');
        }
        elseif ($k=='thumbnail_height') {
            return $this->xpdo->getOption('assman.thumbnail_height');
        }
        return $raw;
    }

    /**
     *
     *
     */
    public function getAssetGroups() {
        $groups = trim($this->xpdo->getOption('assman.groups'));
        $groups = (!empty($groups)) ? json_decode($groups) : array();
        if (empty($groups)) {
            $sql = "SELECT DISTINCT `group` FROM ass_page_assets WHERE 'group' != ''";
            foreach ($this->xpdo->query($sql) as $row) {
                $groups[] = $row['group'];
            }
        }
        return $groups;
    }


    /**
     * Handle saving an array of asset $groups
     */
    public function setAssetGroups($groups) {
        $groups = array_unique($groups);
        $groups = array_filter($groups);
        if (!$Setting = $this->xpdo->getObject('modSystemSetting', 'assman.groups')) {
            $Setting = $this->xpdo->newObject('modSystemSetting');
            $Setting->set('key', 'assman.groups');
            $Setting->set('xtype','textfield');
            $Setting->set('namespace','assman');
            $Setting->set('area','assman:default');       
        }
        $value = json_encode($groups);
        $Setting->set('value', $value);
        if (!$Setting->save()) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Could not save System Setting','','Asset::'.__FUNCTION__);    
        }
        // Clear cache
        $cacheRefreshOptions =  array( 'system_settings' => array() );
        $this->xpdo->cacheManager->refresh($cacheRefreshOptions);
        $this->xpdo->setOption('assman.groups',$value);
            
    }
    /**
     * Get the URL for the thumbnail for a given asset.
     * This will generate the thumbnail if necessary
     *
     * @param object xpdo object representing the asset
     * @param integer $w (optional)
     * @param integer $h (optional)
     * @return string URL (schema according to assets_url);
     */
    public function getThumbnailURL($w=null, $h=null) {
        if(!$path = $this->get('path')) {
            return null;
        }
        $w = ($w) ? $w : $this->xpdo->getOption('assman.thumbnail_width');
        $h = ($h) ? $h : $this->xpdo->getOption('assman.thumbnail_height');
        
        if (!$this->get('is_image')) {
            $ext = trim(strtolower(strrchr($this->get('path'), '.')),'.');
            return $this->getMissingThumbnail($w,$h, $ext);
        }
        
        return $this->getResizedImage($this->get('path'), $this->get('asset_id'), $w, $h);
    }

    /**
     * Given a full path to a file, this strips out the $prefix.
     * (default if null: MODX_ASSET_PATH . assman.library_path)
     * The result ALWAYS omits the leading slash, e.g. "/path/to/something.txt"
     * stripped of "/path/to" becomes "something.txt"
     *
     * @param string $fullpath
     * @param mixed $prefix to remove. Leave null to use MODX settings
     * @throws Exception
     */
    public function getRelPath($fullpath, $prefix=null) {

        if (!is_scalar($fullpath)) {
            throw new \Exception('Invalid data type for path');
        }
        if (!$prefix) {
            $prefix = $this->xpdo->getOption('assets_path').$this->xpdo->getOption('assman.library_path');
        }

        if (substr($fullpath, 0, strlen($prefix)) == $prefix) {
            return ltrim(substr($fullpath, strlen($prefix)),'/');
        }
        else {
            // either the path was to some other place, or it has already been made relative??
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Prefix ('.$prefix.') not found in path ('.$fullpath.')','',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Prefix ('.$prefix.') not found in path ('.$fullpath.')');
        }
    }
    
    /**
     * Create a resized image for the given asset_id
     *
     * @param string $src fullpath to original image
     * @param integer $asset_id primary key
     * @param integer $w
     * @param integer $h (todo)
     * @return string URL to thumbnail, according to settings (e.g. subdir, override url)
     */
    public function getResizedImage($src, $asset_id,$w,$h) {
        if (!$asset_id) {
            return '';
        }
        $this->_validFile($src);
        $dst = $this->getThumbFilename($src, $asset_id,$w,$h);

        if (!file_exists($dst)) {
            $dst = \Craftsmancoding\Image::thumbnail($src,$dst,$w,$h);
        }

        $prefix = $this->xpdo->getOption('assets_path').$this->xpdo->getOption('assman.library_path');
        $rel = $this->getRelPath($dst, $prefix);
        if ($this->xpdo->getOption('assman.url_override')) {
            return $this->xpdo->getOption('assman.site_url') . $this->xpdo->getOption('assman.library_path').$rel;
        }
        else {
            return $this->xpdo->getOption('assets_url') . $this->xpdo->getOption('assman.library_path').$rel;
        }
        
        
    }

    /**
     * Enforces our naming convention for thumbnail images (or any resized images).
     * Desired behavior is like this:
     *
     *  Original image: /lib/path/to/image/foo.jpg   (asset_id 123)
     *  Resized         /lib/resized/123/250x100.jpg
     *  ...etc...
     *
     * @param string $src full path to the original image
     * @param integer $asset_id
     * @param integer $w
     * @param integer $h
     * @return string
     */
    public function getThumbFilename($src,$asset_id,$w,$h) {
        $storage_basedir = $this->xpdo->getOption('assets_path').rtrim($this->xpdo->getOption('assman.library_path'),'/').'/';
        $dir = $storage_basedir.'resized/'.$asset_id.'/';
        // dirname : omits trailing slash
        // basename : same as basename()
        // extension : omits period
        // filename : w/o extension
        $p = pathinfo($src);
        return $dir . $w.'x'.$h.'.'.$p['extension'];
    }

    /**
     * Helps check for filename conflicts: given the desired name for the file,
     * this will see if the file already exists, and if so, it will generate a 
     * unique filename for the file while preserving the extension and the basename
     * of the file. E.g. if "x.txt" exists, then this returns "x 1.txt". If "x 1.txt" 
     * exists, this returns "x 2.txt"
     *
     *
     * @param string $dst full path candidate filename.
     * @param string $space_char (optional) to define the character that appears after 
     *      the filename but before the n integer and the extension.
     * @return string
     */
    public function getUniqueFilename($dst,$space_char=' ') {
        if (!file_exists($dst)) {
            return $dst;
        }
        
        // dirname : omits trailing slash
        // basename : same as basename()
        // extension : omits period
        // filename : w/o extension
        $p = pathinfo($dst);
        $i = 1;
        while(true){
            $filename = $p['dirname'].'/'.$p['filename'].$space_char.$i.'.'.$p['extension'];
            if (!file_exists($filename)) break;
            $i++;
        }
        return $filename;
    }

    /**
     * Used if an image is missing
     *
     * @param integer $w
     * @param integer $h
     * @return string
     */
    public static function getMissingThumbnail($w,$h,$text) {
        //$ext = strtolower(strrchr($this->get('url'), '.'));
        //$w = $this->xpdo->getOption('assman.thumbnail_width');
        //$h = $this->xpdo->getOption('assman.thumbnail_height');
        return sprintf('http://placehold.it/%sx%s/648a1e/ffffff/&text=%s',$w,$h,$text);
    }
    
    /**
     * Given a filename, this checks whether the asset already exists by
     * examining its md5 signature. 
     *
     * @string $src filename
     * @return mixed : object of the existing asset on success, boolean false on fail.
     */
    public function getExisting($src) {
        $this->_validFile($src);
        if ($obj = $this->xpdo->getObject('Asset',array('sig'=>md5_file($src)))) {
            return $obj; 
        }
            
        return false;
    }


    /**
     * We must organize assets somehow into subfolders.
     *
     * @return string sub directory with trailing slash, e.g. "2014/05/28/"
     */
    public function getCalculatedSubdir() {
        return date('Y/m/d/');
    }

    /** 
     * Find a MODX content type based on a filename. This should be executable before a file has been
     * moved into place.
     *
     * Array
     * (
     *   [name] => example.pdf
     *   [type] => application/pdf
     *   [tmp_name] => /tmp/path/somewhere/phpkAYQwR
     *   [error] => 0
     *   [size] => 2109
     *)
     *
     * @param array $FILE from upload (in case we need to auto-create)
     * @throws Exception
     * @return object modContentType
     */
    public function getContentType($FILE) {
        // Lookup by the mime-type
        if ($C = $this->xpdo->getObject('modContentType', array('mime_type'=>$FILE['type']))) {
            $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Content Type Found for mime-type '.$FILE['type'].': '.$C->get('id'),'',__CLASS__,__FILE__,__LINE__);
            return $C;
        }

        // Fallback to file extension
        $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Looking up content type for file '.$FILE['name'].' by file extension','',__CLASS__,__FILE__,__LINE__);
        $ext = ltrim(strtolower(strrchr($FILE['name'], '.')),'.');
        if ($C = $this->xpdo->getObject('modContentType', array('file_extensions'=>'.'.$ext))) {
            $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Content Type Found for extension .'.$ext.': '.$C->get('id'),'',__CLASS__,__FILE__,__LINE__);        
            return $C;
        }
        
        // Final chance: auto-create the content type        
        $this->xpdo->log(\modX::LOG_LEVEL_WARN, 'Unknown Content Type for file '.$FILE['name'],'',__CLASS__,__FILE__,__LINE__);
        if ($this->xpdo->getOption('assman.autocreate_content_type')) {
            $this->xpdo->log(\modX::LOG_LEVEL_INFO, 'Attempting to auto-create modContentType for file '.$FILE['name'],'',__CLASS__,__FILE__,__LINE__);
            $C = $this->xpdo->newObject('modContentType');
            $C->set('name', strtoupper($ext));
            $C->set('file_extensions', '.'.$ext);
            $C->set('binary', $this->isBinary($FILE['tmp_name'])); 
            $C->set('description', 'Automatically created by Asset Manager');
            $C->set('mime_type', $FILE['type']);
            if (!$C->save()) {
                throw new \Exception('Failed to automatically create content type for file.');        
            }
            return $C;
        }
        
        throw new \Exception('Content type not defined for files of type '.$ext.'. Enable auto-creation of content types by adjusting the assman.autocreate_content_type setting.');
    }


    /** 
     * Given a filename, get the file extension WITHOUT the period
     *
     * @param string $filename
     * @return string 
     */
    public function getExt($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Remove any "control" arguments and return only "filter" arguments
     * with some convenience bits for searches. Controls are things like limit, offset,
     * or other things that control HOW the results are returned whereas filters determine
     * WHAT gets returned.
     *
     * @param array
     * @return array
     */
    public function getFilters($array) {
        foreach ($this->control_params as $p) {
            unset($array[$p]);
        }
        
        foreach ($array as $k => $v) {
            // For convenience, we add in the %'s
            if (strtoupper(substr($k,-5)) == ':LIKE') $array[$k] = '%'.$v.'%';
            if (strtoupper(substr($k,-9)) == ':NOT LIKE') $array[$k] = '%'.$v.'%';
            if (strtoupper(substr($k,-12)) == ':STARTS WITH') {
                unset($array[$k]);
                $array[substr($k,0,-12).':LIKE'] = $v.'%';
            }
            if (strtoupper(substr($k,-10)) == ':ENDS WITH') {
                unset($array[$k]);
                $array[substr($k,0,-10).':LIKE'] = '%'.$v;
            }

            // Remove any simple array stuff
            if (is_integer($k)) unset($array[$k]);
        }
        return $array;
    }

    /**
     * Some cleaner repackaging of getimagesize
     *
     * @param string $filename full path to image
     * @return mixed array on success, false on fail
     */
    public function getMediaInfo($filename) {
        if($info = @getimagesize($filename)) {
            $output = array();
            $output['width'] = $info[0];
            $output['height'] = $info[1];
            $output['type'] = $info[2]; // <-- see http://www.php.net/manual/en/image.constants.php
            $output['duration'] = ''; // TODO
            $output['mime'] = $info['mime'];
            return $output;
        }
        return false;
    }

    /**
     * Convert data in an indexed structure to a recordset.
     * This is necessary when processing forms with multiple records of data:
     *
     *     Record 1:
     *      <input name="x[]" value="A"/>
     *      <input name="y[]" value="B">
     *     Record 2:
     *      <input name="x[]" value="C"/>
     *      <input name="y[]" value="D"/>
     * 
     * Data arrives indexed like this:
     *      array( 'x' => array(0=>A, 1=>C) ),
     *      array( 'y' => array(0=>B, 1=>D) ),
     *
     * Whereas we want it formatted as a recordset like this:
     *      array(
     *          array('x'=>'A', 'y' => 'B'),
     *          array('x'=>'C', 'y' => 'D'),
     *     )
     *
     * This function converts the format.  It also handles simple hashes: in that case,
     * the simple hash is "wrapped" in array, thus returning a "record-set" with 1 record.
     *
     * @param array $indexed array
     * @return array record set
     */
    public static function indexedToRecordset(array $indexed) {
        $out = array();
        foreach($indexed as $k => $v) {
            $v = (array) $v;
            foreach ($v as $i => $v2) {
                $out[$i][$k] = $v[$i];
            }
        }
        return $out;        
    }
    
    /**
     * Is the file binary?
     * From http://stackoverflow.com/questions/3872877/how-to-check-if-uploaded-file-is-binary-file
     * @param string $file (full file name and path)
     * @return integer like boolean
     */
    public function isBinary($file) { 
        if (file_exists($file)) { 
            if (!is_file($file)) return 0; 
            $fh  = fopen($file, "r"); 
            $blk = fread($fh, 512); 
            fclose($fh); 
            clearstatcache(); 
            return ( 
              0 or substr_count($blk, "^ -~", "^\r\n")/512 > 0.3 
                or substr_count($blk, "\x00") > 0 ); 
        }
         
        return 0; 
    } 


    /**
     * Returns the $path with trailing slash, creating it if it does not exist 
     * and verifying write permissions.
     * 
     * @param string $path full
     * @param string $umask default 0777
     * @throws Exception
     * @return mixed : string path name on success (w trailing slash), Exception on fail
     */
    public function preparePath($path,$umask=0777) {

        if (!is_scalar($path)) {
            throw new \Exception('Invalid data type for path');
        }
        if (file_exists($path)) {
            $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Path exists: '.$path,'',__CLASS__,__FILE__,__LINE__);            
            if (!is_dir($path)) {
                $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Target directory must be a directory. File found instead: '.$path,'',__CLASS__,__FILE__,__LINE__);
                throw new \Exception('Path must be a directory. File found instead.');
            }
        }
        else {
            $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Creating directory '.$path,'',__CLASS__,__FILE__,__LINE__);
            if (!@mkdir($path,$umask,true)) {
                $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Failed to recursively create directory '.$path.' with umask '.$umask,'',__CLASS__,__FILE__,__LINE__);
                throw new \Exception('Failed to create directory '.$path);
            }        
        }
        
        $path = rtrim($path,'/').'/';
        
        // Try to write to the directory        
        $tmpfile = $path.'.tmp.'.time();
        if (!touch($tmpfile)) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Failed to write file to directory: '.$path,'',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Could not write to directory '.$path);    
        }
        unlink($tmpfile);
        
        return $path;
    }

    /**
     * Some strings like "group" will fail if you try to use them as a sort column, e.g.
     *      SELECT * FROM table ORDER BY group ASC LIMIT 20 
     * So this will properly quote a SQL column. 
     *      group --> `group`
     *      `group` --> `group` (unchanged)
     *      tbl.col --> `tbl`.`col`
     */
    public function quoteSort($str) {
        if (!is_scalar($str)) {
            throw new \Exception('quoteSort expects string');
        }
        $parts = explode('.',$str);
        $parts = array_map(function($v){ return '`'.trim($v,'`').'`'; }, $parts);
        return implode('.',$parts);
    }
    
    /** 
     * We override the parent func so we can clean out the asset files
     */
    public function remove(array $ancestors=array()) {
        $storage_basedir = $this->xpdo->getOption('assets_path').rtrim($this->xpdo->getOption('assman.library_path'),'/').'/';
        $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Removing Asset '.$this->getPrimaryKey().' with assets in storage_basedir '.$storage_basedir,'',__CLASS__,__FILE__,__LINE__);
        
        $file = $this->get('path');        
        if (file_exists($file)) {
            if (!unlink($file)) {
                $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Failed to remove file asset for Asset '.$this->getPrimaryKey(). ': '.$file,'',__CLASS__,__FILE__,__LINE__);
                throw new \Exception('Failed to delete asset file.');
            }
        }
        else {
            $this->xpdo->log(\modX::LOG_LEVEL_INFO, 'File does not exist for Asset '.$this->getPrimaryKey().': '.$file.' This could be because the file was manually deleted or because you did not pass the $storage_basedir parameter.','',__CLASS__,__FILE__,__LINE__);
        }
        
        // remove thumbnails
        $storage_basedir = $this->xpdo->getOption('assets_path').rtrim($this->xpdo->getOption('assman.library_path'),'/').'/';
        $dir = $storage_basedir.'resized/'.$this->get('asset_id').'/';
        self::rrmdir($dir);
                
        return parent::remove($ancestors);
    } 

    /** 
     * Recursively remove a non-empty directory
     *
     */
    public static function rrmdir($dir) { 
        if (is_dir($dir)) { 
            $dir = rtrim($dir,'/');
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != '.' && $object != '..') { 
                    if (filetype($dir.'/'.$object) == 'dir') {
                        self::rrmdir($dir.'/'.$object); 
                    }
                    else {
                        unlink($dir.'/'.$object); 
                    }
                } 
            } 
            reset($objects); 
            rmdir($dir); 
        } 
    }
    
    /**
     * Verify that each asset in the DB references a file that exists.
     * We should run this before verifying files because we want to ensure that md5 signatures are 
     * correct.
     */
    public function verifyDB(){
        $errors = array();
        $Assets = $this->xpdo->getIterator('Asset');
        foreach ($Assets as $A) {
            // Missing File
            if (!file_exists($A->get('path'))) {
                $errors[] = array(
                    'status' => 'error',
                    'code' => 'missing_file',
                    'message' => 'File does not exist',
                    'data' => $A->toArray()
                );
                continue;
            }
            // Verify signature
            $actual = md5_file($A->get('path'));
            if ($actual != $A->get('sig')) {
                $errors[] = array(
                    'status' => 'error',
                    'code' => 'incorrect_signature',
                    'message' => 'File has been modified. Signature incorrect.',
                    'data' => $A->toArray()
                );            
            }
            
        }
        return $errors;
    }
    
    /**
     * Crawl the directories, verify that no extra files are there, or that nothing has been moved.
     */
    public function verifyFiles() {
        // Check main lib
        $path = $this->xpdo->getOption('assets_path') . $this->xpdo->getOption('assman.library_path').$stub;
        if (!file_exists($path)) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR,'Asset does not exist: '.$path,'',__CLASS__.'::'.__FUNCTION__,__LINE__);
            return;
        }
        
        self::crawlDir($path);
        // Check thumb dirs too?
        return self::$errors;

    }

    /**
     * @param string $dir name
     */
    public static function crawlDir($dir) {
        if (is_dir($dir)) { 
            $dir = rtrim($dir,'/');
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != '.' && $object != '..') { 
                    if (filetype($dir.'/'.$object) == 'dir') {
                        self::crawlDir($dir.'/'.$object); 
                    }
                    else {
                        self::checkFile($dir.'/'.$object); 
                    }
                } 
            } 
            reset($objects); 
        } 
    
        // unaccounted for file... do we have a matching signature?
        // 
        // self::$errors[] = array();    
    }
    
    public static function checkFile($file) {
        // Who are you?
        $sig = md5_file($file);
        $Asset = self::$x->getObject('Asset', array('sig'=>$sig));
        if ($Asset) {
            // are you where you are supposed to be?
            if ($Asset->get('path') != $file) {
                self::$errors[] = array(
                    'status' => 'error',
                    'code' => 'incorrect_location',
                    'message' => 'File has been moved to '.$file,
                    'data' => $A->toArray()
                );
            }
        }
        // Intruder! (or the sig in the db is incorrect)
        else {
            self::$errors[] = array(
                'status' => 'error',
                'code' => 'untracked_file',
                'message' => 'Untracked file: '.$file,
                'data' => $A->toArray()
            );
        }
        //$storage_basedir = $this->xpdo->getOption('assets_path').rtrim($this->xpdo->getOption('assman.library_path'),'/').'/';
        //$stub = $this->getRelPath($dst, $storage_basedir);
        // unaccounted for file... do we have a matching signature?
        
        // 
        // self::$errors[] = array();    
    
    }
}
