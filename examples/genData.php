<?php
require_once 'QA/Peardoc/Coverage.php';

if ($argc < 3) {
    echo <<<EOD
Generates PEAR documentation coverage data.
Usage:
    php genData.php path/to/peardoc/manual.xml path/to/pearcvs/

EOD;
    exit(1);
}

//$strManualPath = dirname(__FILE__) . '/../peardoc/manual.xml';
//$strPearDir    = dirname(__FILE__) . '/../pear/';
$strManualPath  = $argv[1];
$strPearDir     = $argv[2];

$pdc = new QA_Peardoc_Coverage($strManualPath, $strPearDir);
file_put_contents(
    'doc.dat',
    serialize(
        $pdc->generateCoverage()
    )
);
?>