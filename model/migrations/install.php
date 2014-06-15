<?php
$core_path = $modx->getOption('assman.core_path','',MODX_CORE_PATH.'components/assman/');

// Add the package to the MODX extension_packages array
// TODO: read the table prefix from config
$modx->addExtensionPackage($object['namespace'],"{$core_path}model/", array('tablePrefix'=>'ass_'));
$modx->addPackage('assman',"{$core_path}model/",'ass_');

$manager = $modx->getManager();

$manager->createObjectContainer('Asset');
$manager->createObjectContainer('PageAsset');
