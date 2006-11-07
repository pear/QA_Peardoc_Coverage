<?php
/**
*   Send mails to the devs telling them that their packages
*   miss end user documentation.
*/
require_once 'PEAR.php';
require_once 'Mail.php';
require_once 'Console/Getargs.php';
require_once 'Console/ProgressBar.php';

$config = array(
    'server' => array(
            'min' => 1,
            'max' => 1,
            'desc' => 'SMTP server'),
    'port' => array(
            'min' => 1,
            'max' => 1,
            'desc' => 'SMTP server port',
            'default' => 25),
    'user' => array(
            'min' => 1,
            'max' => 1,
            'desc' => 'SMTP user'),
    'password' => array(
            'min' => 1,
            'max' => 1,
            'desc' => 'SMTP password'),
    'debug' => array('short' => 'd',
            'max' => 0,
            'desc' => 'Switch to debug mode.'),
    'pretend' => array('short' => 'p',
            'max' => 0,
            'desc' => 'Display what would be done, but do not send any mails.'),
);

$args = Console_Getargs::factory($config);

if (PEAR::isError($args)) {
    if ($args->getCode() === CONSOLE_GETARGS_ERROR_USER) {
        echo Console_Getargs::getHelp($config, null, $args->getMessage())."\n";
    } else if ($args->getCode() === CONSOLE_GETARGS_HELP) {
        echo Console_Getargs::getHelp($config)."\n";
    }
    exit;
}

$bDebug         = $args->isDefined('d');
$bPretend       = $args->isDefined('p');
$arMailParams = array(
    'host'      => $args->getValue('server'),
    'port'      => $args->getValue('port'),
    'username'  => $args->getValue('user'),
    'password'  => $args->getValue('password'),
    'auth'      => true,
    'persist'   => true
);



$strMessage = <<<EOD
Dear PEAR developer,

Some of your packages in PEAR do not have end-user documentation
in the official PEAR manual. Since the best package is useless
without proper documentation, we ask you to write that for
the packages not having any. And do remember that the auto-generated
API-documentation does not replace end-user documentation as found
in the manual. Use case and examples as well as an introduction
to the way the package works is invaluable to the user wishing to use it.

Your packages missing documentation:
{PACKAGES}

If you have already written documentation located on an
external server, plase consider transferring it into the PEAR manual.
If that is not possible we ask you to create a link
in the PEAR manual pointing to it.

In case you are not a developer of one of the mentioned packages,
the information in the package.xml file is wrong.
Ask the lead of that package to correct the data.


Regards,
The PEAR QA team.
EOD;

if (!file_exists('missingdocs.serialized')) {
    echo <<<EOD
Please run examples/genMissingDocsPerDeveloper.php
and save the output into a file called
"missingdocs.serialized"
EOD;
    exit(1);
}

$arData = unserialize(file_get_contents('missingdocs.serialized'));

if (count($arData) == 0) {
    echo "All packages seem to be documented. Nothing to do here.\n";
    exit(0);
}

echo "Sending mails\n";
$bar = new Console_ProgressBar(' %fraction% [%bar%] ', '=>', ' ', 80, count($arData));
$mail = Mail::factory('smtp', $arMailParams);
if (PEAR::isError($mail)) {
    echo $mail->getMessage() . "\n";
    exit(2);
}

$nCurrentMail = 0;

foreach ($arData as $strDevEmail => $arUndocumented) {
    if ($strDevEmail == '???' || $strDevEmail == '') {
        continue;
    }

    $strPackages = implode(', ', $arUndocumented);
    $strMailText = str_replace('{PACKAGES}', $strPackages, $strMessage);

    if ($bDebug) {
        echo 'Mail to ' . $strDevEmail . ' with text:' . "\n" . $strMailText . "\n\n";
    } else {
        $bar->update(++$nCurrentMail);
    }
    if (!$bPretend) {
        $ret = $mail->send(
            $strDevEmail,
            array(
                'From'          => 'pear-qa@lists.php.net',
                'To'            => $strDevEmail,
                'Reply-To'      => 'pear-dev@lists.php.net',
                'User-Agent'    => 'QA_Peardoc_Coverage',
                'Subject'       => '[PEAR-QA] Missing package documentation reminder'
            ),
            $strMailText
        );
        if (PEAR::isError($ret)) {
            echo 'Could not send mail!' . "\n";
            echo $ret->getMessage() . "\n";
            exit(3);
        }
    }
}

echo "Done sending mails\n";
?>