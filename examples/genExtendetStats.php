<?php
require_once 'QA/Peardoc/Coverage.php';

if ($argc < 3) {
    echo <<<EOD
Generates an extendet PEAR documentation coverage.
Usage:
    php genExtendetStats.php path/to/peardoc/manual.xml path/to/pearcvs/

EOD;
    exit(1);
}

$strManualPath  = $argv[1];
$strPearDir     = $argv[2];

$pdc = new QA_Peardoc_Coverage($strManualPath, $strPearDir);
echo QA_Peardoc_Coverage::renderCoverage(
    unserialize(file_get_contents('doc.dat')),
    'QA_Peardoc_Coverage_Renderer_ExtendetPackageList'
);
?>