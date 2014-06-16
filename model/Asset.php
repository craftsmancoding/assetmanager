<?php
/**
 * Asset
 * For asset management
 *
 * Configuration (via MODX System Settings)
 *
 *
 *
 */
namespace Assman;

class Asset extends BaseModel {

    public $xclass = 'Asset';
    public $default_sort_col = 'title';

    public $src_file;
    public $dst_file;
    public $target_dir;

    public $search_columns = array('title','alt','thumbnail_url','size'); 
    
    /**
     * We house our exceptional tantrums here.
     * Use isNew()  getPK() -- gets the name  getPrimaryKey() -- gets the value
     */
    private function _validFile($src) {
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
     * @param sstring $id_name default 'page_id' 
     * @param string $join ProductAsset
     * @param array $dictate'd asset_id's
     *
     * @return
     */
    public function dictateRelations(array $data, $this_id, $id_name='page_id', $join='PageAsset') {
        $this->modx->setLogLevel(4);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Dictating Asset Relations for id '.$this_id.' with join '.$join.' : '.print_r($data,true),'',__CLASS__,__FILE__,__LINE__);
        
        $dictated = array();
        $seq = 0;
        foreach ($data as $r) {
            $dictated[] = $r['asset_id'];
            // Exists aready?
            if (!$Rel = $this->modx->getObject($join, array($id_name => $this_id, 'asset_id'=> $r['asset_id']))) {
                $Rel = $this->modx->newObject($join, array($id_name => $this_id, 'asset_id'=> $r['asset_id']));
            }
            $Rel->fromArray($r);
            $Rel->set('seq', $seq);
            $seq++;
            if(!$Rel->save()) {
                $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Error saving relation for asset_id '.$Rel->get('asset_id'). ' and '.$id_name.' '.$this_id,'',__CLASS__,__FILE__,__LINE__);
            }
        }

        // Remove un-mentioned
        $existing = $this->modx->getIterator($join, array($id_name => $this_id));
        foreach ($existing as $e) {
            if (!in_array($e->get('asset_id'), $dictated)) {
                if (!$e->remove()) {
                    $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Error removing relation for asset_id '.$e->get('asset_id'). ' and '.$id_name.' '.$this_id,'',__CLASS__,__FILE__,__LINE__);
                }
            }
        }

        
        
        return true;
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
     * Enforces our naming convention for thumbnail images (or any resized images).
     * Desired behavior is like this:
     *
     *  Original image: /lib/path/to/image/foo.jpg   (asset_id 123)
     *  Resized         /lib/resized/123/250x100.jpg
     *  ...etc...
     *
     * @param string $src full path to the original image
     * @param string $subdir to define resized images will be written
     * @param integer $w
     * @param integer $h
     * @return string
     */
    public function getThumbFilename($src,$asset_id,$w,$h) {
        $storage_basedir = $this->modx->getOption('assets_path').rtrim($this->modx->getOption('assman.library_path'),'/').'/';
        $dir = $storage_basedir.'resized/'.$asset_id.'/';
        // dirname : omits trailing slash
        // basename : same as basename()
        // extension : omits period
        // filename : w/o extension
        $p = pathinfo($src);
        return $dir . $w.'x'.$h.'.'.$p['extension'];
//        return sprintf('%s/%s/%s.%sx%s.%s',$p['dirname'],$subdir,$p['filename'],$w,$h,$p['extension']);
    }
    
    /**
     * Get the URL for the thumbnail for a given asset.
     * This will generate the thumbnail if necessary
     *
     * @param object xpdo object representing the asset
     * @return string URL rel to library_path
     */
    public function getThumbnailURL($obj) {

        $w = $this->modx->getOption('assman.thumbnail_width');
        $h = $this->modx->getOption('assman.thumbnail_height');
        
        if (!$obj->get('is_image')) {
            return $this->getMissingThumbnail($w,$h);
        }
        
        $thumbfile = $this->getResizedImage($obj->get('path'), $obj->get('asset_id'), $w, $h);
        //$this->modx->log(4, 'Thumbnail: '.$thumbfile);
        $prefix = $this->modx->getOption('assets_path').$this->modx->getOption('assman.library_path');
        return $this->getRelPath($thumbfile, $prefix);
        
        
    }
    
    /**
     * Create a resized image for the given asset_id
     *
     * @param string $src fullpath to original image
     * @param integer $asset_id primary key
     * @param integer $w
     * @param integer $h (todo)
     * @return string relative URL to thumbnail, rel to $storage_basedir
     */
    public function getResizedImage($src, $asset_id,$w,$h) {
        $this->_validFile($src);
        $dst = $this->getThumbFilename($src, $asset_id,$w,$h);
        if (file_exists($dst)) {
            return $dst;
        }
        return \Craftsmancoding\Image::thumbnail($src,$dst,$w,$h);
    }
    
    /**
     * Used if an image is missing
     *
     * @param integer $w
     * @param integer $h
     */
    public static function getMissingThumbnail($w,$h) {
        //$ext = strtolower(strrchr($this->get('url'), '.'));
        //$w = $this->modx->getOption('assman.thumbnail_width');
        //$h = $this->modx->getOption('assman.thumbnail_height');
        return sprintf('http://placehold.it/%sx%s',$w,$h);
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
        if ($obj = $this->modx->getObject('Asset',array('sig'=>md5_file($src)))) {
            $classname = '\\Assman\\'.$this->xclass;        
            return new $classname($this->modx, $obj); 
        }
            
        return false;
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
     * @return object instance representing new or existing asset
     */
    public function fromFile($FILE, $force_create=false) {
        if (!is_array($FILE)) {
            throw new \Exception('Invalid data type.');
        }
        if (!isset($FILE['tmp_name']) || !isset($FILE['name'])) {
            throw new \Exception('Missing required keys in FILE array.');        
        }
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, print_r($FILE,true),'',__CLASS__,__FUNCTION__,__LINE__);
        
        // From Config
        $storage_basedir = $this->modx->getOption('assets_path').rtrim($this->modx->getOption('assman.library_path'),'/').'/';
        $w = $this->modx->getOption('assman.thumbnail_width');
        $h = $this->modx->getOption('assman.thumbnail_height');
        $subdir = $this->modx->getOption('assman.thumbnail_dir');
        
        $src = $FILE['tmp_name']; // source file
        $this->_validFile($src);
        
        $sig = md5_file($src);
        
        // Existing?
        if (!$force_create) {
            if ($existing = $this->modx->getObject('Asset', array('sig'=>$sig))) {
                $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Existing Asset found with matching signature: '.$existing->get('asset_id'),'',__CLASS__,__FUNCTION__,__LINE__); 
                $classname = '\\Assman\\'.$this->xclass;
                return new $classname($this->modx, $existing); 
            }
        }
                
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Creating Asset from File in Storage Directory: '.$storage_basedir,'',__CLASS__,__FUNCTION__,__LINE__);
        $target_dir = $this->preparePath($storage_basedir.$this->getCalculatedSubdir());
        
        $basename = $FILE['name'];
        $dst = $this->getUniqueFilename($target_dir.$basename);

        $obj = $this->modx->newObject('Asset'); 
        $obj->fromArray($FILE); // get any defaults
        
        $size = (isset($FILE['size'])) ? $FILE['size'] : filesize($src);
        $obj->set('sig', $sig);
        $obj->set('size', $size);
        
        if(!@rename($src,$dst)) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Failed to move asset file from '.$src.' to '.$dst,'',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Could not move file from '.$src.' to '.$dst);
        }
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Moved file from '.$src.' to '.$dst,'',__CLASS__,__FILE__,__LINE__);
        @chmod($dst, 0666); // <-- config?

        
        $C = $this->getContentType($dst);        
        $obj->set('content_type_id', $C->get('id'));
        $obj->set('path', $this->getRelPath($dst, $storage_basedir));
        $obj->set('url', $this->getRelPath($dst, $storage_basedir));   
        if ($info = $this->getImageInfo($dst)) {
            $obj->set('is_image', 1);
            $obj->set('width', $info['width']);
            $obj->set('height', $info['height']);
            $obj->set('duration', $info['duration']);
/*
            if ($thumb_fullpath = $this->getResizedImage($dst,$subdir,$w,$h)) {
                $obj->set('thumbnail_url',$this->getRelPath($thumb_fullpath, $storage_basedir));
            }
*/
        }
        else {
            $obj->set('is_image', 0);
        }
        
        if(!$obj->save()) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Failed to save Asset. Errors: '.print_r($obj->errors,true),'',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Error saving to database.');
        }
        // Store thumbnail
