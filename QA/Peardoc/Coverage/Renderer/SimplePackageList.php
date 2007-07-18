<?php
require_once 'QA/Peardoc/Coverage/Renderer.php';

/**
*   Renders the coverage result in a simple
*   list of packages with their documentation state.
*
*   @author Christian Weiske <cweisek@php.net>
*/
class QA_Peardoc_Coverage_Renderer_SimplePackageList implements QA_Peardoc_Coverage_Renderer
{
    public static $colNotDocumented = '#F00';
    public static $colDocumented    = '#0F0';



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
    public function render($arDoc, $arOptions = null)
    {
        $n = "\n";
        $out = '';
        $out .= '<?xml version="1.0" encoding="utf-8" ?>' . $n
            . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '
            . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

        $out .= '<html><head><title>Simple PEAR Documentation coverage analysis</title></head><body>';
        $out .= '<table border="1"><caption>'
            . 'Simple PEAR Documentation coverage analysis as of '
            . date('Y-m-d H:i:s', $arDoc['*date*'])
            . '</caption>' . $n;

        $nPackages       = 0;
        $nDoccedPackages = 0;
        $nCategories     = 0;

        foreach ($arDoc as $strCategory => $arCategoryPackages) {
            if ($strCategory[0] == '*') { continue; }

            $out .= '<tr><th colspan="3">' . ucfirst($strCategory) . '</th></tr>' . $n;
            ++$nCategories;
            $nCategoryPackages       = 0;
            $nCategoryDoccedPackages = 0;

            foreach ($arCategoryPackages as $strPackageName => $arPackageCoverage) {
                ++$nCategoryPackages;
                ++$nPackages;

                $out .= '<tr><td></td><td>'
                     . '<a href="http://pear.php.net/package/' . $strPackageName . '" name="' . $strPackageName . '">'
                     . $strPackageName
                     . '</a>'
                     . '</td>';
                if ($arPackageCoverage['*docid*'] === null) {
                    //not documented
                    $out .= '<td style="background-color:'
                         . self::$colNotDocumented
                         . '">not documented</td>';
                } else {
                    //documented
                    ++$nDoccedPackages;
                    ++$nCategoryDoccedPackages;
                    $out .= '<td style="background-color:'
                         . self::$colDocumented
                         . '"><a href="'
                         . self::getDocUrl($arPackageCoverage['*docid*'])
                         . '">documented</a></td>';
                }
                $out .= '</tr>' . $n;
            }//foreach package in category

            $col = self::getColor($nCategoryDoccedPackages/$nCategoryPackages);
            $out .= '<tr>'
                    . '<td>Sum</td>'
                    . '<td>' . $nCategoryPackages . '</td>'
                    . '<td style="text-align:right; font-weight:bold; background-color:' . $col . '">'
                        . $nCategoryDoccedPackages . '/' . $nCategoryPackages . '</td>'
                    . '</tr>' . $n;
        }//foreach category

        $col = self::getColor($nDoccedPackages/$nPackages);
        $out .= '<tr style="font-weight:bold; background-color:' . $col . '">'
              . '<td rowspan="2">All in all</td>'
              . '<td rowspan="2">' . $nCategories . ' categories</td>'
              . '<td style="text-align:right;">' . $nDoccedPackages . '/' . $nPackages . '</td>'
              . '</tr>' . $n;
        $out .= '<tr style="font-weight:bold; background-color:' . $col . '">'
              . '<td style="text-align:right;">' . number_format($nDoccedPackages/$nPackages * 100, 2) . '%</td>'
              . '</tr>' . $n;

        $out .= '</table>';

        $out .= '</body></html>';

        return $out;
    }//public function render($arDoc)

}//class QA_Peardoc_Coverage_Renderer_SimplePackageList implements QA_Peardoc_Coverage_Renderer
?>