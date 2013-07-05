<?php

// Bump this constant with each new release
const VERSION = '2.7';

// Constants for the project
define('DATAROOT', $ini_array['root']);
define('HG', $ini_array['local_hg'] . '/');
define('TMX', DATAROOT . '/TMX/');
define('INSTALLROOT', $ini_array['install'] . '/');
define('WEBROOT', INSTALLROOT . 'web/');
define('INC', INSTALLROOT . 'web/inc/');
define('VIEWS', INSTALLROOT . 'web/views/');
define('CACHE', INSTALLROOT . 'web/cache/');

// Special modes for the app
define('DEBUG', (strstr(VERSION, 'dev') || isset($_GET['debug'])) ? true : false);
define('WEBSERVICE', (isset($_GET['json']) && !isset($web_service)) ? true : false);
