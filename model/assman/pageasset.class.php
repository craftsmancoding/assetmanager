<?php
class PageAsset extends xPDOSimpleObject {

    /**
     * Get a complete list of page assets, including all related info necessary to generate
     * the Assets tab in the MODX manager.  The format must be groomed for Javascript compatibility
     *
     * @param integer $page_id (MODX page id)
     * @return array
     */
    public function getAssets($page_id) {
        
        $c = $this->xpdo->newQuery('PageAsset');
        $c->where(array('PageAsset.page_id' => $page_id));
        $c->sortby('PageAsset.seq','ASC');
        $PA = $this->xpdo->getCollectionGraph('PageAsset','{"Asset":{}}',$c);
        $out = array();

        foreach ($PA as $p) {
            $out[] = $p->toArray('',false,false,true);
        }
        
        return $out;
    }

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