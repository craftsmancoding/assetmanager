<?php
/**
 * @name scale2h
 * @description Custom output filter for resizing an image asset (by its asset_id) to a given height. Pass a single parameter specifying height. The width will be calculated to preserve the original aspect ratio.
 *
 * USAGE:
 *
 * Apply this filter to the raw asset_id to manipulate the URL inside a product page or chunks that 
 * format assets.
 *
 * We set a placeholder for the calculated width: [[+asset_id.width]]
 *
 * <img src="[[+asset_id:scale2h=`300`]]" width="[[+asset_id.width]]" height="300"/>
 *
 * @package assman
 */

$modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n".print_r($scriptProperties,true),'','Snippet scale2h');

$core_path = $modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
require_once $core_path .'vendor/autoload.php';

$asset_id = $input;
$new_h = $options;

if (!is_numeric($asset_id)) {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Invalid input. Integer asset ID required. ' .print_r($scriptProperties,true),'','scale2h Output Filer');
    return;
} 

if (!is_numeric($new_h)) {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Invalid option. Integer height required. ' .print_r($scriptProperties,true),'','scale2h Output Filer');
    return;
}

if (!$Asset = $modx->getObject('Asset', array('asset_id' => $asset_id))) {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Asset not found.','','scale2h Output Filer');
    return \Moxycart\Asset::getMissingThumbnail($w,$h);
}

// Calculate the new dimensions
// old XY (from src) to new XY
$ox = $Asset->get('width');
$oy = $Asset->get('height');
$nx = floor($new_h * ( $ox / $oy ));
$ny = $new_h;
        
$modx->log(\modX::LOG_LEVEL_INFO,'New asset dimensions calculated: '.$nx, $ny,'','scale2h Output Filer');
$A = new \Assman\Asset($modx);
$url = $A->getThumbnailURL($Asset, $nx, $ny);
$modx->setPlaceholder('asset_id.width', $ny);
if ($modx->getOption('assman.url_override')) {
    return $modx->getOption('assman.site_url') . $modx->getOption('assman.library_path').$url;
}
else {
    return $modx->getOption('assets_url') . $modx->getOption('assman.library_path').$url;
}

/*EOF*/