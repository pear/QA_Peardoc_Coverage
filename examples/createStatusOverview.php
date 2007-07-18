<?php
require_once 'QA/Peardoc/Coverage.php';

if (!isset($statusOverview) || !isset($strManualPath) || !isset($strPearDir)) {
    echo <<<EOD
This file may be only used by including it from another php script
 that defines the \$statusOverview variable.
Further, \$strManualPath and \$strPearDir variables need to be set.
 \$strManualPath needs to be "path/to/peardoc/manual.xml",
 \$strPearDir "path/to/pearcvs/"
Also, 'doc.dat' needs to be in the current directory.

EOD;
    exit(1);
}

$pdc = new QA_Peardoc_Coverage($strManualPath, $strPearDir);
$ret = QA_Peardoc_Coverage::renderCoverage(
    unserialize(file_get_contents('doc.dat')),
    'QA_Peardoc_Coverage_Renderer_StatusOverview',
    array($statusOverview)
);
?>