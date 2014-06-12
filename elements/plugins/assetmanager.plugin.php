<?php
/**
 * @name Asset Manager
 * @description Multi-purpose plugin for Asset Manager handling manager customizations
 * @PluginEvents OnManagerPageInit,OnDocFormPrerender,OnDocFormSave
 *
 */
 
$core_path = $modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
require_once $core_path .'vendor/autoload.php';

switch ($modx->event->name) {

    //------------------------------------------------------------------------------
    //! OnManagerPageInit
    //  Load up custom CSS for the manager
    //------------------------------------------------------------------------------
    case 'OnManagerPageInit':
        $assets_url = $modx->getOption('assman.assets_url', null, MODX_ASSETS_URL.'components/assman/');
        $modx->log(modX::LOG_LEVEL_DEBUG,'Registering '.$assets_url.'css/mgr.css','','Asset Manager Plugin:OnManagerPageInit');
        $modx->regClientCSS($assets_url.'css/mgr.css');
        break;
        

    //------------------------------------------------------------------------------
    //! OnDocFormPrerender
    // Add a custom tab to the resource panel for resource types OTHER THAN Taxonomy
    // and Terms (no sense in categorizing categories). 
    // We have to use $_GET to read the class_key because it's otherwise not avail.
    // Remember: $resource will be null for new Resources!
    //------------------------------------------------------------------------------
    case 'OnDocFormPrerender':
        $modx->log(modX::LOG_LEVEL_DEBUG,'Getting Test','taxonomies Plugin:OnDocFormPrerender');
        $classes = json_decode($modx->getOption('assman.class_keys'),true);

        if (empty($resource)) {
            $class_key = (isset($_GET['class_key'])) ? $_GET['class_key'] : 'modDocument';            
        } 
        else {
            $class_key = $resource->get('class_key');    
        }

        if (in_array($class_key,$classes)) {
            //$T = new \Assman\Base($modx);
            //$form = $T->getForm($id);
            $Page = new \Assman\PageController($modx);
            $url = $Page::url('page','pageassets',array('_nolayout'=>true));
//            print $url; exit;
//            $form = $Page->getPageAssets();
//            $form = '<p>Hello there</p>';
            $modx->lexicon->load('assman:default');
            $title = $modx->lexicon('assets_tab');

            $modx->regClientStartupHTMLBlock('<script type="text/javascript">
                MODx.on("ready",function() {
                    console.log("[assman] Ajax Requesting URL: '.$url.'");
                    MODx.addTab("modx-resource-tabs",{
                        title: '.json_encode($title).',
                        id: "assets-resource-tab",
                        width: "95%",
                        //html: '.json_encode(utf8_encode("$form")).',
                        autoLoad: {
                            url : "'.$url.'",
                            scripts : true
                        }
                    });
                });                
            </script>');
        }
        break;
        
    case 'OnDocFormSave':
        $modx->log(modX::LOG_LEVEL_DEBUG,'','','taxonomies Plugin:OnDocFormSave');
/*
        if ($terms = $resource->get('terms')) {
            $T = new \Taxonomies\Base($modx);
            $T->dictatePageTerms($resource->get('id'), $terms);
        }
*/
        break;}