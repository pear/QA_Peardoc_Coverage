<?php
require_once 'QA/Peardoc/Coverage/ClassList.php';
require_once 'QA/Peardoc/Coverage/MethodList.php';

/*
<!-- missing entities -->
<!ENTITY copy "(C)">
<!ENTITY agrave "">
<!ENTITY dollar "$">
<!ENTITY eacute "">
<!ENTITY euro "€">
*/

/**
*   Simple peardoc coverage analysis.
*   Compares the classes and methods in PEAR packages
*   with the PEAR documentation, trying to find
*   out which packages aren't documented at all,
*   where documentation of parts is lacking or
*   which packages are fully documented.
*
*   IDs in peardoc:
*   ---------------
*   $packagename has "_" replaced with "-"
*
*   = Class:
*   package.$category.$packagename
*       package.html.html-form
*   package.$category.$shortpackagename
*       package.gtk.filedrop
*
*   = Methods:
*   package.$category.$packagename.$methodname
*       package.html.html-template-it.show
*       package.html.html-template-it.show.desc
*       package.html.html-template-it.show.parameter
*       package.html.html-template-it.show.throws
*   package.$category.$packagename.$classname.$methodname
*       package.html.html-quickform.html-quickform.addelement
*
*   = Constructor:
*   package.$category.$packagename.constructor
*       package.gtk2.entrydialog.constructor
*   package.$category.$packagename.$classname.$classname
*       package.datetime.calendar.calendar-day.calendar-day
*
*
*   TODO:
*   - differentiate between stable and unstable packages
*       (read package.xml)
*
*   @author Christian Weiske <cweiske@php.net>
*/
class QA_Peardoc_Coverage
{
    /**
    *   Special category associations.
    *
    *   key: package name
    *   value: category name
    */
    public static $arCategoryAssociation = array(
        'benchmark'                 => 'benchmarking',
        'calendar'                  => 'datetime',
        'games_chess'               => 'structures',
        'genealogy_gedcom'          => 'fileformats',
        'fsm'                       => 'processing',
        'i18nv2'                    => 'internationalization',
        'inline_c'                  => 'php',
        'math_numbers'              => 'numbers',
        'message'                   => 'encryption',
        'mime_type'                 => 'tools',
        'ole'                       => 'structures',
        'pager'                     => 'html',
        'pager_sliding'             => 'html',
        'pecl_gen'                  => 'php',
        'phpdoc'                    => 'php',
        'phpdocumentor'             => 'php',
        'safe_html'                 => 'html',
        'search_mnogosearch'        => 'tools',
        'selenium'                  => 'testing',
        'spreadsheet_excel_writer'  => 'fileformats',
        'sql_parser'                => 'database',
        'translation'               => 'internationalization',
        'translation2'              => 'internationalization',
        'tree'                      => 'structures',
        'xml_rpc'                   => 'webservices',
        'xml_rpc2'                  => 'webservices',
    );

    /**
    *   List with package category name => Doc category name
    *   associations.
    *
    *   Doc category assignments may be arrays of strings.
    *
    *   @var array
    */
    public static $arCategoryDocNames = array(
        'archive'       => 'fileformats',
        'auth'          => 'authentication',
        'cache'         => 'caching',
        'codegen'       => 'tools',
        'config'        => 'configuration',
        'contact'       => 'fileformats',
        'crypt'         => 'encryption',
        'date'          => 'datetime',
        'db'            => 'database',
        'dba'           => 'database',
        'file'          => array('fileformats', 'filesystem'),
        'i18n'          => 'internationalization',
        'image'         => 'images',
        'liveuser'      => 'authentication',
        'log'           => 'logging',
        'mdb'           => 'database',
        'mdb2'          => 'database',
        'mp3'           => 'fileformats',
        'net'           => 'networking',
        'rdf'           => 'semanticweb',
        'services'      => 'webservices',
        'soap'          => 'webservices',
        'stream'        => 'streams',
    );

    /**
    *   Lowercase package names that should be ignored.
    *   @var array
    */
    public static $arIgnoredPackages = array(
        'forum',
        'html_oohform',
        'installphars',
        'perm_liveuser',
        'xml_annotea'
    );



    /**
    *   Creates a new coverage checker instance.
    *
    *   @param string $strManualPath    Full path to the manual.xml file
    *                                    of the pear documentation.
    */
    public function __construct($strManualPath, $strPearDir)
    {
        $this->strManualPath = $strManualPath;
        $this->strPearDir    = $strPearDir;
    }//public function __construct($strManualPath, $strPearDir)



