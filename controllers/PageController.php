<?php
/**
 * This HTML controller is what generates HTML pages (as opposed to JSON responses
 * generated by the other controllers).  The reason is testability: most of the 
 * manager app can be tested by $scriptProperties in, JSON out.  The HTML pages
 * generated by this controller end up being static HTML pages (well... ideally, 
 * anyway). 
 *
 * See http://stackoverflow.com/questions/10941249/separate-rest-json-api-server-and-client
 *
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package assman
 */
namespace Assman;
class PageController extends BaseController {

    public $loadHeader = false;
    public $loadFooter = false;
    // GFD... this can't be set at runtime. See improvised addStandardLayout() function
    public $loadBaseJavascript = false; 
    // Stuff needed for interfacing with Assman API (mapi)
    public $client_config = array();
    
    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        static::$x =& $modx;

        $this->config['controller_url'] = self::url();
        $this->config['core_path'] = $this->modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
        $this->config['assets_url'] = $this->modx->getOption('assman.assets_url', null, MODX_ASSETS_URL.'components/assman/');
                
        $this->modx->regClientCSS($this->config['assets_url'] . 'css/mgr.css');
        $this->modx->regClientCSS($this->config['assets_url'] . 'css/dropzone.css');
        $this->modx->regClientCSS('//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
        $this->modx->regClientCSS($this->config['assets_url'].'css/colorbox.css');        
//        $this->modx->regClientCSS($this->config['assets_url'].'css/jquery-ui.css'); // smoothness        
//        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.min.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-ui.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/handlebars.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/dropzone.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/bootstrap.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/form2js.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.colorbox.js');                      
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/multisortable.js');  
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.quicksand.js');      
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/app.js');  

        
    }


    //------------------------------------------------------------------------------
    //! Assets
    //------------------------------------------------------------------------------
    /**
     * Asset management main page
     *
     * @param array $scriptProperties
     */
    public function getAssets(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Asset Manager PageController:'.__FUNCTION__);
        $A = $this->modx->newObject('Asset');
        $results = $A->all($scriptProperties);
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholder('pagetitle', $this->modx->lexicon('assman.assets.pagetitle'));
        $this->setPlaceholder('subtitle', $this->modx->lexicon('assman.assets.subtitle'));
        return $this->fetchTemplate('main/assets.php');
    }
    public function postAssets(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Asset Manager PageController:'.__FUNCTION__);
        $A = $this->modx->newObject('Asset');
        $results = $A->all($scriptProperties);
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholder('pagetitle', $this->modx->lexicon('assman.assets.pagetitle'));
        $this->setPlaceholder('subtitle', $this->modx->lexicon('assman.assets.subtitle'));
        return $this->fetchTemplate('main/assets.php');
    }
    
    //------------------------------------------------------------------------------
    //! Index
    //------------------------------------------------------------------------------
    /**
     * @param array $scriptProperties
     */
    public function getIndex(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Asset Manager PageController:'.__FUNCTION__);
        $this->setPlaceholder('pagetitle', $this->modx->lexicon('assman.index.pagetitle'));
        $this->setPlaceholder('subtitle', $this->modx->lexicon('assman.index.subtitle'));
        return $this->fetchTemplate('main/index.php');
    }

    //------------------------------------------------------------------------------
    //! Groups
    //------------------------------------------------------------------------------
    public function getGroups(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Asset Manager PageController:'.__FUNCTION__);
        $A = $this->modx->newObject('Asset');
        $this->config['Groups'] = $A->getAssetGroups();
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var assman = '.json_encode($this->config).';
        </script>');
        $this->setPlaceholder('pagetitle', $this->modx->lexicon('assman.groups.pagetitle'));
        $this->setPlaceholder('subtitle', $this->modx->lexicon('assman.groups.subtitle'));
        return $this->fetchTemplate('group/manage.php');
    }
    
