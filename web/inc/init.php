<?php
include_once 'functions.php';
if (!valid($valid)) return;


/* These values depend on the server.
 * We store the application and TMX paths on an ini file shared with python
 */

// PHP >=5.4 syntax
// define('DATAROOT', parse_ini_file('config.ini')['root']);

$ini_array = parse_ini_file('config.ini');
define('DATAROOT', $ini_array['root']);
define('HG',  $ini_array['local_hg'] . '/');
define('TMX', DATAROOT .'/TMX/');
define('VERSION', '1.3dev');

$title = 'Transvision glossary <a href="./changelog.php#v' . VERSION . '">' . VERSION . '</a>';

// for the changelog, we just want to include variables used by the template
if(isset($page) && $page == 'changelog') return;

// Locale detection
require_once 'classes/ChooseLocale.class.php';
$allLocales = file(DATAROOT . '/trunk.txt', FILE_IGNORE_NEW_LINES);
$l10nDetect = new tinyL10n\ChooseLocale($allLocales);
$l10nDetect->setDefaultLocale('fr');
$l10nDetect->mapLonglocales = true;
$detectedLocale = $l10nDetect->getCompatibleLocale();

// Defined locale + rtl
if (isset($_GET['locale']) && in_array($_GET['locale'], $allLocales)) {
    $locale = $_GET['locale'];
} else {
    $locale = $detectedLocale;
}

$direction = (in_array($locale, array('ar', 'fa', 'he'))) ? 'rtl' : 'ltr';

// webservice definition
if(isset($_GET['json']) && !isset($web_service)) {
    $web_service = true;
} else {
    $web_service = false;
}

// include the search options.
require_once 'search_options.php';

// include the cache files.
require_once 'cache_import.php';
