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

// We may want to start speed and memory calculations here as well
if ($debug) {
    error_reporting(E_ALL);
}

// Utility functions
require_once INC . 'functions.php';

// include for search only and its json counterpart
if ($urls[$url['path']] == 'root' || valid($web_service)) {
    // Bootstrap l10n
    require_once INC . 'l10n-init.php';

    // Include Search Options
    require_once INC . 'search_options.php';

    // Import all strings for source and target locales
    require_once INC . 'cache_import.php';
}

if ($urls[$url['path']] == 'root' && valid($web_service)) {
    require_once INC . 'recherche.php';
    require_once INC . 'webservice.php';
    exit;
}

// Start output buffering, we will output in a template
ob_start();

// HTML body ID
$page = $urls[$url['path']];

// Page title
$title = 'Transvision glossary <a href="./news/#v' . VERSION . '">' . VERSION . '</a>';

// Base html
if ($url['path'] == 'stats') {
    // Include Search Options
    require_once INC . 'search_options.php';
    // Import all strings for source and target locales
    require_once INC . 'cache_import.php';
    require_once VIEWS . 'stats.php';
} else {
    require_once VIEWS . 'search_form.php';
}

// Search results process
if ($check['t2t']) {
    require_once VIEWS . 't2t.php';
} else {
    require_once INC . 'recherche.php';

    // result presentation
    if ($recherche != '') {
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
