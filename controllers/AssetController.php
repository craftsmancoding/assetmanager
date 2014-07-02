<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package assman
 */
namespace Assman;
class AssetController extends APIController {

    public $model = 'Asset';

    /**
     * $_FILES
     *
        Array
        (
            [file] => Array
                (
                    [name] => ext_js_firebug.jpg
                    [type] => image/jpeg
                    [tmp_name] => /Applications/MAMP/tmp/php/phpNpESmV
                    [error] => 0
                    [size] => 81367
                )
        
        )     
     */
    public function postCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API $_FILES: '.print_r($_FILES,true),'',__CLASS__,__FUNCTION__,__LINE__);
        $fieldname = $this->modx->getOption('fieldname', $scriptProperties,'file');
        $page_id = $this->modx->getOption('page_id', $scriptProperties); // Optionally associate it with a product

        // Error checking
        if (empty($_FILES)) {
            return $this->sendFail(array('errors'=> 'No FILE data detected.'));
        }
        if (!isset($_FILES[$fieldname])){
            return $this->sendFail(array('errors'=> 'FILE data empty for field: '.$fieldname));
        }
        if (!empty($_FILES[$fieldname]['error'])) {
            return $this->sendFail(array('errors'=> 'Error uploading file: '.$_FILES[$filename]['error']));
        }        
        
        try {
            $Model = $this->modx->newObject('Asset');
            $Asset = $Model->fromFile($_FILES[$fieldname]);
            
            if ($Asset->save()) {
                return $this->sendSuccess(array(
                    'msg' => sprintf('%s created successfully.',$this->model),
                    'class' => $this->model,
                    'fields' => $Asset->toArray()
                ));
            }
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Error saving Asset '.print_r($_FILES[$fieldname],true).' '.print_r($Model->errors,true),'',__CLASS__,__FUNCTION__,__LINE__);            
            return $this->sendFail(array('errors'=> $Model->errors));      
        }
        catch (\Exception $e) {
            return $this->sendFail(array('msg'=> $e->getMessage()));    
        }  
        
    }

}
/*EOF*/