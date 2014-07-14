<?php
/**
 *
 * To run these tests, pass the test directory as the 1st argument to phpunit:
 *
 *   phpunit path/to/moxycart/core/components/assman/tests
 *
 * or if you're having any trouble running phpunit, download its .phar file, and 
 * then run the tests like this:
 *
 *  php phpunit.phar path/to/moxycart/core/components/moxycart/tests
 *
 *
 GOTCHAS:
 
 1. You need to set the $modx variable before using runSnippet:
        global $modx;
        $modx = self::$modx;
 (using & won't work for some reason)

 2. You must run tests with the same permissions as the webserver, e.g. in MAMP
    you must run tests as the admin user.

 3. runSnippet will not preserve datatypes on return, so you cannot rely on assertTrue 
    or assertFalse to check the outputs.  E.g. returning false will return '', returning 
    true from a Snippet returns a 1.
    
 */
namespace Assman;
class snippetTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    
    /**
     * Load up MODX for our tests.
     *
     */
    public static function setUpBeforeClass() {        
        self::$modx = new \modX();
        self::$modx->initialize('mgr');
        $core_path = self::$modx->getOption('assman.core_path','',MODX_CORE_PATH.'components/assetmanager/');
        self::$modx->addExtensionPackage('assman',"{$core_path}model/orm/", array('tablePrefix'=>'ass_'));
        self::$modx->addPackage('assman',"{$core_path}model/",'ass_');
        
        // Create Page
        if (!self::$Page = self::$modx->getObject('modResource', array('alias'=>'test-test-test'))) {
            self::$Page = self::$modx->newObject('modResource');
            self::$Page->fromArray(array(
                'alias' => 'test-test-test',
                'pagetitle' => 'Test Asset 101',
            ));
            self::$Page->save();        
        }        
    }  


    /**
     * Seems that you have to force the Snippet to be cached before this will work.
     */
    public function testSecure() {
        global $modx;
        $modx = self::$modx;

        $props = array();

        $props['input'] = '29.99';
        $props['options'] = '';
        $actual = $modx->runSnippet('scale2w', $props);
        $expected = '';
        $this->assertEquals(normalize_string($expected), normalize_string($actual));
        
    }

}