<?php

require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

// Find MODX
$docroot = dirname(dirname(dirname(dirname(__FILE__))));
while (!file_exists($docroot.'/config.core.php')) {
    if ($docroot == '/') {
        die('Failed to locate config.core.php');
    }
    $docroot = dirname($docroot);
}
if (!file_exists($docroot.'/config.core.php')) {
    die('Failed to locate config.core.php');
}
include_once $docroot . '/config.core.php';

if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', true);
}
//include_once $docroot . '/index.php';

include_once MODX_CORE_PATH . 'model/modx/modx.class.php';

/*EOF*/