<?php

// These values depend on the server. We store the application and TMX paths in an ini file shared with Python
$ini_array = parse_ini_file(__DIR__ . '/config.ini');
define('DATAROOT',    $ini_array['root']);
define('HG',          $ini_array['local_hg'] . '/');
define('TMX',         DATAROOT .'/TMX/');
define('INSTALLROOT', $ini_array['install'] . '/');
define('WEBROOT',     INSTALLROOT . 'web/');
define('INC',         INSTALLROOT . 'web/inc/');
define('VIEWS',       INSTALLROOT . 'web/views/');
define('VERSION',     '1.6dev');

// Global Variables used on the site
$debug = (strstr(VERSION, 'dev') || isset($_GET['debug'])) ? true : false;
$web_service = (isset($_GET['json']) && !isset($web_service)) ? true : false;

if($debug) {
    error_reporting(E_ALL);
}

// Utility functions
require_once __DIR__ . '/functions.php';

// Bootstrap l10n
require_once __DIR__ . '/l10n-init.php';

// Include Search Options
require_once INC . '/search_options.php';

// Import all strings for source and target locales
require_once INC . '/cache_import.php';

// Start output buffering, we will output in a template
ob_start();

if(valid($web_service)) {
    // fonction de recherche
    require_once INC . 'recherche.php';

    $ken = array();
    $kfr = array();
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

// HTML body ID
$page = array_search($url['path'], $urls);

// Page title
$title = 'Transvision glossary <a href="./changelog/#v' . VERSION . '">' . VERSION . '</a>';

// Base html
if($url['path'] == 'stats2') {
    require_once VIEWS . 'stats.php';
} else {
    require_once VIEWS . 'search_form.php';
}
// fonction de recherche
if ($check['t2t']) {
    require_once VIEWS . 't2t.php';
} else {
    require_once INC . 'recherche.php';

    // result presentation
    if($recherche != '') {
        require_once WEBROOT .'classes/ShowResults.class.php';
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
