<?php
mb_internal_encoding("UTF-8");

// We store the application and TMX paths in an ini file shared with Python
$ini_array = parse_ini_file(__DIR__ . '/config.ini');

// Load all constants for the application
require_once __DIR__ . '/constants.php';

// We may want to start speed and memory calculations here as well
if (DEBUG) {
    error_reporting(E_ALL);
}

// Autoloading of classes (both /vendor and /classes)
require_once WEBROOT . 'vendor/autoload.php';

// Logging
//~ use Monolog\Logger;
//~ use Monolog\Handler\StreamHandler;
//~ $logger = new Logger(VERSION);
//~ $logger->pushHandler(new StreamHandler(__DIR__ . '/transvision.log', Logger::DEBUG));

// Dispatch urls
require_once INC . 'dispatcher.php';
