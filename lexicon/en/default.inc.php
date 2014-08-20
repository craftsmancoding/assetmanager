<?php
/**
 * en default topic lexicon file for moxycart extra
 *
 * Copyright 2013 by Everett Griffiths everett@craftsmancoding.com
 * Created on 07-05-2013
 *
 * assman is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * assman is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * moxycart; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package assman
 */

$_lang['assman'] = 'Asset Manager'; 
$_lang['assman_desc'] = 'Media Library';
$_lang['assets_tab'] = 'Assets'; 

//---------------------------------------
// System Settings
// Name: setting_ + Key
// Description: setting_ + Key + _desc
//---------------------------------------
$_lang['setting_assman.library_path'] = 'Library Path';
$_lang['setting_assman.library_path_desc'] = "Set a path relative to MODX_BASE_PATH where your media assets will be stored. This directory will be fully under Asset Manager's control and off-limits to manual uploads and other hanky-panky.";
$_lang['setting_assman.url_override'] = 'URL Override';
$_lang['setting_assman.url_override_desc'] = 'If checked, the "Asset Manager Site URL" will be used instead of the MODX_ASSET_URL when calculating asset URLs. Check this if your Asset Manager URL needs to a custom sub-domain or port to serve your images/assets.';
$_lang['setting_assman.site_url'] = 'Asset Manager Site URL';
$_lang['setting_assman.site_url_desc'] = 'If "URL Override" is checked, this URL will be used instead of MODX_ASSET_URL when calculating URLs.';
$_lang['setting_assman.thumbnail_width'] = 'Thumbnail Width';
$_lang['setting_assman.thumbnail_width_desc'] = 'Used when displaying thumbnails in the manager.';
$_lang['setting_assman.thumbnail_height'] = 'Thumbnail Height';
$_lang['setting_assman.thumbnail_height_desc'] = 'Used when displaying thumbnails in the manager.';

$_lang['setting_assman.groups'] = 'Asset Groups';
$_lang['setting_assman.groups_desc'] = 'JSON array of names, used to group assets on each page.';


$_lang['setting_assman.class_keys'] = 'Class Keys';
$_lang['setting_assman.class_keys_desc'] = 'JSON array listing class names of MODX resources that will get the "Assets" tab added. Default is modDocument. Other possible values include modStaticResource, modSymLink, modWebLink, and any custom resource classes you may have defined.';

$_lang['setting_assman.autocreate_content_type'] = 'Auto-Create Content Types';
$_lang['setting_assman.autocreate_content_type_desc'] = 'If you upload a content type that is not defined in your <code>modx_content_type</code> table, the Asset Manager will attempt to intelligently create a new content type based on the detected mime-type of the uploaded file. If you disable this setting, uploads may fail when a user tries to upload a files whose content types have not been defined.';


//---------------------------------------
// CMP label
//---------------------------------------
$_lang['assman.lbl.thumbnail'] = 'Thumbnail';
$_lang['assman.lbl.title'] = 'Title';
$_lang['assman.lbl.alt'] = 'Alt';
$_lang['assman.lbl.size'] = 'Size';
$_lang['assman.lbl.action'] = 'Action';
$_lang['assman.lbl.thumbwidth'] = 'Thumbnail Width';
$_lang['assman.lbl.thumbheight'] = 'Thumbnail height';
$_lang['assman.lbl.classkeys'] = 'Class Keys';
$_lang['assman.lbl.librarypath'] = 'Library Path';
$_lang['assman.lbl.override'] = 'URL Override';
$_lang['assman.lbl.autocreate'] = 'Auto Create';

