<?php
// Use PHPUnit2 to create the skeletons.
require_once 'PHPUnit2/Util/Skeleton.php';

/*
require_once 'QA/Peardoc/Coverage.php';
require_once 'QA/Peardoc/Coverage/ClassList.php';
require_once 'QA/Peardoc/Coverage/MethodList.php';
require_once 'QA/Peardoc/Coverage/Renderer/SimpleClassList.php';
*/

$classes = array(
    'QA_Peardoc_Coverage_Renderer_DeveloperList'
/*
    'QA_Peardoc_Coverage_Renderer_SimpleClassList',
    'QA_Peardoc_Coverage_ClassList',
    'QA_Peardoc_Coverage_MethodList',
    'QA_Peardoc_Coverage',
*/
);

foreach ($classes as $class) {
    // Create a skeleton for the class.
    $skeleton = new PHPUnit2_Util_Skeleton(
        $class,
        str_replace('_', '/', $class) . '.php'
    );

    // Write the test.
    $skeleton->write();
}
?>