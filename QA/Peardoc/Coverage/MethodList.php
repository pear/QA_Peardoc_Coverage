<?php
require_once 'QA/Peardoc/Coverage/ClassList.php';

/**
*   Returns a method list for the class
*   defined in the given filename.
*
*   @author Christian Weiske <cweiske@php.net>
*/
class QA_Peardoc_Coverage_MethodList
{
    /**
    *   Returns a class => method list for the classes
    *   defined in the given filename.
    *
    *   @param string $strClassFile     Path of a .php file to load
    *   @return array   Array with classname => methods => bool array
    */
    public static function getMethods($strClassFile)
    {
        if (!file_exists($strClassFile)) {
            throw new Exception('File does not exist: ' . $strClassFile);
        }

        $arClassnames = QA_Peardoc_Coverage_ClassList::getClassnamesFromFilename($strClassFile);

        if (count($arClassnames) == 0) {
            return array();
        }

        $bWaitForClassname  = false;
        $bWaitForMethodname = false;
        $arMethods          = array();
        $strClassName       = '*funcs*';
        foreach (
            token_get_all(
                file_get_contents($strClassFile)
            )
            as $token
        ) {
            if (!is_string($token)) {
                list($nId, $strText) = $token;
                if ($nId == T_CLASS) {
                    $bWaitForClassname = true;
                } else if ($nId == T_FUNCTION) {
                    $bWaitForMethodname = true;
                } else if ($bWaitForClassname && $nId == T_STRING) {
                    //classname found
                    $strClassName = $strText;
                    $arMethods[$strClassName] = array();
                    $bWaitForClassname = false;
                } else if ($bWaitForMethodname && $nId == T_STRING) {
                    //methodname found
                    $strMethodname = $strText;
                    //FIXME: check if docblock
                    //FIXME: use public methods only
                    $arMethods[$strClassName][$strMethodname] = false;
                    $bWaitForMethodname = false;
                }
            }
        }

        return $arMethods;
    }//public static function getMethods($strClassFile)

}//class QA_Peardoc_Coverage_MethodList
?>