//---------------------------------------
// CMP Buttons
//---------------------------------------
$_lang['assman.btn.save'] = 'Save';
$_lang['assman.btn.cancel'] = 'Cancel';
$_lang['assman.btn.search'] = 'Search';
$_lang['assman.btn.showall'] = 'Show All';
$_lang['assman.btn.edit'] = 'Edit';
$_lang['assman.btn.delete'] = 'Delete';
$_lang['assman.btn.updatesettings'] = 'Update Settings';
$_lang['assman.btn.seesettings'] = 'See all Settings';
$_lang['assman.btn.addgroup'] = 'Add Group';


//---------------------------------------
// CM Pages Menu
//---------------------------------------
$_lang['assman.menu.manage'] = 'Manage';
$_lang['assman.menu.library'] = 'Library';
$_lang['assman.menu.settings'] = 'Settings';
$_lang['assman.menu.groups'] = 'Groups';
$_lang['assman.menu.verify'] = 'Verify';
$_lang['assman.menu.donation'] = 'Make a Donation';
$_lang['assman.menu.bug'] = 'Report a Bug';
$_lang['assman.menu.wiki'] = 'Wiki';
$_lang['assman.menu.support'] = 'Get Paid Support';

//---------------------------------------
// CM Pages and other content related text
//---------------------------------------
$_lang['assman.index.pagetitle'] = 'Welcome to the MODX Asset Manager';
$_lang['assman.index.subtitle'] = 'Stay tuned. more tools and stuff will be here in future versions.';
$_lang['assman.products.subtitle'] = 'Here you can Manage Products, Add Edit and Preview';
$_lang['assman.verify.pagetitle'] = 'Verify Assets';
$_lang['assman.verify.subtitle'] = 'Your filesystem and your database must be in sync in order for the Asset Manager to work. Use this page to verify your site.';

$_lang['assman.assets.pagetitle'] = 'Manage Assets';
$_lang['assman.assets.subtitle'] = 'Browse your asset library.';
$_lang['assman.settings.pagetitle'] = 'Asset Manager Settings';
$_lang['assman.groups.pagetitle'] = 'Asset Groups';
$_lang['assman.groups.subtitle'] = 'Use groups to help organize your assets within a page. Each asset may belong to only one group per page. If an asset is used on multiple pages, it may belong to a different group on each page where it is used.';

$_lang['assman.settings.thumbnail.title'] = 'Thumbnail Dimensions';
$_lang['assman.settings.thumbnail.desc'] = 'These settings affect the size of thumbnails displayed on the "Assets" tab.';
$_lang['assman.settings.resourcetype.title'] = 'Resource Types';
$_lang['assman.settings.resourcetype.desc'] = 'The Asset Manager can be attached to any valid MODX Resource Class, e.g. <code>modWebLink</code>, <code>modSymLink</code>, <code>modStaticResource</code>, and <code>modDocument</code> (the default).  List these as a JSON array.';
$_lang['assman.settings.storage.title'] = 'Storage';
$_lang['assman.settings.storage.path.desc'] = 'Several settings relate to where the Asset Manager stores its files and how they are accessed.';
$_lang['assman.settings.storage.path.note'] = 'Relative to the <code>MODX_ASSETS_URL</code>';
$_lang['assman.settings.storage.override.desc'] = 'Normally, asset URLS are calculated as <code>MODX_ASSETS_URL</code> + <code>assman.library_path</code> + Asset stub.  If URL-override is enabled, the URLS are calculated as <code>assman.site_url</code> + <code>assman.library_path</code> + Asset stub.';
$_lang['assman.settings.storage.override.note'] = 'Used to calculate URLs When the URL Override is checked.';
$_lang['assman.settings.contenttype.desc'] = 'By default, the Asset Manager will detect the MIME types of uploaded files and create a <code>modContentType</code> object for the detected content type.  This behavior saves a lot of time, but you may disable if you need to customize the specifics of your upload types.';
$_lang['assman.settings.contenttype.title'] = 'Content Types';

$_lang['assman.no_results'] = 'No results.';

//---------------------------------------
// Assets Tab
//---------------------------------------
$_lang['assman.assettab.drop'] = 'Drop files here to upload or';