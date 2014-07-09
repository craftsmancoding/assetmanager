<?php
/**
 * @name Asset
 * @description Returns a single asset
 *
 *
 * If no &height or &width arguments are passed, then no resizing takes place: return the full size of the original asset image.
 * If the snippet call sets a &width but no &height, then return a scaled version of the asset scaled to the desired width.
 * If the snippet call sets a &height but no &width, then return a scaled version of the asset scaled to the desired height.
 * If both &height and &width are set, then do what the "scale" Snippet does and scale asset to the desired dimensions
 * 
 * USAGE EXAMPLES
 * [[Asset? &asset_id=`123` &tpl=`<img src="[[+url]]"/>` &height=`100` &width=`100` ]]
 * [[Asset? &asset_id=`123` &tpl=`<a href="[[+url]]"><img src="[[+thumbnail_url]]"/></a>` &height=`100` &width=`100` ]]
 *
 * USAGE EXAMPLES FOR NON-IMAGES ASSET
 * width and height param doesnt work on non-images asset
 * [[Asset? &asset_id=`123` &tpl=`<img src="[[+thumbnail_url]]"/>` ]]
 * [[Asset? &asset_id=`123` &tpl=`<a href="[[+url]]"><img src="[[+thumbnail_url]]"/></a>` ]]
 *
 * Parameters
 * -----------------------------
 * @param integer &asset_id 
 * @param integer &width in pixels
 * @param integer &height in pixels
 * @param string &tpl either a MODX chunk or a formatting string
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package assman
 */
$core_path = $modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Assman\Snippet($modx);
$Snippet->log('Asset',$scriptProperties);


$asset_id = (int) $modx->getOption('asset_id', $scriptProperties);
$width = (int) $modx->getOption('width', $scriptProperties);
$height = (int) $modx->getOption('height', $scriptProperties);
$tpl = $modx->getOption('tpl', $scriptProperties, '<img src="[[+url]]" width="[[+width]]" height="[[+height]]" alt="[[+alt]]" />');

$Asset = $modx->getObject('Asset', $asset_id);

if(!$Asset) {
	$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','Asset',__LINE__);
	return;
}


$ass_props = array(
	'asset_id'	=> $Asset->get('asset_id'),
	'title'	=> $Asset->get('title'),
	'alt'	=> $Asset->get('alt'),
	'width'	=> $Asset->get('width'),
	'height'	=> $Asset->get('height'),
	'thumbnail_url'	=> $Asset->get('thumbnail_url'),
	'url'	=> $Asset->get('url')
);

if($Asset->is_image) {

	if( $width == 0 && $height > 0 ) {
		// Calculate the new dimensions
		$nx = floor($height * ( $Asset->get('width') / $Asset->get('height') ));
		$ny = $height;
		$url = $Asset->getResizedImage($Asset->get('path'), $asset_id,$nx,$ny);
		$url = explode('/', $url);
		unset($url[0]);
		unset($url[1]);
		unset($url[2]);
		$ass_props['url'] = '/'.implode('/', $url);
		$ass_props['width'] = $nx;
		$ass_props['height'] = $ny;
	}

	if( $height == 0 && $width > 0 ) {
		// Calculate the new dimensions
		$nx = $width;
		$ny = floor($width * ($Asset->get('height') / $Asset->get('width')));
		$url = $Asset->getResizedImage($Asset->get('path'), $asset_id,$nx,$ny);
		$url = explode('/', $url);
		unset($url[0]);
		unset($url[1]);
		unset($url[2]);
		$ass_props['url'] = '/'.implode('/', $url);
		$ass_props['width'] = $nx;
		$ass_props['height'] = $ny;
	}

	if( $height > 0 && $width > 0 ) {
		$url = $Asset->getResizedImage($Asset->get('path'), $asset_id,$width,$height);
		$url = explode('/', $url);
		unset($url[0]);
		unset($url[1]);
		unset($url[2]);
		$ass_props['url'] = '/'.implode('/', $url);
		$ass_props['width'] = $width;
		$ass_props['height'] = $height;
	}
}

// Create the temporary chunk
$uniqid = uniqid();
$chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
$chunk->setCacheable(false);
 
$output = $chunk->process($ass_props, $tpl);

return $output;