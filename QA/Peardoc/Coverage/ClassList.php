<?php

/**
*   Class and method list for a given package directory.
*
*   Don't list:
*   - doc|docs directory
*   - examples
*   - test|tests
*
*   @author Christian Weiske <cweiske@php.net>
*/
class QA_Peardoc_Coverage_Classlist
{
    protected static $arBadFiles = array(
        '/tests/',
        '/test/',
        '/test.php',
        '/test_.+.php',
        '/data/',
        '/cases/',
        '/demo/',
        '/doc/',
        '/docs/',
        '/example/',
        '/examples/',
        '/Examples/',
        '/example.php',
        '/.+_example.php',
        '/.+Example.php',
        '/scripts/',

        '/buildPackage.php',
        '/compileAll.php',
        '/createPackageXml.php',
        '/generate_package_xml.php',
        '/generate_package2_xml.php',
        '/generatePackage.xml.php',
        'constants.php',
        '/makepackage.php',
        '/package.php',
        '/package_.+.php',
        '/test.+.php',
        '/updatePear.php',
/*
        '/DBA_Relational/Toolbox.php',
        '/DB_DOM/DB_DOM.php',
        '/fix.+Files.php',
        '/Gtk_Styled/Buttons.php',
        '/HTTP_WebDAV_Client/Client.php',
        '/HTTP_WebDAV_Server/file.php',
        '/MDB_Frontend/Common.php',
        '/MDB_Frontend/Dump.php',
        '/MDB_Frontend/Frontend.php',
        '/MDB_Frontend/Login.tpl.php',
        '/MDB_Frontend/Update.php',
        '/PDBPLUS/pdbplus.php',
        '/PDBPLUS/readline.php',
        '/PEAR/PEAR.php',
        '/PEAR_Frontend_Web/WebInstaller.php',
        '/PHPDoc/front-end.php',
        '/PHPDoc/index.php',
        '/PHPDoc/prepend.php',
        '/PHP_Parser/skeleton.php',
        '/PHP_ParserGenerator/Lemon.php',
        '/PHP_ParserGenerator/parsephp.php',
        '/PhpDocumentor/phpdoc.php',
        '/SOAP_Interop/',
        '/SQL_Parser/Dialect_.+.php',
        '/SQL_Parser/ctype.php',
        '/Science_Chemistry/Chemistry.php',
        '/Services_Weather/buildMetarDB.php',
        '/Translation/translation.str_mgmt.php',
        '/XML_HTMLSax/XML_HTMLSax.php',
        '/Auth/Auth/Auth.php',
        '/DBA/DBA/Compatibility.php',
        '/DB_DataObject/DataObject/createTables.php',
        '/Forum/',
        '/HTML_AJAX/js/build.php',
        '/HTML_Page/Page/Doctypes.php',
        '/HTML_Page/Page/Namespaces.php',
        '/HTML_Page2/Page2/Doctypes.php',
        '/HTML_Page2/Page2/Namespaces.php',
        '/HTTP_SessionServer/SessionServer/SaveHandler.php',
        '/I18N/Messages/determineLanguage.inc.php',
        '/I18Nv2/.+/.+.php',
        '/Image_Graph/Graph/Config.php',
        '/Image_Graph/Graph/Constants.php',
        '/Image_Transform/Driver/.+.php',
        '/Image_Transform/imgtests/.+.php',
        '/MDB/MDB/reverse_engineer_xml_schema.php',
        '/Math_Fibonacci/Fibonacci/_fibonacciTable.php',
        '/Message/misc/.+.php',
        '/Net_GameServerQuery/GameServerQuery/Games.php',
        '/Net_SmartIRC/SmartIRC/defines.php',
        '/PEAR_Frontend_Gtk2/Gtk2/Checks.php',
        '/PHP_Compat/Compat/',
        '/PHP_CompatInfo/CompatInfo/',
*/
    );

    /**
    *   Returns a list of .php files in the given
    *   directory and its subdirectories.
    *   The files do not match self::$arBadFiles.
    *
    *   @param string   $strPackageDir      Directory to scan
    *   @param boolean  $bAbsolute          Return absolute file paths
    *                                        (or relative to package dir)
    *   @return array   Array with absolute file paths
    */
    public static function getFileList($strPackageDir, $bAbsolute = true)
    {
        if (!file_exists($strPackageDir) || !is_dir($strPackageDir)) {
            throw new Exception("Package directory does not exist: " . $strPackageDir);
        }

        if (substr($strPackageDir, -1) != '/') {
            $strPackageDir .= '/';
        }
        $strPath = getcwd();
        chdir($strPackageDir);

        $arFiles = preg_grep(
            '!(' . implode(self::$arBadFiles, '|') . ')!',
            glob('./' . '{*,*/*,*/*/*,*/*/*/*}.php', GLOB_BRACE),
            PREG_GREP_INVERT
        );

        foreach ($arFiles as $id => $strFile) {
            if ($bAbsolute) {
                $arFiles[$id] = $strPackageDir . substr($strFile, 2);
            } else {
                $arFiles[$id] = substr($strFile, 2);
            }
        }

        chdir($strPath);

        return $arFiles;
    }//public static function getFileList($strPackageDir, $bAbsolute = true)



    /**
    *   Tries to find classnames in a given file
    *
    *   @param string $strClassFile     .php filename
    *   @return array   Array of classnames defined in the file
    */
    public static function getClassnamesFromFilename($strClassFile)
    {
        //simple: open file and search for "class classname"
        $strContent = file_get_contents($strClassFile);

        if (preg_match_all('/' . "(?:\r|\n)" . '\\s*(?:abstract\\s+)?(?:final\\s+)?(?:[Cc]lass|interface)\\s+([A-Za-z0-9_]+)/', $strContent, $arMatches)) {
            return $arMatches[1];
        } else {
            return array();
        }
    }//public static function getClassnamesFromFilename($strClassFile)

}//class QA_Peardoc_Coverage_Classlist
?>