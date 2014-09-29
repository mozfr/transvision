<?php

// Bump this constant with each new release
const VERSION = '3.5';

// Constants for the project
define('DATA_ROOT',     $server_config['root']);
define('HG',            $server_config['local_hg'] . '/');
define('SVN',           $server_config['local_svn'] . '/');
define('TMX',           DATA_ROOT . '/TMX/');
define('INSTALL_ROOT',  $server_config['install'] . '/');
define('APP_SOURCES',   $server_config['config'] . '/sources/');
define('WEB_ROOT',      INSTALL_ROOT . 'web/');
define('APP_ROOT',      INSTALL_ROOT . 'app/');
define('INC',           APP_ROOT . 'inc/');
define('VIEWS',         APP_ROOT . 'views/');
define('MODELS',        APP_ROOT . 'models/');
define('CONTROLLERS',   APP_ROOT . 'controllers/');
define('CACHE_ENABLED', isset($_GET['nocache']) ? false : true);
define('CACHE_PATH',    INSTALL_ROOT . 'cache/');

if (file_exists(CACHE_PATH . 'lastdataupdate.txt')) {
    define('CACHE_TIME',  time() - filemtime(CACHE_PATH . 'lastdataupdate.txt'));
} else {
    // 05h20 cache (because we extract data every 6h and extraction lasts 25mn)
    define('CACHE_TIME',  19200);
}

// Special modes for the app
define('DEBUG', (strstr(VERSION, 'dev') || isset($_GET['debug'])) ? true : false);

// Set to True to log page time generation and memory used while in DEBUG mode
const PERF_CHECK = false;
