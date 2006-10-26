<?php
/**
*   Displays the packages per email on the console
*/
$ar = unserialize(file_get_contents('missingdocs.serialized'));
foreach ($ar as $strEmail => $arPackages) {
    echo $strEmail . "\n";
    echo '  ' . implode(', ', $arPackages) . "\n";
}
?>