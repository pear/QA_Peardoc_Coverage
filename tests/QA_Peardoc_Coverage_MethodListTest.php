<?php
// Call QA_Peardoc_Coverage_MethodListTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "QA_Peardoc_Coverage_MethodListTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

// You may remove the following line when all tests have been implemented.
require_once "PHPUnit/Framework/IncompleteTestError.php";

require_once "QA/Peardoc/Coverage/MethodList.php";
require_once dirname(__FILE__) . '/config.php';

/**
 * Test class for QA_Peardoc_Coverage_MethodList.
 * Generated by PHPUnit_Util_Skeleton on 2006-10-18 at 12:28:36.
 */
class QA_Peardoc_Coverage_MethodListTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("QA_Peardoc_Coverage_MethodListTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
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
     *
     */
    public function testGetMethodsAuth() {
        $strAuthDir = $this->strPearDir . '/Auth';
        $strAuthFile = $strAuthDir . '/Auth.php';
        $this->assertTrue(file_exists($strAuthFile));

        $ar = QA_Peardoc_Coverage_MethodList::getMethods($strAuthFile);
        $this->assertTrue(isset($ar['Auth']));
        $this->assertTrue(isset($ar['Auth']['Auth']));
        $this->assertTrue(isset($ar['Auth']['applyAuthOptions']));
        $this->assertTrue(isset($ar['Auth']['_loadStorage']));
    }

    /**
     *
     */
    public function testGetMethodsMDB2() {
        $strMdb2Dir = $this->strPearDir . '/MDB2';
        $strMdb2File = $strMdb2Dir. '/MDB2.php';
        $this->assertTrue(file_exists($strMdb2File));

        $ar = QA_Peardoc_Coverage_MethodList::getMethods($strMdb2File);
        $this->assertTrue(isset($ar['MDB2']));
        $this->assertTrue(isset($ar['MDB2_Error']));
        $this->assertTrue(isset($ar['MDB2_Driver_Common']));
        $this->assertTrue(isset($ar['MDB2_Result']));
        $this->assertTrue(isset($ar['MDB2_Result_Common']));
        $this->assertTrue(isset($ar['MDB2_Row']));
        $this->assertTrue(isset($ar['MDB2_Statement_Common']));
        $this->assertTrue(isset($ar['MDB2_Module_Common']));

        $this->assertTrue(isset($ar['MDB2']['setOptions']));
        $this->assertTrue(isset($ar['MDB2']['classExists']));
        $this->assertTrue(isset($ar['MDB2']['loadClass']));
        $this->assertTrue(isset($ar['MDB2']['factory']));
        $this->assertTrue(isset($ar['MDB2']['connect']));
        $this->assertTrue(isset($ar['MDB2']['singleton']));

        $this->assertTrue(isset($ar['MDB2_Error']['MDB2_Error']));
        $this->assertTrue(isset($ar['MDB2_Error']['MDB2_Error']));

        $this->assertTrue(isset($ar['MDB2_Driver_Common']['__construct']));
        $this->assertTrue(isset($ar['MDB2_Driver_Common']['__destruct']));
        $this->assertTrue(isset($ar['MDB2_Driver_Common']['MDB2_Driver_Common']));
        $this->assertTrue(isset($ar['MDB2_Driver_Common']['free']));
        $this->assertTrue(isset($ar['MDB2_Driver_Common']['errorInfo']));

        $this->assertTrue(isset($ar['MDB2_Result_Common']['seek']));
        $this->assertTrue(isset($ar['MDB2_Result_Common']['fetchRow']));
        $this->assertTrue(isset($ar['MDB2_Result_Common']['fetchOne']));
    }
}

// Call QA_Peardoc_Coverage_MethodListTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "QA_Peardoc_Coverage_MethodListTest::main") {
    QA_Peardoc_Coverage_MethodListTest::main();
}
?>
