<?php
require_once 'QA/Peardoc/Coverage/Renderer.php';
require_once 'HTML/Table.php';

/**
*   Renders a list with developers, the number of packages
*   they maintain and the documented/undocumented ratio
*
*   @author Christian Weiske <cweisek@php.net>
*/
class QA_Peardoc_Coverage_Renderer_DeveloperList implements QA_Peardoc_Coverage_Renderer
{
    public static $colNotDocumented = '#F00';
    public static $colDocumented    = '#0F0';
    public static $arLevels = array(
        100 => 'Well done',
         90 => 'Not bad',
         50 => 'Should be better',
         30 => 'Shame on you'
    );



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
    *   Renders the given coverage array and
    *   returns the HTML.
    */
    public function render($arDoc)
    {
        $arMaintainer = self::getMaintainers($arDoc);

        $n = "\n";
        $out = '';
        $out .= '<?xml version="1.0" encoding="utf-8" ?>' . $n
            . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '
            . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

        $out .= '<html><head><title>PEAR Documentation coverage by developer</title></head><body>';
        $out .= '<table border="1"><caption>'
            . 'PEAR documentation coverage by developer '
            . date('Y-m-d H:i:s')
            . '</caption>' . $n;


        $arMaintainers = self::getMaintainers($arDoc);
        uasort($arMaintainers, array('self', 'compareMaintainers'));

        $out .= '<thead>'
            . '<tr><th>Place</th><th>Developer</th><th>Docced</th><th>Packages</th><th>Percentage</th>'
            . '<th>Undocumented Packages</th>'
            . '</tr>'
            . '</thead>' . $n;

        $nPlace = 0;
        $nNextNumber = 101;
        reset(self::$arLevels);

        foreach ($arMaintainers as $strUsername => $arMaintainer) {
            $num = $arMaintainer['docced'] / $arMaintainer['packages'];
            $glatt = intval($num * 100);

            if ($glatt < $nNextNumber) {
                $strLevel = current(self::$arLevels);
                $out .= '<tr><th colspan="6">' . $strLevel . '</th></tr>' . $n;
                next(self::$arLevels);
                $nNextNumber = key(self::$arLevels);
            }

            $arUndocumented = array();
            if ($glatt != 100) {
                foreach ($arMaintainer['packagelist'] as $strPackageName => $arPackage) {
                    if ($arPackage['*docid*'] === null) {
                        $arUndocumented[] = '<a href="http://pear.php.net/package/'
                            . $strPackageName . '">'
                            . $strPackageName
                            . '</a>';
                    }
                }
            }

            $out .= '<tr>'
                . '<td>' . ++$nPlace . '</td>'
                . '<td><a href="http://pear.php.net/user/' . $strUsername . '">' . $strUsername . '</a></td>'
                . '<td>' . $arMaintainer['docced'] . '</td>'
                . '<td>' . $arMaintainer['packages'] . '</td>'
                . '<td style="background-color:' . self::getColor($num) . '">'
                . number_format($num * 100, 2) . '%</td>'
                . '<td>' . implode(', ', $arUndocumented) . '</td>'
                . '</tr>' . $n
            ;
        }

        $out .= '</table>' . $n;


        $out .= '</body></html>';

        return $out;
    }//public function render($arDoc)



    /**
    *   Returns an array of package maintainer usernames,
    *   their email address, real name and the packages
    *   maintained by them. Also lists which packages are
    *   not docced, and which are.
    *
    *   @param array $arDoc     Array as passed to render() method.
    *   @return array   Array with the following data:
    *   [username] => array(
    *       [name]      => Real name
    *       [email]     => Email address
    *       [docced]    => # of documented packages
    *       [packages]  => # of packages at all
    *       [packagelist]   => array with package names (key)
    *                       value is reference to doc array
    *   )
    */
    public static function getMaintainers($arDoc)
    {
        $arMaintainers = array();
        foreach ($arDoc as $strCategory => &$arPackages) {
            foreach ($arPackages as $strPackageName => &$arPackage) {
                $strPath = $arPackage['*package*'];
                $strV1 = $strPath . '/package.xml';
                $strV2 = $strPath . '/package2.xml';

                if (file_exists($strV2)) {
                    $strPackageXmlPath = $strV2;
                } else {
                    $strPackageXmlPath = $strV1;
                }

                $arPackageMaintainers = self::getPackageMaintainers(
                    $strPackageXmlPath
                );

                foreach ($arPackageMaintainers as $strUsername => $arMaintainer) {
                    if (!isset($arMaintainers[$strUsername])) {
                        $arMaintainers[$strUsername] = $arMaintainer;
                        $arMaintainers[$strUsername]['packages'] = 0;
                        $arMaintainers[$strUsername]['docced']   = 0;
                        $arMaintainers[$strUsername]['packagelist'] = array();
                    }
                    ++$arMaintainers[$strUsername]['packages'];
                    $arMaintainers[$strUsername]['packagelist'][$strPackageName] = &$arPackage;
                    if ($arPackage['*docid*'] !== null) {
                        ++$arMaintainers[$strUsername]['docced'];
                    }
                }

            }
        }

        return $arMaintainers;
    }//public static function getMaintainers($arDoc)



