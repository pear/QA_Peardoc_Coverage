<?php
require_once 'QA/Peardoc/Coverage/Renderer.php';

/**
* Puts the coverage result into the PEAR_QA_CI StatusOverview
* database.
*
* @author Christian Weiske <cweisek@php.net>
*/
class QA_Peardoc_Coverage_Renderer_StatusOverview
    implements QA_Peardoc_Coverage_Renderer
{

    /**
    * Renders the given coverage array and returns the HTML.
    *
    * @param array $arDoc   Documentation check results array
    * @param array $options Array with a statusoverview object
    *
    * @return boolean true if all was ok
    */
    public function render($arDoc, $options = null)
    {
        if (!isset($options[0])
            || !($options[0] instanceof QA_PEAR_CI_StatusOverview)
        ) {
            throw new Exception(
                'Please pass a StatusOverview object as only option'
            );
        }

        $so = $options[0];
        $so->registerCategory('doc');

        foreach ($arDoc as $strCategory => $arCategoryPackages) {
            if ($strCategory[0] == '*') { continue; }

            foreach ($arCategoryPackages as $strPackageName => $arPackageCoverage) {
                //in case there is no error :)
                $so->addPackage($strPackageName);

                if ($arPackageCoverage['*docid*'] === null) {
                    //not documented
                    $so->addError(
                        $strPackageName,
                        'doc',
                        'No documentation at all');
                } else {
                    //documented
                    self::getMethodDocState(
                        $strPackageName,
                        $arPackageCoverage,
                        $so
                    );
                }
            }//foreach package in category
        }//foreach category

        $so->save();
        return true;
    }//public function render($arDoc)



    /**
    * Generates the class/method coverage html
    *
    * @param string                    $strPackageName    Package name
    * @param array                     $arPackageCoverage Coverage array for
    *                                                      that package
    * @param QA_PEAR_CI_StatusOverview $so                StatusOverview object
    * @return void
    */
    public static function getMethodDocState($strPackageName, $arPackageCoverage, $so)
    {
        foreach ($arPackageCoverage as $strClass => $arMethods) {
            if ($strClass[0] == '*') { continue; }

            if ($arMethods === null) {
                //FIXME: display not docced
                $so->addWarning(
                    $strPackageName,
                    'doc',
                    $strClass . ' undocumented'
                );
                continue;
            }

            foreach ($arMethods as $strMethod => $bDocumented) {
                if ($strMethod[0] == '_') { continue; }

                if (!$bDocumented) {
                    $so->addWarning(
                        $strPackageName,
                        'doc',
                        $strClass . '::' . $strMethod . ' undocumented'
                    );
                }
            }
        }
    }//public static function getMethodDocState($arPackageCoverage)

}//class QA_Peardoc_Coverage_Renderer_StatusOverview
?>