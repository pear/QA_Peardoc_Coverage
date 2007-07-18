<?php
require_once 'QA/PEAR/CI/StatusOverview.php';

if ($argc < 4) {
    echo <<<EOD
Tests QA_Peardoc_Coverage StatusOverview interaction
Usage:
    php testStatusOverview.php path/to/peardoc/manual.xml path/to/pearcvs/ path/to/statusOverview/file

EOD;
    exit(1);
}

$strManualPath = $argv[1];
$strPearDir    = $argv[2];
$strStatusFile = $argv[3];

$statusOverview = new QA_PEAR_CI_StatusOverview($strStatusFile);

require_once 'createStatusOverview.php';

?>