    public function postGroups(array $scriptProperties = array()) {
        $groups = $this->modx->getOption('groups', $scriptProperties);
        $A = $this->modx->newObject('Asset');
        $A->setAssetGroups($groups);
        return $this->getGroups();
    }
    
    
    //------------------------------------------------------------------------------
    //! Settings
    //------------------------------------------------------------------------------
    /**
     * @param array $scriptProperties
     */
    public function getSettings(array $scriptProperties = array(),$msg='') {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Asset Manager PageController:'.__FUNCTION__);
        $this->setPlaceholder('msg', $msg);
        $this->setPlaceholder('library_path', $this->modx->getOption('assman.library_path'));
        $this->setPlaceholder('url_override', $this->modx->getOption('assman.url_override'));
        $this->setPlaceholder('site_url', $this->modx->getOption('assman.site_url'));
        $this->setPlaceholder('class_keys', $this->modx->getOption('assman.class_keys'));
        $this->setPlaceholder('thumbnail_width', $this->modx->getOption('assman.thumbnail_width'));
        $this->setPlaceholder('thumbnail_height', $this->modx->getOption('assman.thumbnail_height'));
        $this->setPlaceholder('autocreate_content_type', $this->modx->getOption('assman.autocreate_content_type'));
        $this->setPlaceholder('pagetitle', $this->modx->lexicon('assman.settings.pagetitle'));
        return $this->fetchTemplate('main/settings.php');
     
    }
    
    /**
     * Save the posted settings.
     *
     */
    public function postSettings(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Asset Manager PageController:'.__FUNCTION__);    
        //return '<pre>'.print_r($scriptProperties,true).'</pre>';
        $settings = array('library_path','url_override','site_url','class_keys','thumbnail_width',
            'thumbnail_height','autocreate_content_type');
        foreach ($settings as $s) {
            $value = $this->modx->getOption($s, $scriptProperties);
            if ($Setting = $this->modx->getObject('modSystemSetting', 'assman.'.$s)) {
                $Setting->set('value', $value);       
            }
            else {
                $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Could not load System Setting assman.'.$s,'','Asset::'.__FUNCTION__);                    
                continue;
            }
            if (!$Setting->save()) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Could not save System Setting','','Asset::'.__FUNCTION__);    
                continue;
            }
            $this->modx->setOption('assman.'.$s, $value);
        }
        // Clear cache
        $this->modx->cacheManager->refresh(array( 'system_settings' => array() ));
        return $this->getSettings(array(),'<div class="success">Your settings have been saved.</div>');

    }

    
    //------------------------------------------------------------------------------
    //! Page
    //------------------------------------------------------------------------------
    /**
     * Generates a tab for Ext JS editing a resource
     * @param array $scriptProperties
     */
    public function getPageAssetsTab(array $scriptProperties = array()) {

        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Asset Manager PageController:'.__FUNCTION__);
        $page_id = (int) $this->modx->getOption('page_id', $scriptProperties);
        $this->config['page_id'] = $page_id;
        $this->setPlaceholder('page_id', $page_id);
        $this->scriptProperties['_nolayout'] = true;
        
        $PA = $this->modx->newObject('PageAsset');
        $A = $this->modx->newObject('Asset');
        $this->config['PageAssets'] = $PA->getAssets($page_id);
        $this->config['Groups'] = $A->getAssetGroups();

        // Wedge the output into the tab
        $this->modx->lexicon->load('assman:default');
        $title = $this->modx->lexicon('assets_tab');

        $path = $this->modx->getOption('assman.core_path','', MODX_CORE_PATH.'components/assman/').'views/';
        if ($page_id) {
            $out = file_get_contents($path.'main/pageassets.tpl');
            $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
                var assman = '.json_encode($this->config).';
                var inited = 0;
                MODx.on("ready",function() {
                    console.log("[assman] on ready...");
                    MODx.addTab("modx-resource-tabs",{
                        title: '.json_encode(utf8_encode($title)).',
                        id: "assets-resource-tab",
                        width: "95%",
                        html: '.json_encode(utf8_encode($out)).'
                    });
                    if (inited==0) {
                        page_init();
                    }
                });                
            </script>');

        }
        else {
            $out = file_get_contents($path.'main/pageassets_new.tpl');
            $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
                var assman = '.json_encode($this->config).';
                var inited = 0;
                MODx.on("ready",function() {
                    console.log("[assman] on ready...");
                    MODx.addTab("modx-resource-tabs",{
                        title: '.json_encode(utf8_encode($title)).',
                        id: "assets-resource-tab",
                        width: "95%",
                        html: '.json_encode(utf8_encode($out)).'
                    });
                });                
            </script>');
        }


    }            
}
/*EOF*/