/*
        if ($obj->get('is_image')) {
            if ($thumb_fullpath = $this->getResizedImage($dst,$obj->get('asset_id'),$w,$h)) {
                $obj->set('thumbnail_url',$this->getRelPath($thumb_fullpath, $storage_basedir));
            }        
        }
*/
        
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Saved Asset: '.print_r($obj->toArray(), true),'',__CLASS__,__FUNCTION__,__LINE__);
        $classname = '\\Assman\\'.$this->xclass;
        return new $classname($this->modx, $obj); 
    }
    
    
    /**
     * Given a full path to a file, this strips out the $prefix.
     * (default if null: MODX_ASSET_PATH . assman.library_path)
     * The result ALWAYS omits the leading slash, e.g. "/path/to/something.txt"
     * stripped of "/path/to" becomes "something.txt"
     *
     * @param string $fullpath
     * @param mixed $prefix to remove. Leave null to use MODX settings
     */
    public function getRelPath($fullpath, $prefix=null) {
        if (!is_scalar($fullpath)) {
            throw new \Exception('Invalid data type for path');
        }
        if (!$prefix) {
            $prefix = $this->modx->getOption('assets_path').$this->modx->getOption('assman.library_path');
        }
        
        if (substr($fullpath, 0, strlen($prefix)) == $prefix) {
            return ltrim(substr($fullpath, strlen($prefix)),'/');
        }
        else {
            // either the path was to some other place, or it has already been made relative??
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Prefix ('.$prefix.') not found in path ('.$fullpath.')','',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Prefix not found in path');
        }
    }
    
    /**
     * Returns the $path with trailing slash, creating it if it does not exist 
     * and verifying write permissions.
     * 
     * @param string $path full
     * @param string $umask default 0777
     * @return mixed : string path name on success (w trailing slash), Exception on fail
     */
    public function preparePath($path,$umask=0777) {

        if (!is_scalar($path)) {
            throw new \Exception('Invalid data type for path');
        }
        if (file_exists($path)) {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Path exists: '.$path,'',__CLASS__,__FILE__,__LINE__);            
            if (!is_dir($path)) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Target directory must be a directory. File found instead: '.$path,'',__CLASS__,__FILE__,__LINE__);
                throw new \Exception('Path must be a directory. File found instead.');
            }
        }
        else {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Creating directory '.$path,'',__CLASS__,__FILE__,__LINE__);
            if (!@mkdir($path,$umask,true)) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Failed to recursively create directory '.$path.' with umask '.$umask,'',__CLASS__,__FILE__,__LINE__);
                throw new \Exception('Failed to create directory '.$path);
            }        
        }
        
        $path = rtrim($path,'/').'/';
        
        // Try to write to the directory        
        $tmpfile = $path.'.tmp.'.time();
        if (!touch($tmpfile)) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Failed to write file to directory: '.$path,'',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Could not write to directory '.$path);    
        }
        unlink($tmpfile);
        
        return $path;
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
     * Given a filename, get the file extension WITHOUT the period
     *
     * @param string $filename
     * @return string 
     */
    public function getExt($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /** 
     * Find a MODX content type based on a filename
     *
     * @param string $filename
     * @return object modContentType
     */
    public function getContentType($filename) {
        if (!file_exists($filename)) {
            throw new \Exception('File not found '.$filename);
        }
        // More thorough is to lookup by the mime-type
        if (function_exists('finfo_file') && function_exists('finfo_open')) {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Looking up content type for file '.$filename.' by mime-type','',__CLASS__,__FILE__,__LINE__);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($mime_type = finfo_file($finfo, $filename)) {
                if ($C = $this->modx->getObject('modContentType', array('mime_type'=>$mime_type))) {
                    $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Content Type Found for mime-type '.$mime_type.': '.$C->get('id'),'',__CLASS__,__FILE__,__LINE__);
                    return $C;
                }
            }
        }
        // Fallback to file extension
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Looking up content type for file '.$filename.' by file extension','',__CLASS__,__FILE__,__LINE__);
        if (!$ext = $this->getExt($filename)) {
            throw new \Exception('Extension not found '.$filename);
        }
        if ($C = $this->modx->getObject('modContentType', array('file_extensions'=>'.'.$ext))) {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Content Type Found for extension .'.$ext.': '.$C->get('id'),'',__CLASS__,__FILE__,__LINE__);        
            return $C;
        }
        
        throw new \Exception('Content type not defined.');
    }

    /**
     * Some cleaner repackaging of getimagesize
     *
     * @param string $filename full path to image
     * @return mixed array on success, false on fail
     */
    public function getImageInfo($filename) {
        if($info = @getimagesize($filename)) {
            $output = array();
            $output['width'] = $info[0];
            $output['height'] = $info[1];
            $output['type'] = $info[2]; // <-- see http://www.php.net/manual/en/image.constants.php
            $output['duration'] = '';
            $output['mime'] = $info['mime'];
            return $output;
        }
        return false;
    }
    
    /**
     * Determine whether or not the asset is hosted remotely by examining its url
     * @param string $url
     * @return boolean
     */
    public function isRemote($url) {
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * upload a file to a target directory
     *
     * @param string $tmp_name (from $_FILES['xyz']['tmp_name'])
     * @param string $name (basename from $_FILES['xyz']['name']) 
     * @param string $target_dir where we will write the uploaded file
     * @return string fullpath to new file location or Exception on fail
     */
    public function uploadTmp($tmp_name, $name, $target_dir) {
        $this->_validFile($tmp_name);
        $this->preparePath($target_dir);
        $candidate = rtrim($target_dir,'/').'/'.$name;
        $dst = $this->getUniqueFilename($candidate);
        if (@move_uploaded_file($tmp_name, $dst)) {
            return $dst; // success
        }
        throw new \Exception('Unable to move uploaded file '.$tmp_name.' to '.$dst);
    }
    
    /** 
     * Override parent so we can clean out the asset files
     *
     */
    public function remove() {
        $storage_basedir = $this->modx->getOption('assets_path').$this->modx->getOption('assman.library_path');
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Removing Asset '.$this->getPrimaryKey().' with assets in storage_basedir '.$storage_basedir,'',__CLASS__,__FILE__,__LINE__);
        
        //$file = $storage_basedir.$this->modelObj->get('path');
        $file = $this->modelObj->get('path');        
        if (file_exists($file)) {
            if (!unlink($file)) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Failed to remove file asset for Asset '.$this->getPrimaryKey(). ': '.$file,'',__CLASS__,__FILE__,__LINE__);
                throw new \Exception('Failed to delete asset file.');
            }
        }
        else {
            $this->modx->log(\modX::LOG_LEVEL_INFO, 'File does not exist for Asset '.$this->getPrimaryKey().': '.$file.' This could be because the file was manually deleted or because you did not pass the $storage_basedir parameter.','',__CLASS__,__FILE__,__LINE__);
        }
        // remove thumbnails
/*
        $file = $prefix.$this->modelObj->get('thumbnail_url');
        if (file_exists($file)) {
            if (!unlink($file)) {
                throw new \Exception('Failed to delete thumbnail file.');
            }
        }
*/
        
        return parent::remove();
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
     * Save the asset to the defined storage directory. This means that various sub-directories
     * will be created within the $storage_basedir.  In normal operation, pass this the 
     * assman.library_path setting.
     *
     * @param string $storage_basedir full path
     */
    public function saveTo($storage_basedir) {
        $storage_basedir = $this->preparePath($storage_basedir);
//print "\n".$storage_basedir."\n"; exit;
        $src = $this->modelObj->get('src_file');
        $basename = $this->modelObj->get('src_basename');
//        print "\n".$basename."\n"; exit;
        $this->_validFile($src);

        $target_dir = $this->preparePath($storage_basedir.$this->getCalculatedSubdir());
//print $target_dir; exit;        
        $dst = $this->getUniqueFilename($target_dir.$basename);
        if(!rename($src,$dst)) {
            throw new \Exception('Could not move file from '.$src.' to '.$dst);
        }

        $this->modelObj->set('path', $this->getRelPath($dst, $storage_basedir));
        $this->modelObj->set('url', $this->getRelPath($dst, $storage_basedir));
        $this->modelObj->set('thumbnail_url',$this->getResizedImage($dst, $storage_basedir));
        
        return $this->save();
    }
      
    
    /**
     * Override here to make the url and path relative to the defined assman.library_path
     */
    public function save() {
        // move to 
        // calculate thumbnail?
        // $result = Image::scale($fullpath,$thumbnail_path,$thumb_w);
        //$this->preparePath($this->modelObj->get('target_dir'));
        return parent::save();
    }
}
/*EOF*/