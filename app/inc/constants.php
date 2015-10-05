<?php

// Bump this constant with each new release
const VERSION = '3.9dev';

// Constants for the project
define('DATA_ROOT',     realpath($server_config['root']) . '/');
define('HG',            realpath($server_config['local_hg']) . '/');
define('SVN',           realpath($server_config['local_svn']) . '/');
define('GIT',           realpath($server_config['local_git']) . '/');
define('TMX',           DATA_ROOT . 'TMX/');
define('INSTALL_ROOT',  realpath($server_config['install']) . '/');
define('APP_SOURCES',   realpath($server_config['config']) . '/sources/');
define('WEB_ROOT',      INSTALL_ROOT . 'web/');
define('APP_ROOT',      INSTALL_ROOT . 'app/');
define('INC',           APP_ROOT . 'inc/');
define('VIEWS',         APP_ROOT . 'views/');
define('MODELS',        APP_ROOT . 'models/');
define('CONTROLLERS',   APP_ROOT . 'controllers/');
define('CACHE_ENABLED', isset($_GET['nocache']) ? false : true);
define('CACHE_PATH',    INSTALL_ROOT . 'cache/');
define('APP_SCHEME',    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://');

if (file_exists(CACHE_PATH . 'lastdataupdate.txt')) {
    define('CACHE_TIME',  time() - filemtime(CACHE_PATH . 'lastdataupdate.txt'));
} else {
    // 05h20 cache (because we extract data every 6h and extraction lasts 25mn)
    define('CACHE_TIME',  19200);
}

// Special modes for the app
define('DEBUG',     strstr(VERSION, 'dev') || isset($_GET['debug']));
define('LOCAL_DEV', isset($server_config['dev']) && $server_config['dev']);

// Set perf_check=true in config.ini to log page time generation and memory used while in DEBUG mode
define('PERF_CHECK', isset($server_config['perf_check']) ? $server_config['perf_check'] : false);
