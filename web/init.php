<?php
if (!$valid) return;

// Force error reporting
error_reporting(E_ALL);

// These values depend on the server
define('APPROOT', '/home/pascalc/transvision');
define('HG',  APPROOT .'/data/hg/');
define('TMX', APPROOT .'/TMX/');
define('VERSION', ' development');

// Locale detection
require_once 'classes/ChooseLocale.class.php';
$availableLocales = file(APPROOT . '/trunk.txt', FILE_IGNORE_NEW_LINES);
$l10nDetect = new tinyL10n\ChooseLocale($availableLocales);
$l10nDetect->setDefaultLocale('fr');
$l10nDetect->mapLonglocales = true;
$detectedLocale = $l10nDetect->getCompatibleLocale();

// Defined locale + rtl
$locale    = (isset($_GET['locale'])) ? $_GET['locale'] : $detectedLocale;
$direction = (in_array($locale, array('ar', 'fa', 'he'))) ? 'rtl' : 'ltr';

// webservice definition
if($webservice || isset($_GET['json'])) {
    $webservice = true;
}

// include the search options.
require_once 'search_options.php';

// include the cache files.
require_once 'cache_import.php';
