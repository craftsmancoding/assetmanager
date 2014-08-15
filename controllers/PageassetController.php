<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package assman
 */
namespace Assman;
class PageassetController extends APIController {

    public $model = 'PageAsset'; 
        
 
    /** 
     * Delete the asset and remove the association
     */
    public function postDelete(array $scriptProperties = array()) {    

        $page_id = (int) $this->modx->getOption('page_id', $scriptProperties);
        $asset_id = (int) $this->modx->getOption('asset_id', $scriptProperties);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, print_r($scriptProperties,true),'','Assman PageassetController:'.__FUNCTION__);
        
        // We must delete these separately because the relationship is not defined 
        if ($PA = $this->modx->getObject('PageAsset', array('page_id'=> $page_id, 'asset_id'=>$asset_id))) {
            $PA->remove();
        }
        else {
            // You may end up here if you attempt to delete an asset before the page has been saved.  I.e. the asset exists,
            // but not the PageAsset pivot record.  But we do not consider this condition an error.
            // return $this->sendFail(array('msg'=>'Record not found for page_id '.$page_id .' and asset_id '.$asset_id));        
        }

        if (!$Asset = $this->modx->getObject('Asset', $asset_id)) {
            return $this->sendFail(array('msg'=>'Record not found for asset_id '.$asset_id));
        }
        $Asset->remove();

        return $this->sendSuccess(array(
            'msg' => 'Asset Deleted successfully'
        ));
        
    }


    /** 
     * Remove only the association
     */
    public function postRemove(array $scriptProperties = array()) {    

        $page_id = (int) $this->modx->getOption('page_id', $scriptProperties);
        $asset_id = (int) $this->modx->getOption('asset_id', $scriptProperties);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, print_r($scriptProperties,true),'','PageassetController::'.__FUNCTION__);
        
        // We must delete these separately because the relationship is not defined 
        if ($PA = $this->modx->getObject('PageAsset', array('page_id'=> $page_id, 'asset_id'=>$asset_id))) {
            $PA->remove();
        }
        else {
            // You may end up here if you attempt to delete an asset before the product has been saved.  I.e. the asset exists,
            // but not the PageAsset pivot record.  But we're not gonna could this condition as an error though.
            // return $this->sendFail(array('msg'=>'Record not found for page_id '.$page_id .' and asset_id '.$asset_id));        
        }

        return $this->sendSuccess(array(
            'msg' => 'Asset removed from product successfully'
        ));
        
    }
        
}
/*EOF*/