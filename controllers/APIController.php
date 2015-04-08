<?php
/**
 * Handles JSON API within MODX manager. Responses follow Jsend suggested format:
 *
 * http://labs.omniti.com/labs/jsend
 *
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package assman 
 */
namespace Assman;
require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class APIController extends \modExtraManagerController {

    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false;
    public $templatesPaths = array();      
    public $model;

    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        header('Content-Type: application/json');
    }

    /**
     * Catch all for bad function requests -- our 404
     */
    public function __call($name,$args) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'[assman] Invalid function name '.$name. ' '.print_r($args,true),'',__CLASS__);
        header('HTTP/1.0 404 Not Found');
        return $this->sendError('Invalid API method: '.__CLASS__.'::'.$name);
    }
        
    /** 
     * Send JSON Success response
     * @param mixed $data you want to return, e.g. a record or recordset
     */
    public function sendSuccess($data) {
        $out = array(
            'status' => 'success',
            'data' => $data,
        );
        return json_encode($out);
    }
    
    /** 
     * Send JSON fail response
     * @param mixed $data you want to return, e.g. a record or recordset
     */    
    public function sendFail($data) {
        $out = array(
            'status' => 'fail',
            'data' => $data,
        );
        return json_encode($out);    
    }
    
    /**
     * Send JSON error response. More serious than a fail, e.g. 
     * When an exception is throw.
     */
    public function sendError($message,$code=null, $data=null) {
        $out = array(
            'status' => 'error',
            'message' => $message,
        );
        if ($code) $out['code'] = $code;
        if ($data) $out['data'] = $data;
        return json_encode($out);    
    }

    /**
     * 
     */
    public function postCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        $Model = $this->modx->newObject('Asset');
        $Model->fromArray($scriptProperties);
        if (!$Model->save()) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'API: failed to create '.$this->model.' due to errors: '.print_r($Model->errors,true),'',__CLASS__,__FUNCTION__,__LINE__);
            return $this->sendFail(array('errors'=> $Model->errors));
        }
        return $this->sendSuccess(array(
            'msg' => sprintf('%s created successfully.',$this->model)
        ));
    }

    /**
     * 
     */
    public function postDelete(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);

        $id = (int) $this->modx->getOption('asset_id',$scriptProperties);

        if (!$Obj = $this->modx->getObject('Asset', $id)) {
            return $this->sendFail(array('msg'=>sprintf('%s not found', $this->model)));
        }

        if (!$Obj->remove()) {
            return $this->sendFail(array('errors'=> $Model->errors));
        }
        return $this->sendSuccess(array(
            'msg' => sprintf('%s deleted successfully.',$this->model)
        ));
    }

    /**
     *
     */
    public function postEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);

        $id = (int) $this->modx->getOption('asset_id',$scriptProperties);

        if (!$Obj = $this->modx->getObject('Asset', $id)) {
            return $this->sendFail(array('msg'=>'Asset not found'));
        }
        $Obj->fromArray($scriptProperties);
        if (!$Obj->save()) {
            return $this->fail(array('errors'=> $Obj->errors));
        }
        return $this->sendSuccess(array(
            'msg' => sprintf('%s updated successfully.',$this->model)
        ));
    }

    /**
     * Used by autocomplete. Default limit is 25 terms
     * http://www.pontikis.net/blog/jquery-ui-autocomplete-step-by-step
     *
     * results should be an array with id, value, label keys
     *
     * data will look like this:
     *     "results":[{"id":"1","value":"2","label":"My Product"},...]
     */
    public function postSearch(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        $Model = $this->modx->newObject('Asset'); 

        $scriptProperties['limit'] = $this->modx->getOption('limit',$scriptProperties,25);
        //$results = $Model->all(array('name:like'=>'shirt','limit'=>25));
        if (!$results = $Model->all($scriptProperties)) {
            return $this->sendFail(array(
                'msg'=>sprintf('%s not found', $this->model),
                'params' => print_r($scriptProperties,true)
            ));
        }
        $data = array();
        foreach ($results as $r) {
            $data[] = array(
                'id' => $r->getPrimaryKey(),
                'value' => $r->get('name'),
                'label' => strip_tags(sprintf('%s (%s)',$r->get('name'),$r->get('sku')))
            );
        }
        return $this->sendSuccess(array('results' => $data));
    }





    /**
     * This is what ultimately responds to a manager request and send a JSON response
     *
     * We override this so we can route to functions other than the simple "process"
     * 
     * There are 2 class vars important here:
     *
     *      $this->scriptProperties : contains all request data
     *      $this->config : set in our constructor. Contains "method"
     *
     * @return string
     */
    public function render() {
        if (!$this->checkPermissions()) {
            return $this->modx->error->failure($this->modx->lexicon('access_denied'));
        }

        // This routing comes from the index.class.php
        $method = $this->config['method'];
        $props = $this->scriptProperties;
        unset($props['a']);
        unset($props['class']);
        unset($props['method']);
        return $this->$method($props);
    }


}
/*EOF*/