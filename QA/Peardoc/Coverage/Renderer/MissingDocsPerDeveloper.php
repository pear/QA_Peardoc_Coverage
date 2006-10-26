<?php
require_once 'QA/Peardoc/Coverage/Renderer.php';
require_once 'QA/Peardoc/Coverage/Renderer/DeveloperList.php';
require_once 'HTML/Table.php';

/**
*   Creates the serialized output of an array
*   containing the email address of a developer
*   and his undocumented packages.
*
*   array(
*       email  => array(package1, package2),
*       email2 => array(package1, package2),
*   )
*
*   @author Christian Weiske <cweisek@php.net>
*/
class QA_Peardoc_Coverage_Renderer_MissingDocsPerDeveloper implements QA_Peardoc_Coverage_Renderer
{
    /**
    *   Renders the given coverage array and
    *   returns the HTML.
    */
    public function render($arDoc)
    {
        $arMaintainers = QA_Peardoc_Coverage_Renderer_DeveloperList::getMaintainers($arDoc);
        $arList = array();

        foreach ($arMaintainers as $strUsername => $arMaintainer) {
            $strEmail = $arMaintainer['email'];
            $arUndocumented = array();
            if ($arMaintainer['docced'] != $arMaintainer['packages']) {
                foreach ($arMaintainer['packagelist'] as $strPackageName => $arPackage) {
                    if ($arPackage['*docid*'] === null) {
                        $arUndocumented[] = $strPackageName;
                    }
                }
            }

            if (count($arUndocumented) > 0) {
                $arList[$strEmail] = $arUndocumented;
            }
        }

        ksort($arList);

        return serialize($arList);
    }//public function render($arDoc)

}//class QA_Peardoc_Coverage_Renderer_MissingDocsPerDeveloper implements QA_Peardoc_Coverage_Renderer
?>