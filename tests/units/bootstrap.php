<?php
$ini_array = parse_ini_file(__DIR__ . '/../../app/config/config.ini');
define('TMX', $ini_array['root'] . '/TMX/');
define('CACHE_PATH', realpath(__DIR__ . '/../testfiles/cache/') . '/');

require __DIR__.'/../../vendor/autoload.php';