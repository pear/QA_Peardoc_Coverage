<?php
require_once 'QA/Peardoc/Coverage/Renderer.php';
require_once 'HTML/Table.php';

/**
*   Renders the coverage result in an extendet
*   list of packages with its documented state,
*   and the classes with their methods.
*
*   @author Christian Weiske <cweisek@php.net>
*/
class QA_Peardoc_Coverage_Renderer_ExtendetPackageList implements QA_Peardoc_Coverage_Renderer
{
    public static $colNotDocumented = '#F00';
    public static $colDocumented    = '#0F0';

    public static $colMethodDocumented      = '#9F9';
    public static $colMethodNotDocumented   = '#F99';
    public static $colMethodPartlyDocumented= '#FF9';



    /**
    *   Returns the color code matching the number.
    *
    *   @param float    $flNumber   Number (x/y), !no! percentage
    *   @return string  HTML color #0AF
    */
    public static function getColor($flNumber)
    {
        if ($flNumber == 1) {
            return '#0F0';
        } else if ($flNumber >= 0.9) {
            return '#dfff00';
        } else if ($flNumber >= 0.5) {
            return '#FF0';
        } else if ($flNumber >= 0.3) {
            return '#F70';
        } else {
            return '#F00';
        }
    }//public static function getColor($flNumber)



    /**
    *   Returns the manual url (deep link) for
    *   the given documentation id.
    *
    *   @param string $strDocId     Documentation id=""
    *   @return string      URL to the manual
    */
    public static function getDocUrl($strDocId)
    {
        return 'http://pear.php.net/manual/en/'
            . $strDocId . '.php';
    }//public static function getDocUrl($strDocId)



    /**
    *   Renders the given coverage array and
    *   returns the HTML.
    */
    public function render($arDoc)
    {
        $n = "\n";
        $out = '';
        $out .= '<?xml version="1.0" encoding="utf-8" ?>' . $n
            . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '
            . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

        $out .= '<html><head><title>Extendet PEAR Documentation coverage analysis</title>'
            . '<style type="text/css">th { background-color: black; color: white; }'
            . 'td.class { font-weight:bold;}'
            . '</style></head><body>';
        $out .= '<table border="1"><caption>'
            . 'Extendet PEAR Documentation coverage analysis as of '
            . date('Y-m-d H:i:s')
            . '</caption>' . $n;

        $nPackages       = 0;
        $nDoccedPackages = 0;
        $nCategories     = 0;
        //Package name|Class name|methods|number/percent

        foreach ($arDoc as $strCategory => $arCategoryPackages) {
            $out .= '<tr><th colspan="3">' . ucfirst($strCategory) . '</th></tr>' . $n;
            ++$nCategories;
            $nCategoryPackages       = 0;
            $nCategoryDoccedPackages = 0;

            foreach ($arCategoryPackages as $strPackageName => $arPackageCoverage) {
                ++$nCategoryPackages;
                ++$nPackages;

                $out .= '<tr><td colspan="2">'
                     . '<a href="http://pear.php.net/package/' . $strPackageName . '">'
                     . $strPackageName
                     . '</a>'
                     . '</td>';
                if ($arPackageCoverage['*docid*'] === null) {
                    //not documented
                    $out .= '<td style="background-color:'
                         . self::$colNotDocumented
                         . '">not documented</td></tr>' . $n;
                } else {
                    //documented
                    ++$nDoccedPackages;
                    ++$nCategoryDoccedPackages;
                    $out .= '<td style="background-color:'
                         . self::$colDocumented
                         . '"><a href="'
                         . self::getDocUrl($arPackageCoverage['*docid*'])
                         . '">documented</a></td></tr>' . $n;
                    $out .= self::getMethodDocState($arPackageCoverage);
                }
            }//foreach package in category
/*
            $col = self::getColor($nCategoryDoccedPackages/$nCategoryPackages);
            $out .= '<tr>'
                    . '<td>' . $nCategoryPackages . '</td>'
                    . '<td style="text-align:right; font-weight:bold; background-color:' . $col . '">'
                        . $nCategoryDoccedPackages . '/' . $nCategoryPackages . '</td>'
                    . '</tr>' . $n;
*/
        }//foreach category

        $col = self::getColor($nDoccedPackages/$nPackages);
        $out .= '<tr style="font-weight:bold; background-color:' . $col . '">'
              . '<td rowspan="2">' . $nCategories . ' categories</td>'
              . '<td style="text-align:right;">Packages documented: ' . $nDoccedPackages . '/' . $nPackages . '</td>'
              . '</tr>' . $n;
        $out .= '<tr style="font-weight:bold; background-color:' . $col . '">'
              . '<td style="text-align:right;">' . number_format($nDoccedPackages/$nPackages * 100, 2) . '%</td>'
              . '</tr>' . $n;

        $out .= '</table>';

        $out .= '</body></html>';

        return $out;
    }//public function render($arDoc)



    /**
    *   Generates the class/method coverage html
    *
    *   @return string  class and method coverage HTML
    */
    public static function getMethodDocState($arPackageCoverage)
    {
        $n = "\n";
        $out = '';
        $nClasses       = 0;
        $nClassesDocced = 0;
        foreach ($arPackageCoverage as $strClass => $arMethods) {
            if ($strClass[0] == '*') { continue; }
            ++$nClasses;

            if ($arMethods === null) {
                //FIXME: display not docced
                $out .= '<tr><td></td><td class="class">'
                    . $strClass
                    . '</td><td style="background-color:' . self::$colMethodNotDocumented . '">poor</td></tr>' . $n;
                continue;
            }

            $nMethods       = 0;
            $nMethodsDocced = 0;
            $strDocced      = '';
            $strNotDocced   = '';
            foreach ($arMethods as $strMethod => $bDocumented) {
                if ($strMethod[0] == '_') { continue; }

                ++$nMethods;
                if ($bDocumented) {
                    ++$nMethodsDocced;
                    $strDocced .= $strMethod . ', ';
                } else {
                    $strNotDocced .= $strMethod . ', ';
                }
            }

            //class
            $mout = '';
            if ($nMethods > 0 && $nMethods != $nMethodsDocced) {
                if ($nMethodsDocced > 0) {
                    //docced
                    $mout .= '<tr><td></td>'
                        . '<td style="background-color:' . self::$colMethodDocumented . '">'
                        . $strDocced . '</td>'
                        . '<td>' . $nMethodsDocced . '</td></tr>' . $n;

                }
                //undocced
                $mout .= '<tr><td></td>'
                    . '<td style="background-color:' . self::$colMethodNotDocumented . '">'
                    . $strNotDocced . '</td>'
                    . '<td>' . ($nMethods - $nMethodsDocced) . '</td></tr>' . $n;
            }

            if ($nMethods == $nMethodsDocced) {
                $strState = 'perfect';
                $col = self::$colMethodDocumented;
            } else if ($nMethodsDocced == 0) {
                $strState = 'poor';
                $col = self::$colMethodNotDocumented;
            } else {
                $strState = 'partly';
                $col = self::$colMethodPartlyDocumented;
            }

            $out .= '<tr><td></td>'
                . '<td class="class">' . $strClass . '</td>'
                . '<td style="background-color:' . $col . '">' . $strState . '</td></tr>' . $n;
            $out .= $mout;
        }

        return $out;
    }//public static function getMethodDocState($arPackageCoverage)

}//class QA_Peardoc_Coverage_Renderer_ExtendetPackageList implements QA_Peardoc_Coverage_Renderer
?>