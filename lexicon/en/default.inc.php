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
$_lang['setting_assman.thumbnail_width_desc'] = 'Used when calculating thumbnails when assets are first uploaded via the Asset Manager';
$_lang['setting_assman.thumbnail_height'] = 'Thumbnail Height';
$_lang['setting_assman.thumbnail_height_desc'] = 'Used when calculating thumbnails when assets are first uploaded via the Asset Manager';

$_lang['setting_assman.groups'] = 'Asset Groups';
$_lang['setting_assman.groups_desc'] = 'JSON array of names, used to group assets in the manager and in the front end.';


$_lang['setting_assman.class_keys'] = 'Class Keys';
$_lang['setting_assman.class_keys_desc'] = 'JSON array listing class names of MODX resources that will get the "Assets" tab added. Default is modDocument. Other possible values include modStaticResource, modSymLink, modWebLink, and any custom resource classes you may have defined.';

$_lang['setting_assman.autocreate_content_type'] = 'Auto-Create Content Types';
$_lang['setting_assman.autocreate_content_type_desc'] = 'If you upload a content type that is not defined in the content_type table, the Asset Manager will attempt to intelligently create it based on the file extension and detected mime-type. If you disable this setting, uploads may fail when a user tries to upload an allowed file type (via the upload_files setting), but no corresponding content type is defined.';


//---------------------------------------
// CMP label
//---------------------------------------
$_lang['assman.lbl.thumbnail'] = 'THUMBNAIL';
$_lang['assman.lbl.title'] = 'TITLE';
$_lang['assman.lbl.alt'] = 'ALT';
$_lang['assman.lbl.size'] = 'SIZE';
$_lang['assman.lbl.action'] = 'ACTION';

//---------------------------------------
// CMP Buttons
//---------------------------------------
$_lang['assman.btn.search'] = 'Search';
$_lang['assman.btn.showall'] = 'Show All';
$_lang['assman.btn.edit'] = 'Edit';
$_lang['assman.btn.delete'] = 'Delete';


//---------------------------------------
// CM Pages Menu
//---------------------------------------
$_lang['assman.menu.manage'] = 'Manage';
$_lang['assman.menu.library'] = 'Library';
$_lang['assman.menu.settings'] = 'Settings';
$_lang['assman.menu.groups'] = 'Groups';
$_lang['assman.menu.donation'] = 'Make a Donation';
$_lang['assman.menu.bug'] = 'Report a Bug';
$_lang['assman.menu.wiki'] = 'Wiki';
$_lang['assman.menu.support'] = 'Get Paid Support';

//---------------------------------------
// CM Pages and other content related text
//---------------------------------------
$_lang['assman.index.pagetitle'] = 'Welcome to the MODX Asset Manager';
$_lang['assman.index.subtitle'] = 'Stay tuned. more tools and stuff will be here in future versions.';
$_lang['assman.assets.pagetitle'] = 'Manage Assets';
$_lang['assman.assets.subtitle'] = 'Browse your asset library..';
$_lang['assman.settings.pagetitle'] = 'Asset Manager Settings';
$_lang['assman.groups.pagetitle'] = 'Asset Groups';
$_lang['assman.groups.subtitle'] = 'Use groups to help organize your assets within a page. Each asset may belong to only one group for page. If an asset is used on multiple pages, it may belong to a different group on each page where it is used.';
