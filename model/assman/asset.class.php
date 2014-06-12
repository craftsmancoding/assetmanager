<?php
class Asset extends xPDOObject {

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
                $w = $this->xpdo->getOption('assman.thumbnail_width');
                $h = $this->xpdo->getOption('assman.thumbnail_height');
                return \Assman\Asset::getMissingThumbnail($w,$h);
            }
            // Passthru if the user has set a full URL
            elseif(filter_var($raw, FILTER_VALIDATE_URL)) {
                return $raw;
            }

            return MODX_ASSETS_URL . $this->xpdo->getOption('assman.library_path').$raw;
        }

        return $raw;
    }

}