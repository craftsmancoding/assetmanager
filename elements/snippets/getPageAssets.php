<?php
/**
 * @name getPageAssets
 * @description Returns a list of images or other assets for the given page
 *
 * 
 * USAGE EXAMPLES
 *
 * You can use the resize output filter to display different sizes. 
 *
 *  [[getPageAssets? &innerTpl=`<li><img src="[[+asset_id:resize=`300x500`]]" width="300" height="500" alt="[[+Asset.alt]]" /></li>`]]
 *
 * If using the "resize" output filter, you MUST call the snippet cached! Otherwise the "resize" filter attempts to operate on the placeholder
 * before it's set!
 *
 * No results: use a MODX output filter:
 *
 *  [[getPageAssets:empty=`No images found`? ]]
 
 * Available Placeholders
 * ---------------------------------------
 * e.g. to format the original image: 
 *      <img src="[[+Asset.url]]" width="[[+Asset.width]]" height="[[+Asset.height]]" alt="[[+Asset.alt]]" />
 * or the standard Thumbnail:
 *      <img src="[[+Asset.thumbnail_url]]" width="[[+Asset.thumbnail_width]]" height="[[+Asset.thumbnail_height]]" alt="[[+Asset.alt]]" />
 *
 * If needed, include the System Settings (double ++) :
 *      [[++assman.thumbnail_width]]
 *      [[++assman.thumbnail_height]]
 * e.g. <img src="[[+Asset.thumbnail_url]]" width="[[++assman.thumbnail_width]]" height="[[++assman.thumbnail_width]]" alt="[[+Asset.alt]]"/>
 * 
 * 
 *
 *
 * Parameters
 * -----------------------------
 * @param integer $page_id of the page whose images you want. Defaults to the current page.
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param string $firstTpl Format the first Item of List (optional : defaults to innerTpl)
 * @param string $lastTpl Format the last Item of List (optional : defaults to innerTpl)
 * @param string $onOne which tpl to use if there is only 1 result: innerTpl, firstTpl, or lastTpl. Default: innerTpl
 * @param string $group optional: limit the results to the specified group
 * @param boolean $is_active Get all active records only
 * @param boolean $is_image if true, return only images, if false, only other assets. If not set, we get everything.
 * @param int $limit Limit the records to be shown (if set to 0, all records will be pulled)
 * @param string $sort which column should we sort by?  Default: Product.seq
 * @param string $dir which direction should results be returned?  ASC or DESC (optional)

 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * To get all Images on certain page
 * [[!getPageAssets? &page_id=`[[*id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &firstCLass=`first` &is_active=`1` &limit=`0`]]
 * [[!getPageAssets? &page_id=`[[*id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &is_active=`1` &limit=`1`]]
 *
 * @package assman
 */

$core_path = $modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Assman\Snippet($modx);
$Snippet->log('getProductImages',$scriptProperties);


// Formatting Arguments:
$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li><img src="[[+Asset.url]]" width="[[+Asset.width]]" height="[[+Asset.height]]" alt="[[+Asset.alt]]" /></li>');
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>');
$firstTpl = $modx->getOption('firstTpl', $scriptProperties, $innerTpl);
$lastTpl = $modx->getOption('lastTpl', $scriptProperties, $innerTpl);
$onOne = $modx->getOption('onOne', $scriptProperties, 'innerTpl');

$sort = $modx->getOption('sort', $scriptProperties, 'PageAsset.seq');
$dir = $modx->getOption('dir', $scriptProperties);

// Default Arguments:
$scriptProperties['is_active'] = (bool) $modx->getOption('is_active',$scriptProperties, 1);
$scriptProperties['limit'] = (int) $modx->getOption('limit',$scriptProperties, null);
$page_id = (int) $modx->getOption('page_id',$scriptProperties);

// Just being safe in case this is run without a resource in context
if (!$page_id) {
    if (isset($modx->resource) && is_object($modx->resource)) {
        $page_id = $modx->resource->get('id');
    }
}

if (!$page_id) {
    return 'Page ID is required.';
}

$criteria = array();
$criteria['page_id'] = $page_id;
$criteria['PageAsset.is_active'] = true;
if (isset($scriptProperties['is_image'])) {
    $criteria['Asset.is_image'] = (bool) $scriptProperties['is_image'];
}
if (isset($scriptProperties['group'])) {
    $criteria['PageAsset.group'] = $scriptProperties['group'];
}

$c = $modx->newQuery('PageAsset');
$c->where($criteria);
if ($sort && $dir) {
    $c->sortby($sort,$dir);
}
elseif($sort) {
    $c->sortby($sort);
}
if ($scriptProperties['limit']) {
    $c->limit($scriptProperties['limit']);
}
$cnt = $modx->getCount('PageAsset',$c);

$ProductAssets = $modx->getCollectionGraph('PageAsset','{"Asset":{}}', $c);

if ($ProductAssets) {
    return $Snippet->format($ProductAssets,$innerTpl,$outerTpl,$firstTpl,$lastTpl,$onOne,$cnt);    
}

$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getPageAssets',__LINE__);

return;