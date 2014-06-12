<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package assman 
 */
namespace Assman;
class ErrorController extends BaseController {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false; // GFD... this can't be set at runtime.
    
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function get404(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        //$this->addStandardLayout($scriptProperties);
                $this->scriptProperties['_nolayout'] = true;
        $this->setPlaceholders($scriptProperties);    
        return $this->fetchTemplate('error/404.php');
    }
}
/*EOF*/