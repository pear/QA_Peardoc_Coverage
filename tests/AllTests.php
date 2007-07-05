<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'QA_Peardoc_Coverage_AllTests::main');
    chdir(dirname(__FILE__) . '/../');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';


require_once 'QA_Peardoc_CoverageTest.php';
require_once 'QA_Peardoc_Coverage_ClassListTest.php';
require_once 'QA_Peardoc_Coverage_MethodListTest.php';
require_once 'QA_Peardoc_Coverage_Renderer_DeveloperListTest.php';


class QA_Peardoc_Coverage_AllTests
{
    public static function main()
    {

        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('QA_Peardoc_Coverage Tests');
        /** Add testsuites, if there is. */
        $suite->addTestSuite('QA_Peardoc_CoverageTest');
        $suite->addTestSuite('QA_Peardoc_Coverage_ClassListTest');
        $suite->addTestSuite('QA_Peardoc_Coverage_MethodListTest');
        $suite->addTestSuite('QA_Peardoc_Coverage_Renderer_DeveloperListTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'QA_Peardoc_Coverage_AllTests::main') {
    QA_Peardoc_Coverage_AllTests::main();
}
?>