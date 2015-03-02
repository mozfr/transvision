<?php
define('TMX', realpath(__DIR__ . '/../testfiles/TMX/') . '/');
define('CACHE_PATH', realpath(__DIR__ . '/../testfiles/cache/') . '/');
define('INSTALL_ROOT',  realpath(__DIR__ . '/../') . '/');
define('TEST_FILES', realpath(__DIR__ . '/../testfiles/') . '/');
define('APP_SOURCES', realpath(__DIR__ . '/../testfiles/config') . '/');

// We always work with UTF8 encoding
mb_internal_encoding('UTF-8');

// Make sure we have a timezone set
date_default_timezone_set('Europe/Paris');

const DEBUG = true;
const CACHE_ENABLED = true;
const CACHE_TIME = 19200;

require __DIR__ . '/../../vendor/autoload.php';
