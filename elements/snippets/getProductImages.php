<?php
/**
 * @name getProductImages
 * @description Returns a list of product_images.
 *
 * 
 * Available Placeholders
 * ---------------------------------------
 * e.g. to format the large image: 
 *      <img src="[[+Asset.url]]" width="[[+Asset.width]]" height="[[+Asset.height]]" alt="[[+Asset.alt]]" />
 * Thumbnail:
 *      <img src="[[+Asset.thumbnail_url]]" />
 *
 * If needed, include the System Settings (double ++) :
 *      [[++moxycart.thumbnail_width]]
 *      [[++moxycart.thumbnail_height]]
 * e.g. <img src="[[+Asset.thumbnail_url]]" width="[[++moxycart.thumbnail_width]]" height="[[++moxycart.thumbnail_width]]" alt="[[+Asset.alt]]"/>
 * 
 * Parameters
 * -----------------------------
 * @param integer $product_id of the product whose images you want. Defaults to the current product (if used in a product template)
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param boolean $is_active Get all active records only
 * @param int $limit Limit the records to be shown (if set to 0, all records will be pulled)
// * @param int $firstClass set CSS class name on the first item (Optional)
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * To get all Images on certain product
 * [[!getProductImages? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &firstCLass=`first` &is_active=`1` &limit=`0`]]
 * [[!getProductImages? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &is_active=`1` &limit=`1`]]
 *
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProductImages',$scriptProperties);


// Formatting Arguments:
$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li><img src="[[+Asset.url]]" width="[[+Asset.width]]" height="[[+Asset.height]]" alt="[[+Asset.alt]]" /></li>');
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>');

// Default Arguments:
$scriptProperties['is_active'] = (bool) $modx->getOption('is_active',$scriptProperties, 1);
$scriptProperties['limit'] = (int) $modx->getOption('limit',$scriptProperties, null);
$product_id = (int) $modx->getOption('product_id',$scriptProperties, $modx->getPlaceholder('product_id'));

if (!$product_id) {
    return 'product_id is required.';
}

$c = $modx->newQuery('ProductAsset');
$c->where(array(
    'ProductAsset.product_id'=>$product_id,
    'ProductAsset.is_active'=>true,
    'Asset.is_image' => true,
));
$c->sortby('ProductAsset.seq','ASC');
if ($scriptProperties['limit']) {
    $c->limit($scriptProperties['limit']);
}
$ProductAssets = $modx->getCollectionGraph('ProductAsset','{"Asset":{}}', $c);

if ($ProductAssets) {
    return $Snippet->format($ProductAssets,$innerTpl,$outerTpl);    
}

$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getProducts',__LINE__);

return 'No images found.';