    /**
    *   Reads the package maintainers from a package.xml (v1 and v2)
    *   file.
    *
    *   @param string $strPackageXmlPath    Path to a package.xml file
    *   @return array   Array with maintainers of following structure:
    *   [username] => array(
    *       [name]      => Real name
    *       [email]     => Email address
    *   )
    */
    public static function getPackageMaintainers($strPackageXmlPath)
    {
        if (!file_exists($strPackageXmlPath)) {
            throw new Exception('File does not exist: ' . $strPackageXmlPath);
        }

        $doc = new DOMDocument();
        if (!@$doc->load($strPackageXmlPath)) {
            return array();
            throw new Exception('Package xml is broken: ' . $strPackageXmlPath);
        }
        $xpath = new DOMXPath($doc);

        $arMaintainers = array();

        $pack = $doc->getElementsByTagName('package')->item(0);
        $strVersion = $pack->getAttribute('version');

        if ($strVersion == '') {
            $strVersion = '1.0';
        }

        if ($strVersion != '1.0' && $strVersion != '2.0') {
            throw new Exception('Unsupported package.xml version ' . $strVersion
                . ' in ' . $strPackageXmlPath);
        }

        if ($strVersion == '1.0') {
            //get maintainers
            $maintainers = $pack->getElementsByTagName('maintainer');
            foreach ($maintainers as $maintainer) {
                $strUsername = $maintainer->getElementsByTagName('user')->item(0)->textContent;
                $strRealname = $maintainer->getElementsByTagName('name')->item(0)->textContent;
                $strEmail    = $maintainer->getElementsByTagName('email')->item(0)->textContent;
                $arMaintainers[$strUsername] = array(
                    'username'  => $strUsername,
                    'name'      => $strRealname,
                    'email'     => $strEmail
                );
            }
        } else {
            //v2
            $maintainers = $pack->getElementsByTagName('lead');
            foreach ($maintainers as $maintainer) {
                if ($maintainer->getElementsByTagName('active')->item(0)->textContent != 'yes') {
                    continue;
                }
                $strUsername = $maintainer->getElementsByTagName('user')->item(0)->textContent;
                $strRealname = $maintainer->getElementsByTagName('name')->item(0)->textContent;
                $strEmail    = $maintainer->getElementsByTagName('email')->item(0)->textContent;
                $arMaintainers[$strUsername] = array(
                    'username'  => $strUsername,
                    'name'      => $strRealname,
                    'email'     => $strEmail
                );
            }
            $maintainers = $pack->getElementsByTagName('developer');
            foreach ($maintainers as $maintainer) {
                if ($maintainer->getElementsByTagName('active')->item(0)->textContent != 'yes') {
                    continue;
                }
                $strUsername = $maintainer->getElementsByTagName('user')->item(0)->textContent;
                $strRealname = $maintainer->getElementsByTagName('name')->item(0)->textContent;
                $strEmail    = $maintainer->getElementsByTagName('email')->item(0)->textContent;
                $arMaintainers[$strUsername] = array(
                    'username'  => $strUsername,
                    'name'      => $strRealname,
                    'email'     => $strEmail
                );
            }
        }

        return $arMaintainers;
    }//public static function getPackageMaintainers($strPackageXmlPath)



    public static function compareMaintainers($m1, $m2)
    {
        $v1 = $m1['docced'] / $m1['packages'];
        $v2 = $m2['docced'] / $m2['packages'];
        if ($v1 == $v2) {
            return strcasecmp($m1['username'], $m2['username']);
        }
        return ($v1 > $v2) ? -1 : 1;
    }//public static function compareMaintainers($m1, $m2)

}//class QA_Peardoc_Coverage_Renderer_DeveloperList implements QA_Peardoc_Coverage_Renderer
?>