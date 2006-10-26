<?php
// Call QA_Peardoc_CoverageTest::main() if this source file is executed directly.
if (!defined("PHPUnit2_MAIN_METHOD")) {
    define("PHPUnit2_MAIN_METHOD", "QA_Peardoc_CoverageTest::main");
}

require_once "PHPUnit2/Framework/TestCase.php";
require_once "PHPUnit2/Framework/TestSuite.php";

// You may remove the following line when all tests have been implemented.
require_once "PHPUnit2/Framework/IncompleteTestError.php";

require_once "QA/Peardoc/Coverage.php";
require_once dirname(__FILE__) . '/config.php';

/**
 * Test class for QA_Peardoc_Coverage.
 * Generated by PHPUnit2_Util_Skeleton on 2006-10-18 at 12:28:36.
 */
class QA_Peardoc_CoverageTest extends PHPUnit2_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit2/TextUI/TestRunner.php";

        $suite  = new PHPUnit2_Framework_TestSuite("QA_Peardoc_CoverageTest");
        $result = PHPUnit2_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
        $this->strPearDir = $GLOBALS['testConfig']['PearDir'];
        $this->assertTrue(file_exists($this->strPearDir));
        $this->assertTrue(is_dir($this->strPearDir));
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    /**
     * @todo Implement testGenerateCoverage().
     */
    public function testGenerateCoverage() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testRenderCoverage().
     */
    public function testRenderCoverage() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testGetPackageList().
     */
    public function testGetPackageList() {
        $ar = QA_Peardoc_Coverage::getPackageList($this->strPearDir);
        $this->assertTrue(in_array('Auth'           , $ar));
        $this->assertTrue(in_array('LiveUser'       , $ar));
        $this->assertTrue(in_array('System_Folders' , $ar));
        $this->assertTrue(in_array('Selenium'       , $ar));
        $this->assertTrue(in_array('XML_RPC'        , $ar));
    }

    /**
     * @todo Implement testGetCategory().
     */
    public function testGetCategory() {
        $this->assertEquals(array('authentication'),
            QA_Peardoc_Coverage::getCategory('Auth')
        );
        $this->assertEquals(array('database'),
            QA_Peardoc_Coverage::getCategory('MDB')
        );
        $this->assertEquals(array('php'),
            QA_Peardoc_Coverage::getCategory('PHP_Archive')
        );
        $this->assertEquals(array('testing'),
            QA_Peardoc_Coverage::getCategory('Selenium')
        );
    }

    /**
     * @todo Implement testGetPackageDocId().
     */
    public function testGetPackageDocId() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testExistsId().
     */
    public function testExistsId() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testGetPackageCoverage().
     */
    public function testGetPackageCoverage() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }
}

// Call QA_Peardoc_CoverageTest::main() if this source file is executed directly.
if (PHPUnit2_MAIN_METHOD == "QA_Peardoc_CoverageTest::main") {
    QA_Peardoc_CoverageTest::main();
}
?>
