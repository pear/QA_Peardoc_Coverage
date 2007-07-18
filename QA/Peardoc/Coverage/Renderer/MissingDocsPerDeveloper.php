<?php
require_once 'QA/Peardoc/Coverage/Renderer.php';
require_once 'QA/Peardoc/Coverage/Renderer/DeveloperList.php';

/**
* Creates the serialized output of an array
* containing the email address of a developer
* and his undocumented packages.
*
* array(
*     email  => array(package1, package2),
*     email2 => array(package1, package2),
* )
*
* @category QA
* @package  QA_Peardoc_Coverage
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @version  CVS: $Id$
* @link     http://pear.php.net/package/QA_Peardoc_Coverage
*/
class QA_Peardoc_Coverage_Renderer_MissingDocsPerDeveloper implements QA_Peardoc_Coverage_Renderer
{
    /**
    * Renders the given coverage array and
    * returns the HTML.
    *
    * @param array $arDoc     Documentation coverage analysis results
    * @param array $arOptions Options
    *
    * @return string HTML
    */
    public function render($arDoc, $arOptions = null)
    {
        $arMaintainers = QA_Peardoc_Coverage_Renderer_DeveloperList::getMaintainers($arDoc);
        $arList        = array();

        foreach ($arMaintainers as $strUsername => $arMaintainer) {
            $strEmail       = $arMaintainer['email'];
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