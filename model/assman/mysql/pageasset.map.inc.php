<?php
$xpdo_meta_map['PageAsset']= array (
  'package' => 'assman',
  'version' => '1.0',
  'table' => 'page_assets',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'page_id' => NULL,
    'asset_id' => NULL,
    'group' => NULL,
    'is_active' => 1,
    'seq' => NULL,
  ),
  'fieldMeta' => 
  array (
    'page_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'asset_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'group' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => true,
    ),
    'is_active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
      'comment' => 'Used to disable/enable an asset on a given page',
    ),
    'seq' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '3',
      'phptype' => 'integer',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'columns' => 
      array (
        'id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Page' => 
    array (
      'class' => 'modResource',
      'local' => 'page_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Asset' => 
    array (
      'class' => 'Asset',
      'local' => 'asset_id',
      'foreign' => 'asset_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