    /**
    *   Generates the coverage analysis
    *
    *   @return array   Documentation coverage array. Pass it to a render() method.
    */
    public function generateCoverage()
    {
        $this->loadXmlDocument($this->strManualPath);

        /*
        *   This is an array with doc statistics.
        *   key:   category
        *   value: array
        *       key:   package name
        *       value: array
        *           special key:   '*docid*'
        *           special value: documentation id
        *                           NULL if no doc
        *           special key:   '*path*'
        *           special value: path to package cvs
        *           key:   classname
        *           value: array, NULL if no doc
        *               key:   methodname
        *               value: ??
        */
        $arDoc = array();

        foreach (self::getPackageList($this->strPearDir)
                    as $strPackageDir => $strPackage
        ) {
            if (in_array(strtolower($strPackage), self::$arIgnoredPackages)) {
                continue;
            }

            $arCategories = self::getCategory($strPackage);
            foreach ($arCategories as $strCategory) {
                $strId = $this->getPackageDocId($strPackage, $strCategory);
                if ($strId !== null) {
                    break;
                }
            }

            if ($strId === null) {
                $arDoc[$strCategory][$strPackage] = array(
                    '*docid*'   => null,
                    '*package*' => $strPackageDir
                );
            } else {
                $arDoc[$strCategory][$strPackage] =
                    $this->getPackageCoverage($strPackage, $strCategory, $strId, $strPackageDir);
            }
        }

        ksort($arDoc);
        foreach ($arDoc as &$arPackages) {
            ksort($arPackages);
        }

        $arDoc['*date*'] = time();

        return $arDoc;
    }//public function generateCoverage()



    /**
    *   Renders the coverage analysis into a viewable format.
    *
    *   @param array $arCoverage    Coverage analysis array from generateCoverage()
    *   @param string $strRenderer  Classname of the renderer
    *
    *   @return string  Viewable coverage analysis.
    */
    public static function renderCoverage(
        $arCoverage,
        $strRenderer = 'QA_Peardoc_Coverage_Renderer_SimplePackageList'
    ) {
        if (!class_exists($strRenderer)) {
            require_once str_replace('_', '/', $strRenderer) . '.php';
        }

        return call_user_func(
            array($strRenderer, 'render'),
            $arCoverage
        );
    }//public static function renderCoverage(..)



    /**
    *   Loads the given xml file into a DOM document.
    *
    *   @param string $strManualPath    Full path to the manual.xml file
    *                                    of the pear documentation.
    *   @return boolean true if all is ok.
    */
    protected function loadXmlDocument($strManualPath)
    {
        if (!file_exists($strManualPath)) {
            throw new Exception('Manual file does not exist: ' . $strManualPath);
        }
        $strPath = getcwd();
        chdir(dirname($strManualPath));

        $this->doc = new DOMDocument();
        $this->doc->resolveExternals = true;
        $this->doc->substituteEntities = true;
        if ($this->doc->load($strManualPath)) {
            $this->xpath = new DOMXPath($this->doc);
            chdir($strPath);
            return true;
        } else {
            chdir($strPath);
            throw new Exception('manual XML could not be loaded.');
        }
    }//protected function loadXmlDocument($strManualPath)



    /**
    *   Returns a list of packages and their paths
    *
    *   @param string $strPearDir   PEAR CVS directory
    *   @return array   Array with package dir as key and package name as value
    */
    public static function getPackageList($strPearDir)
    {
        $arPackages = array();

        if (substr($strPearDir, -1) != '/') {
            $strPearDir .= '/';
        }
        foreach (
            glob($strPearDir . '*/packag{e,e2}.xml', GLOB_BRACE)
            as $strPackageFilePath
        ) {
            $strPackageDir = substr($strPackageFilePath, 0, strrpos($strPackageFilePath, '/'));
            $arPackages[$strPackageDir] = basename($strPackageDir);
        }

        asort($arPackages);
        return $arPackages;
    }//public static function getPackageList($strPearDir)



    /**
    *   Returns the category name for the package.
    *
    *   @param string $strPackage   Package name (e.g. Gtk2_EntryDialog)
    *   @return string              Array with category names (lowercase)
    */
    public static function getCategory($strPackage)
    {
        $strPackage = strtolower($strPackage);

        if (isset(self::$arCategoryAssociation[$strPackage])) {
            $strCategory = self::$arCategoryAssociation[$strPackage];
        } else {
            $nPos = strpos($strPackage, '_');
            if ($nPos === false) {
                $strCategory = $strPackage;
            } else {
                $strCategory = substr($strPackage, 0, $nPos);
            }
        }

        if (isset(self::$arCategoryDocNames[$strCategory])) {
            $arCategories = self::$arCategoryDocNames[$strCategory];
        } else {
            $arCategories = array($strCategory);
        }
        if (!is_array($arCategories)) {
            $arCategories = array($arCategories);
        }
        $arCategories = array_map('strtolower', $arCategories);

        return $arCategories;
    }//public static function getCategory($strPackage)



