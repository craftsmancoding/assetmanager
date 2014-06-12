<?php
/**
 * @name resize
 * @description Custom output filter for resizing an image asset by its asset_id. Pass a single parameter specifying {width}x{height}.
 *
 * USAGE:
 *
 * Apply this filter to the raw asset_id to manipulate the URL inside a product page or chunks that 
 * format assets.
 *
 * <img src="[[+asset_id:resize=`500x300`]]" width="500" width="300" />
 *
 * @package assetmanager
 */
$modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n".print_r($scriptProperties,true),'','Snippet secure');

$core_path = $modx->getOption('assetmanager.core_path', null, MODX_CORE_PATH.'components/assetmanager/');
require_once $core_path .'vendor/autoload.php';

$asset_id = $input; 
// e.g. 500x300
if (preg_match('/^(\d+)x(\d+)$/',$options,$m)) {
    $w = $m[1];
    $h = $m[2];
}
else {
    $this->modx->log(\modX::LOG_LEVEL_ERROR,'Invalid image dimensions passed: '.$options,'','resize Output Filer');
    $w = $modx->getOption('assetmanager.thumbnail_width');
    $h = $modx->getOption('assetmanager.thumbnail_height');
}

if (!$Asset = $modx->getObject('Asset', array('asset_id' => $asset_id))) {
    $this->modx->log(\modX::LOG_LEVEL_ERROR,'Asset not found.','','resize Output Filer');
    return \Moxycart\Asset::getMissingThumbnail($w,$h);
}

$path = $Asset->get('path');

$dst = \Moxycart\Image::thumbnail($src,$dst,$w,$h)
/*EOF*/