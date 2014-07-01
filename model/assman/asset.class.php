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

    /**
     * Override to provide calculated fields
     */
    public function __construct(xPDO & $xpdo) { 
        parent::__construct($xpdo);
        $this->_fields['thumbnail_url'] = $this->get('thumbnail_url'); // wormhole
        $this->_fields['thumbnail_width'] = $this->get('thumbnail_width');
        $this->_fields['thumbnail_height'] = $this->get('thumbnail_height');
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
     * Modifiers: 
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
            if(filter_var($raw, FILTER_VALIDATE_URL)) {
                return $raw;
            }
            elseif ($this->xpdo->getOption('assman.url_override')) {
                return $this->xpdo->getOption('assman.site_url') . $this->xpdo->getOption('assman.library_path').$raw;
            }
            else {
                return $this->xpdo->getOption('assets_url') . $this->xpdo->getOption('assman.library_path').$raw;
            }
            
        }
        elseif ($k=='path') {
            return $this->xpdo->getOption('assets_path') . $this->xpdo->getOption('assman.library_path').$raw;    
        }
        elseif ($k=='thumbnail_url') {
            if ($this->isNew()) {
                return ''; // otherwise you get exceptions because path isn't set yet
            }

            $override = $this->get('thumbnail_override_url');
            if (empty($override)) {
                return $this->getThumbnailURL();
            }
            // Passthru if the user has set a full URL
            elseif(filter_var($override, FILTER_VALIDATE_URL)) {
                return $override;
            }
            // relative URL (?) fallback
            return MODX_SITE_URL .ltrim($override,'/');
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
     * Get the URL for the thumbnail for a given asset.
     * This will generate the thumbnail if necessary
     *
     * @param object xpdo object representing the asset
     * @param integer $w (optional)
     * @param integer $h (optional)
     * @return string URL (schema according to assets_url);
     */
    public function getThumbnailURL($w=null, $h=null) {
        $w = ($w) ? $w : $this->xpdo->getOption('assman.thumbnail_width');
        $h = ($h) ? $h : $this->xpdo->getOption('assman.thumbnail_height');
        
        if (!$this->get('is_image')) {
            $ext = trim(strtolower(strrchr($this->get('path'), '.')),'.');
            return \Assman\Asset::getMissingThumbnail($w,$h, $ext);
        }
        $thumbfile = $this->getResizedImage($this->get('path'), $this->get('asset_id'), $w, $h);
        //$this->xpdo->log(4, 'Thumbnail: '.$thumbfile);
        $prefix = $this->xpdo->getOption('assets_path').$this->xpdo->getOption('assman.library_path');
        $rel = $this->getRelPath($thumbfile, $prefix);
        if ($this->xpdo->getOption('assman.url_override')) {
            return $this->xpdo->getOption('assman.site_url') . $this->xpdo->getOption('assman.library_path').$rel;
        }
        else {
            return $this->xpdo->getOption('assets_url') . $this->xpdo->getOption('assman.library_path').$rel;
        }
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
/*
        $asset_id = $this->get('asset_id');
        if (!$asset_id) {
            return '';
        }
*/
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
     * @return string relative URL to thumbnail, rel to $storage_basedir
     */
    public function getResizedImage($src, $asset_id,$w,$h) {
        if (!$asset_id) {
            return '';
        }
        $this->_validFile($src);
        $dst = $this->getThumbFilename($src, $asset_id,$w,$h);
        if (file_exists($dst)) {
            return $dst;
        }
        return \Craftsmancoding\Image::thumbnail($src,$dst,$w,$h);
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
     * Override parent so we can clean out the asset files
     *
     */
    public function remove() {
        $storage_basedir = $this->xpdo->getOption('assets_path').$this->xpdo->getOption('assman.library_path');
        $this->xpdo->log(\modX::LOG_LEVEL_DEBUG, 'Removing Asset '.$this->getPrimaryKey().' with assets in storage_basedir '.$storage_basedir,'',__CLASS__,__FILE__,__LINE__);
        
        //$file = $storage_basedir.$this->modelObj->get('path');
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

}
