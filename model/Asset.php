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
        //$this->modx->setLogLevel(4);
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
     * Create a resized image for the given asset_id
     *
     * @param string $src fullpath to original image
     * @param integer $asset_id primary key
     * @param integer $w
     * @param integer $h (todo)
     * @return string relative URL to thumbnail, rel to $storage_basedir
     */
/*
    public function getResizedImage($src, $asset_id,$w,$h) {
        $this->_validFile($src);
        $dst = $this->getThumbFilename($src, $asset_id,$w,$h);
        if (file_exists($dst)) {
            return $dst;
        }
        return \Craftsmancoding\Image::thumbnail($src,$dst,$w,$h);
    }
    
*/
    /**
     * Used if an image is missing
     *
     * @param integer $w
     * @param integer $h
     */
    public static function getMissingThumbnail($w,$h,$text) {
        //$ext = strtolower(strrchr($this->get('url'), '.'));
        //$w = $this->modx->getOption('assman.thumbnail_width');
        //$h = $this->modx->getOption('assman.thumbnail_height');
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
        if ($obj = $this->modx->getObject('Asset',array('sig'=>md5_file($src)))) {
            $classname = '\\Assman\\'.$this->xclass;        
            return new $classname($this->modx, $obj); 
        }
            
        return false;
    }

    /**
     * Is the file binary?
     * From http://stackoverflow.com/questions/3872877/how-to-check-if-uploaded-file-is-binary-file
     * @param string $file (full file name and path)
     * @return integer like boolean
     */
    public function isBinary($file) 
    { 
        if (file_exists($file)) { 
        if (!is_file($file)) return 0; 
        
        $fh  = fopen($file, "r"); 
        $blk = fread($fh, 512); 
        fclose($fh); 
        clearstatcache(); 
        
        return ( 
          0 or substr_count($blk, "^ -~", "^\r\n")/512 > 0.3 
            or substr_count($blk, "\x00") > 0 
        ); 
        } 
        return 0; 
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
        
        // Are we allowed to upload this file type?
        $ext = ltrim(strtolower(strrchr($FILE['name'], '.')),'.');
        $uploadable = explode(',',$this->modx->getOption('upload_files'));
        $uploadable = array_map('trim', $uploadable);
        if (!in_array($ext, $uploadable)) {
            throw new \Exception('Uploads not allowed for this file type ('.$ext.')! <a href="?a=70">Adjust the allowed extensions</a> for the <code>upload_files</code> Setting.');
        }
        
        $sig = md5_file($src);

        // File already exists?
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
        
        // Fail if content type cannot be found
        $C = $this->getContentType($FILE);
        
        if(!@rename($src,$dst)) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Failed to move asset file from '.$src.' to '.$dst,'',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Could not move file from '.$src.' to '.$dst);
        }
        
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Moved file from '.$src.' to '.$dst,'',__CLASS__,__FILE__,__LINE__);
        @chmod($dst, 0666); // <-- config?
        $path = $obj->getRelPath($dst, $storage_basedir);
        $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Dst'.$dst.' Storage: '.$storage_basedir.' path:' .$path,'',__CLASS__,__FILE__,__LINE__);
        $obj->set('content_type_id', $C->get('id'));
        $obj->set('path', $path);
        //$obj->set('url', $this->getRelPath($dst, $storage_basedir));   
        if ($info = $this->getImageInfo($dst)) {
            $obj->set('is_image', 1);
            $obj->set('width', $info['width']);
            $obj->set('height', $info['height']);
            $obj->set('duration', $info['duration']);
        }
        else {
            $obj->set('is_image', 0);
        }
        
        if(!$obj->save()) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Failed to save Asset. Errors: '.print_r($obj->errors,true). ' '.print_r($obj->toArray(),true),'',__CLASS__,__FILE__,__LINE__);
            throw new \Exception('Error saving to database.');
        }
        
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Saved Asset: '.print_r($obj->toArray(), true),'',__CLASS__,__FUNCTION__,__LINE__);
        $classname = '\\Assman\\'.$this->xclass;
        return new $classname($this->modx, $obj); 
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
     * @return object modContentType
     */
    public function getContentType($FILE) {
        // Lookup by the mime-type
        if ($C = $this->modx->getObject('modContentType', array('mime_type'=>$FILE['type']))) {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Content Type Found for mime-type '.$FILE['type'].': '.$C->get('id'),'',__CLASS__,__FILE__,__LINE__);
            return $C;
        }

        // Fallback to file extension
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Looking up content type for file '.$FILE['name'].' by file extension','',__CLASS__,__FILE__,__LINE__);
        $ext = ltrim(strtolower(strrchr($FILE['name'], '.')),'.');
        if ($C = $this->modx->getObject('modContentType', array('file_extensions'=>'.'.$ext))) {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Content Type Found for extension .'.$ext.': '.$C->get('id'),'',__CLASS__,__FILE__,__LINE__);        
            return $C;
        }
        
        // Final chance: auto-create the content type        
        $this->modx->log(\modX::LOG_LEVEL_WARN, 'Unknown Content Type for file '.$FILE['name'],'',__CLASS__,__FILE__,__LINE__);
        if ($this->modx->getOption('assman.autocreate_content_type')) {
            $this->modx->log(\modX::LOG_LEVEL_INFO, 'Attempting to auto-create modContentType for file '.$FILE['name'],'',__CLASS__,__FILE__,__LINE__);
            $C = $this->modx->newObject('modContentType');
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
    
}
/*EOF*/