    /**
    *   Returns the ID of the XML node of the package
    *   in the pear documentation.
    *
    *   @param string $strPackage   Package name
    *   @param string $strCategory  Category name, gotten from self::getCategory()
    *
    *   @return string  The id, or NULL if not found -> not documented.
    */
    public function getPackageDocId($strPackage, $strCategory)
    {
        $strCategory       = strtolower($strCategory);
        $strPackageIdName  = strtolower(str_replace('_', '-', $strPackage));
        $strPackageIdName2 = strtolower(str_replace('_', '-',
                                    substr(
                                        $strPackage,
                                        strpos($strPackage, '_') + 1
                                    )
                             ));

        $strId  = 'package.' . $strCategory . '.' . $strPackageIdName;
        $strId2 = 'package.' . $strCategory . '.' . $strPackageIdName2;
        $strId3 = 'core.'    . $strCategory . '.' . $strPackageIdName;
//echo $strId . "|" . $strId2 . "|" . $strId3 . "\n";

        if ($this->existsId($strId)) {
            return $strId;
        } else if ($this->existsId($strId2)) {
            return $strId2;
        } else if ($this->existsId($strId3)) {
            return $strId3;
        } else {
            return null;
        }
    }//public function getPackageDocId($strPackage)



    /**
    *   Checks if an id exists in the manual
    *
    *   @param string $strId    xml id="" to check
    *   @return boolean     True if it exists, false if not
    */
    public function existsId($strId)
    {
        //echo $strId;
        return $this->doc->getElementById($strId) != null;
    }//public function existsId($strId)



    /**
    *   Creates the documentation coverage for the given package.
    *
    *   A class is considered documented if either an ID
    *   /baseid + "." + classname/ exists, or the classname
    *   is surrounded by <classname> tags at least once.
    *
    *   Functions/methds are considered as documented if
    *   they are mentioned inside a <function> tag or
    *   an id like /baseid + "." + classname + "." + functionname/
    *   exists.
    *
    *   @param string   $strPackage     Package name
    *   @param string   $strCategory    Category (lowercase)
    *   @param string   $strBaseId      Base documentation id attribute
    *   @param string   $strPackageDir  Package directory
    *
    *   @return array   Array with doc coverage information
    */
    public function getPackageCoverage($strPackage, $strCategory, $strBaseId, $strPackageDir)
    {
        $arDoc = array(
            '*docid*'   => $strBaseId,
            '*package*' => $strPackageDir
        );

        $baseElement = $this->doc->getElementById($strBaseId);
        //find <classname> elements
        $arDocClasses = array();
        foreach ($baseElement->getElementsByTagName('classname') as $classElement) {
            $arDocClasses[$classElement->nodeValue] = true;
        }

        $arClasses = array();
        foreach (
            QA_Peardoc_Coverage_ClassList::getFileList($strPackageDir)
            as $strClassFile
        ) {
            $arClasses = array_merge(
                $arClasses,
                QA_Peardoc_Coverage_MethodList::getMethods($strClassFile)
            );
        }//foreach file

        ksort($arClasses);

        foreach ($arClasses as $strClassName => $arMethods) {
            if ($strClassName[0] == '*') {
                continue;
            }
            //Check if class is documented
            $strClassDocId = $strBaseId . '.' . strtolower(str_replace('_', '-', $strClassName));

            if (!isset($arDocClasses[$strClassName]) && !$this->existsId($strClassDocId)) {
                //class is not documented
                $arDoc[$strClassName] = null;
                continue;
            }

            $arDocMethods = array();
            foreach ($baseElement->getElementsByTagName('function') as $funcElement) {
                $arDocMethods[$funcElement->nodeValue] = true;
            }

            $arDoc[$strClassName] = array();

            //check if methods exist
            foreach ($arMethods as $strMethod => $bDocBlock) {
                //omit constructors
                if ($strMethod == $strClassName || $strMethod == '__construct') {
                    continue;
                }

                if (isset($arDocMethods[$strMethod])) {
                    //first check if the method is in a <function> tag
                    $arDoc[$strClassName][$strMethod] = true;
                } else {
                    //then check if the method has its own section
                    $strMethodDocId = $strClassDocId . '.' . strtolower(str_replace('_', '-', $strMethod));

                    $arDoc[$strClassName][$strMethod] = $this->existsId($strMethodDocId);
                }
            }//foreach method
        }//foreach class

        return $arDoc;
    }//public function getPackageCoverage($strPackage, $strCategory, $strBaseId, $strPackageDir)

}//class QA_Peardoc_Coverage
?>