<?php

// Bump this constant with each new release
const VERSION = '3.3dev';

// Constants for the project
define('DATA_ROOT',     $ini_array['root']);
define('HG',            $ini_array['local_hg'] . '/');
define('SVN',           $ini_array['local_svn'] . '/');
define('TMX',           DATA_ROOT . '/TMX/');
define('INSTALL_ROOT',  $ini_array['install'] . '/');
define('WEB_ROOT',      INSTALL_ROOT . 'web/');
define('APP_ROOT',      INSTALL_ROOT . 'app/');
define('INC',           APP_ROOT . 'inc/');
define('VIEWS',         APP_ROOT . 'views/');
define('MODELS',        APP_ROOT . 'models/');
define('CONTROLLERS',   APP_ROOT . 'controllers/');
define('CACHE',         INSTALL_ROOT . 'cache/');

// Special modes for the app
define('DEBUG', (strstr(VERSION, 'dev') || isset($_GET['debug'])) ? true : false);
define('JSON_API', (isset($_GET['json']) && !isset($web_service)) ? true : false);
define('NOCACHE', (isset($_GET['nocache'])) ? true : false);
