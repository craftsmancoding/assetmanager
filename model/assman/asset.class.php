<?php
/**
 * The media library is organized thusly:
 *
 *  1. User uploads original full-sized assets, the Asset class organizes these files into
 *      the sub-folders in the library_path according to the date (yyyy/mm/dd).  This must 
 *      be done using the interface.
 *  2. Thumbnails or any resized images are stored inside a folder dedicated to a particular
 *      asset id: library_path/resized/{asset_id}/ 
 *  3. The thumbnail_url is normally not stored: it is calculated depending on the system settings for
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
 */
class Asset extends xPDOObject {

    /**
     * Override to provide calculated fields
     */
    public function __construct(xPDO & $xpdo) { 
        parent::__construct($xpdo);
        $this->_fields['thumbnail_width'] = $this->get('thumbnail_width');
        $this->_fields['thumbnail_height'] = $this->get('thumbnail_height');
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
            if ($this->xpdo->getOption('assman.url_override')) {
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
            

            if (empty($raw)) {
                $Asset = new \Assman\Asset($this->xpdo);
                $thumbnail_url = $Asset->getThumbnailURL($this);
                return $this->xpdo->getOption('assets_url') . $this->xpdo->getOption('assman.library_path').$thumbnail_url;
/*
                //$ext = strtolower(strrchr($this->get('url'), '.'));
                $w = $this->xpdo->getOption('assman.thumbnail_width');
                $h = $this->xpdo->getOption('assman.thumbnail_height');
                return \Assman\Asset::getMissingThumbnail($w,$h);
*/
            }
            // Passthru if the user has set a full URL
            elseif(filter_var($raw, FILTER_VALIDATE_URL)) {
                return $raw;
            }
            // relative URL (?) fallback
            return MODX_SITE_URL .$raw;
        }
        elseif ($k=='thumbnail_width') {
            return $this->xpdo->getOption('assman.thumbnail_width');
        }
        elseif ($k=='thumbnail_height') {
            return $this->xpdo->getOption('assman.thumbnail_height');
        }

        return $raw;

    }

}