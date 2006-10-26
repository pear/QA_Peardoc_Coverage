<?php

/**
*   Returns a method list for the class
*   defined in the given filename.
*
*   @author Christian Weiske <cweiske@php.net>
*/
class QA_Peardoc_Coverage_MethodList
{
    /**
    *   Returns a method list for the class
    *   defined in the given filename.
    *
    *   @param string $strClassFile     Path of a .php file to load
    *   @return array   Array with classname and methods
    */
    public static function getMethods($strClassFile)
    {
        if (!file_exists($strClassFile)) {
            throw new Exception('File does not exist: ' . $strClassFile);
        }

        $strClassName = self::getClassnameFromFilename($strClassFile);

        if ($strClassName === false) {
            return false;
        }

        $bWaitForClassname  = false;
        $bWaitForMethodname = false;
        $arMethods          = array();
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
                    $strClassName = $strText;
                    $bWaitForClassname = false;
                } else if ($bWaitForMethodname && $nId == T_STRING) {
                    $strMethodname = $strText;
                    //FIXME: check if docblock
                    //FIXME: use public methods only
                    $arMethods[$strMethodname] = false;
                    $bWaitForMethodname = false;
                }
            }
        }

        return array($strClassName, $arMethods);
    }//public static function getMethods($strClassFile)



    /**
    *   Tries to guess a classname from a given filename
    *
    *   @param string $strClassFile     .php filename
    *   @return string  Class name OR false if no class found
    */
    public static function getClassnameFromFilename($strClassFile)
    {
        //simple: open file and search for "class classname"
        $strContent = file_get_contents($strClassFile);

        if (preg_match('/' . "(?:\r|\n)" . '\\s*(?:abstract\\s+)?(?:final\\s+)?(?:[Cc]lass|interface)\\s+([A-Za-z0-9_]+)/', $strContent, $arMatches)) {
            return $arMatches[1];
        } else {
            //no match?
            //throw new Exception('No classname in ' . $strClassFile);
            return false;
        }
    }//public static function getClassnameFromFilename($strClassFile)

}//class QA_Peardoc_Coverage_MethodList
?>