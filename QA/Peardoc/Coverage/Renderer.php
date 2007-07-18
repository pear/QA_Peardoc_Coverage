<?php
/**
* Renderer interface.
*
* @category QA
* @package  QA_Peardoc_Coverage
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @version  CVS: $Id$
* @link     http://pear.php.net/package/QA_Peardoc_Coverage
*/
interface QA_Peardoc_Coverage_Renderer
{
    /**
    * This method gets the $arDoc array
    * with coverage analysis, and returns
    * a string with the rendered coverage.
    *
    * @param array $arDoc     Documentation coverage analysis results
    * @param array $arOptions Options
    *
    * @return string HTML
    */
    public function render($arDoc, $arOptions = null);

}//interface QA_Peardoc_Coverage_Renderer
?>