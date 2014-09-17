<?php
define('TMX', realpath(__DIR__ . '/../testfiles/TMX/') . '/');
define('CACHE_PATH', realpath(__DIR__ . '/../testfiles/cache/') . '/');
define('INSTALL_ROOT',  realpath(__DIR__ . '/../') . '/');
define('TEST_FILES', realpath(__DIR__ . '/../testfiles/') . '/');
define('APP_SOURCES', realpath(__DIR__ . '/../testfiles/config') . '/');

mb_internal_encoding('UTF-8');

const DEBUG = true;
const CACHE_ENABLED = true;
const CACHE_TIME = 19200;

require __DIR__.'/../../vendor/autoload.php';
