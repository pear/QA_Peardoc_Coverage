<?php
/**
*   displays the generated data with Gtk2_VarDump
*/
require_once 'Gtk2/VarDump.php';
Gtk2_VarDump::display(
    unserialize(
        file_get_contents('doc.dat')
    ),
    'PEAR documentation coverage data'
);
?>