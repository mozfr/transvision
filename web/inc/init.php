<?php

/* routing */

// We separate path and query string for the dispatcher
$path  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

// Don't allow direct access to the file
if ($path == '/inc/init.php') {
    die('No direct access');
}

// Don't process non-PHP files
$file_ext = pathinfo($path);
if (isset($file_ext['extension']) && $file_ext['extension'] != 'php') {
    return false;
}

/* end routing */

$valid = true;
require_once 'functions.php';

// perf metrics
$time_start = getmicrotime();

/* These values depend on the server.
 * We store the application and TMX paths on an ini file shared with python
 */

// PHP >=5.4 syntax
// define('DATAROOT', parse_ini_file('config.ini')['root']);

$ini_array = parse_ini_file('config.ini');

define('DATAROOT',    $ini_array['root']);
define('HG',          $ini_array['local_hg'] . '/');
define('TMX',         DATAROOT .'/TMX/');
define('INSTALLROOT', $ini_array['install'] . '/');
define('WEBROOT',     INSTALLROOT . 'web/');
define('INC',         INSTALLROOT . 'web/inc/');
define('VIEWS',       INSTALLROOT . 'web/views/');
define('VERSION',     '1.5dev');

// variable to activate debug mode
$debug = (strstr(VERSION, 'dev') || isset($_GET['debug'])) ? true : false;

// Default body ID, can be overriden for CSS styling
if(isset($page)) return;
$page = 'default';

// page title
$title = 'Transvision glossary <a href="./changelog.php#v' . VERSION . '">' . VERSION . '</a>';


// for the changelog, we just want to include variables used by the template
if(isset($page) && $page == 'changelog') return;

// Locale detection
require_once WEBROOT .'classes/ChooseLocale.class.php';
$allLocales = file(INSTALLROOT . '/central.txt', FILE_IGNORE_NEW_LINES);
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

// optional source locale if we want to compare with another locale
if (isset($_GET['sourcelocale']) && in_array($_GET['sourcelocale'], $allLocales)) {
    $sourceLocale = $_GET['sourcelocale'];
} else {
    $sourceLocale = 'en-US';
}

// rtl
$direction = (in_array($locale, array('ar', 'fa', 'he'))) ? 'rtl' : 'ltr';

// webservice definition
if(isset($_GET['json']) && !isset($web_service)) {
    $web_service = true;
} else {
    $web_service = false;
}

// include the search options.
require_once INC . '/search_options.php';

// include the cache files.
require_once INC . 'cache_import.php';

// Start output buffering, we will output in a template
ob_start();

if(valid($web_service)) {
    // fonction de recherche
    require_once INC . 'recherche.php';

    foreach ($keys as $key => $chaine) {
        $ken[$key][$chaine] = htmlspecialchars_decode($tmx_target[$key], ENT_QUOTES);
    }
    foreach ($keys2 as $key => $chaine) {
        $kfr[$key][$chaine] = htmlspecialchars_decode($tmx_source[$key], ENT_QUOTES);
    }

    $json_en = json_encode($ken);
    $json_fr = json_encode($kfr);


    header('Content-type: application/json; charset=UTF-8');

    if (isset($_GET['callback'])) {
        if ($_GET['return_loc'] == 'loc') {
            echo $_GET['callback'] . '(' . $json_fr . ');';
        } else{
            echo $_GET['callback'] . '(' . $json_en . ');';
        }
    } else {
        if (isset($_GET['return_loc']) && $_GET['return_loc'] == 'loc'){
            echo $json_fr;
        } else {
            echo htmlspecialchars_decode($json_en, ENT_QUOTES);
        }
    }
    // end of webservice code
    // XXX: factorize more code with normal display
    exit;
}

// Base html
require_once VIEWS . 'search_form.php';

// fonction de recherche
if ($check['t2t']) {
    require_once VIEWS . 't2t.php';
} else {
    require_once INC . 'recherche.php';

    // result presentation
    if($recherche != '') {
        if ($check['ent']) {
            require_once VIEWS . 'results_ent.php';
        } else {
            require_once VIEWS . 'results.php';
        }
    }
}

$content = ob_get_contents();
ob_end_clean();

// display the page
require_once VIEWS .'template.php';
