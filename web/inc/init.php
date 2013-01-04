<?php

mb_internal_encoding("UTF-8");

// These values depend on the server.
// We store the application and TMX paths in an ini file shared with Python
$ini_array = parse_ini_file(__DIR__ . '/config.ini');
define('DATAROOT', $ini_array['root']);
define('HG', $ini_array['local_hg'] . '/');
define('TMX', DATAROOT .'/TMX/');
define('INSTALLROOT', $ini_array['install'] . '/');
define('WEBROOT', INSTALLROOT . 'web/');
define('INC', INSTALLROOT . 'web/inc/');
define('VIEWS', INSTALLROOT . 'web/views/');

// Bump this constant with each new release
const VERSION = '1.9dev';

// Global Variables used on the site
$debug = (strstr(VERSION, 'dev') || isset($_GET['debug'])) ? true : false;
$web_service = (isset($_GET['json']) && !isset($web_service)) ? true : false;

// We may want to start speed and memory calculations here as well
if ($debug) {
    error_reporting(E_ALL);
}

// Autoloading of composer classes
require_once WEBROOT . 'vendor/autoload.php';

// Logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$logger = new Logger(VERSION);
$logger->pushHandler(new StreamHandler(__DIR__ . '/transvision.log', Logger::DEBUG));

// Utility functions
require_once INC . 'functions.php';

// Dispatch urls
require_once INC . 'dispatcher.php';


