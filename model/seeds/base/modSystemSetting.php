<?php
/*-----------------------------------------------------------------
For descriptions here, you must create some lexicon entries:
Name: setting_ + $key
Description: setting_ + $key + _desc
-----------------------------------------------------------------*/
return array(

    // Relative to MODX_ASSETS_PATH and MODX_ASSETS_URL
    // This is where all downloadable product assets will be stored.
    array(
        'key'  =>     'assman.library_path',
		'value'=>     'lib/',
		'xtype'=>     'textfield',
		'namespace' => 'assman',
		'area' => 'assman:default'
    ),
    // If true, the assman.site_url will be used to calculate an asset's URL.
    // If false, the MODX assets url is used. 
    array(
        'key'  =>     'assman.url_override',
		'value'=>     false,
		'xtype'=>     'combo-boolean',
		'namespace' => 'assman',
		'area' => 'assman:default'
    ),
    // You can override the site's primary url here, e.g. if you want your images served via a different port.
    // This setting is only used if the assman.url_override is set to true.
    array(
        'key'  =>     'assman.site_url',
		'value'=>     '',
		'xtype'=>     'textfield',
		'namespace' => 'assman',
		'area' => 'assman:default'
    ),
    array(
        'key'  =>     'assman.class_keys',
		'value'=>     '["modDocument"]',
		'xtype'=>     'textfield',
		'namespace' => 'assman',
		'area' => 'assman:default'
    ),
    array(
        'key'  =>     'assman.thumbnail_width',
		'value'=>     '240',
		'xtype'=>     'textfield',
		'namespace' => 'assman',
		'area' => 'assman:default'
    ),
    array(
        'key'  =>     'assman.thumbnail_height',
		'value'=>     '180',
		'xtype'=>     'textfield',
		'namespace' => 'assman',
		'area' => 'assman:default'
    ),
    array(
        'key'  =>     'assman.autocreate_content_type',
		'value'=>     '1',
		'xtype'=>     'combo-boolean',
		'namespace' => 'assman',
		'area' => 'assman:default'
    ),
    array(
        'key'  =>     'assman.groups',
		'value'=>     '',
		'xtype'=>     'textfield',
		'namespace' => 'assman',
		'area' => 'assman:default'
    ),
);
/*EOF*/