<?php
/**
*   Renderer interface.
*/
interface QA_Peardoc_Coverage_Renderer
{
    /**
    *   This method gets the $arDoc array
    *   with coverage analysis, and returns
    *   a string with the rendered coverage.
    */
    public function render($arDoc);

}//interface QA_Peardoc_Coverage_Renderer
?>