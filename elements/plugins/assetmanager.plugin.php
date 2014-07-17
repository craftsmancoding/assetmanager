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
        $classes = json_decode($modx->getOption('assman.class_keys'),true);
        // New Resource?
        if (empty($resource)) {
            $class_key = (isset($_GET['class_key'])) ? $_GET['class_key'] : 'modDocument';
            $page_id = 0;
        } 
        else {
            $class_key = $resource->get('class_key');    
            $page_id = $resource->get('id');
        }

        if (in_array($class_key,$classes)) {
            $Page = new \Assman\PageController($modx);
            $Page->getPageAssetsTab(array('page_id'=>$page_id,'_nolayout'=>true));
        }
        break;
        
    case 'OnDocFormSave':
        $modx->log(modX::LOG_LEVEL_DEBUG,'','','asset Manager Plugin:OnDocFormSave');
        if ($pageassets = $resource->get('PageAssets')) {
            $A = $modx->newObject('Asset');
            $data = $A->indexedToRecordset($pageassets);
            $modx->log(modX::LOG_LEVEL_ERROR,print_r($data,true),'','Assman');
            $A->dictateRelations($data,$resource->get('id'));
        }
        break;
    }