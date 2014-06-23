<?php
define('TMX', realpath(__DIR__ . '/../testfiles/TMX/') . '/');
define('CACHE_PATH', realpath(__DIR__ . '/../testfiles/cache/') . '/');
define('INSTALL_ROOT',  realpath(__DIR__ . '/../') . '/');
const DEBUG = true;
const CACHE_ENABLED = true;
const CACHE_TIME = 19200;

require __DIR__.'/../../vendor/autoload.php';
