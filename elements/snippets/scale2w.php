<?php
/**
 * @name scale2w
 * @description Custom output filter for resizing an image asset (by its asset_id) to a given width. Pass a single parameter specifying width. The height will be calculated to preserve the original aspect ratio.
 *
 * USAGE:
 *
 * Apply this filter to the raw asset_id to manipulate the URL inside a product page or chunks that 
 * format assets.
 *
 * We set a placeholder for the calculated height: [[+asset_id.height]]
 *
 * <img src="[[+asset_id:scale2w=`400`]]" width="400" height="[[+asset_id.height]]"/>
 *
 * @package assman
 */

$modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n".print_r($scriptProperties,true),'','Snippet scale2w');

$core_path = $modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
require_once $core_path .'vendor/autoload.php';

$asset_id = $input;
$new_w = $options;

if (!is_numeric($asset_id)) {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Invalid input. Integer asset ID required. '.$asset_id .print_r($scriptProperties,true),'','scale2w Output Filer');
    return;
} 

if (!is_numeric($new_w)) {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Invalid option. Integer width required. ' .print_r($scriptProperties,true),'','scale2w Output Filer');
    return;
}

if (!$Asset = $modx->getObject('Asset', array('asset_id' => $asset_id))) {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Asset not found: '. $asset_id,'','scale2w Output Filer');
    return $Asset->getMissingThumbnail($new_w,$new_w); // square.
}

// Calculate the new dimensions
// old XY (from src) to new XY
$ox = $Asset->get('width');
$oy = $Asset->get('height');
$nx = $new_w;
$ny = floor($new_w * ($oy / $ox));

$modx->log(\modX::LOG_LEVEL_INFO,'New asset dimensions calculated: '.$nx, $ny,'','scale2w Output Filer');

$modx->setPlaceholder('asset_id.height', $ny);

return $Asset->getThumbnailURL($nx, $ny);

/*EOF*/