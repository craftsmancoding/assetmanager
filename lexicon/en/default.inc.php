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
$_lang['setting_assman.library_path_desc'] = "Set a path relative to MODX_ASSETS_PATH where your media assets will be stored. This should be a dedicated directory that can be fully under Asset Manager's control.";
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

