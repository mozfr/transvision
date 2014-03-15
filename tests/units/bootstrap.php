<?php
$ini_array = parse_ini_file(__DIR__ . '/../../web/inc/config.ini');
define('TMX', $ini_array['root'] . '/TMX/');
define('CACHE', __DIR__ . '/../testfiles/cache/');


require __DIR__.'/../../vendor/autoload.php';