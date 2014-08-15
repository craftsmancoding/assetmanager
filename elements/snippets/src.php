<?php
/**
 * @name src
 * @description Returns the URL to the asset (specified by asset_id). This may also be used as an Output Filter.
 *
 * This Snippet is intended as a simpler alternative to the Asset Snippet. 
 * The Asset Snippet offers more functionality because it can return all of an asset's properties,
 * whereas the src Snippet returns ONLY the URL (i.e. the src).
 *
 * All parameters are optional, but for image assets, you can pass &height and/or &width parameters to trigger resizing.
 *
 * If no &height or &width arguments are passed, then no resizing takes place: return the full size of the original asset image.
 * If the snippet call sets a &width but no &height, then return a scaled version of the asset scaled to the desired width.
 * If the snippet call sets a &height but no &width, then return a scaled version of the asset scaled to the desired height.
 * If both &height and &width are set, then do what the "scale" Snippet does and scale asset to the desired dimensions
 * 
 * USAGE EXAMPLES
 *      <img src="[[src? &asset_id=`123`]]"/>
 *      <a href="[[src? &asset_id=`123`]]">Download<a/>
 *
 * AS AN OUTPUT FILTER
 *  
 * This can only be used to format an asset_id in the same ways and places as the scale2h and scale2w Snippets, e.g.
 * in the innerTpl of the getPageAssets Snippet:
 *      [[getPageAssets? &innerTpl=`myChunk`]]
 *
 * myChunk:
 *  [[+Asset.title]] <a href="[[+asset_id:src]]">View</a>
 * or:
 *  [[+Asset.title]] <img src="[[+asset_id:src=`300x200`]]" width="300" height="200"/>
 *    
 *
 * Parameters
 * -----------------------------
 * @param integer &asset_id (required)
 * @param integer &width in pixels (optional)
 * @param integer &height in pixels (optional)
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
$Snippet->log('src',$scriptProperties);


$asset_id = (int) $modx->getOption('asset_id', $scriptProperties);
$width = (int) $modx->getOption('width', $scriptProperties);
$height = (int) $modx->getOption('height', $scriptProperties);

// called as output filter?
if (isset($input) && isset($options)) {
    $asset_id = (int) $input;
    if (strpos($options, 'x') !== false) {
        list($width, $height) = explode('x',$options);
    }
}

$Asset = $modx->getObject('Asset', $asset_id);

if(!$Asset) {
	$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','src',__LINE__);
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
        return $Asset->getResizedImage($Asset->get('path'), $asset_id,$nx,$ny);
	}

	if( $height == 0 && $width > 0 ) {
		// Calculate the new dimensions
		$nx = $width;
		$ny = floor($width * ($Asset->get('height') / $Asset->get('width')));
        return $Asset->getResizedImage($Asset->get('path'), $asset_id,$nx,$ny);
	}

	if( $height > 0 && $width > 0 ) {
		return $Asset->getResizedImage($Asset->get('path'), $asset_id,$width,$height);
	}
}

return $Asset->get('url');