<?php
class PageAsset extends xPDOSimpleObject {

    /** 
     * Make sure the relations actually exist.
     */
    public function save($cacheFlag= null) {

        if (!$Asset = $this->xpdo->getObject('Asset', $this->get('asset_id'))) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Invalid Asset ID: '.$this->get('asset_id'),'',__CLASS__);
            throw new \Exception('Invalid Asset ID specified for PageAsset');
        }
        if (!$Page = $this->xpdo->getObject('modResource', $this->get('page_id'))) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Invalid Page ID: '.$this->get('page_id'),'',__CLASS__);
            throw new \Exception('Invalid Page ID specified for PageAsset');
        }
        
        return parent::save($cacheFlag);
    }
    
}