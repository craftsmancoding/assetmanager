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
 * @package assman
 */
$modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n".print_r($scriptProperties,true),'','Snippet resize');

$core_path = $modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
require_once $core_path .'vendor/autoload.php';

$asset_id = $input;

if (!is_numeric($asset_id)) {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Invalid input. Integer asset ID required. ' .print_r($scriptProperties,true),'','resize Output Filer');
    return;
} 
// e.g. 500x300
if (preg_match('/^(\d+)x(\d+)$/',$options,$m)) {
    $w = $m[1];
    $h = $m[2];
}
else {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Invalid image dimensions passed: '.$options,'','resize Output Filer');
    $w = $modx->getOption('assman.thumbnail_width');
    $h = $modx->getOption('assman.thumbnail_height');
}

if (!$Asset = $modx->getObject('Asset', array('asset_id' => $asset_id))) {
    $modx->log(\modX::LOG_LEVEL_ERROR,'Asset not found.','','resize Output Filer');
    return \Assman\Asset::getMissingThumbnail($w,$h);
}
$A = new \Assman\Asset($modx);
$url = $A->getThumbnailURL($Asset, $w, $h);

if ($modx->getOption('assman.url_override')) {
    return $modx->getOption('assman.site_url') . $modx->getOption('assman.library_path').$url;
}
else {
    return $modx->getOption('assets_url') . $modx->getOption('assman.library_path').$url;
}

/*EOF*/