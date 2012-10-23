<?php
/*
 * This view outputs a json or jsonp representation of search results
 */

$json = array();

foreach ($locale1_strings as $key => $str) {
    $json[$key][$str] = htmlspecialchars_decode($tmx_target[$key], ENT_QUOTES);
}

$json = json_encode($json);
$mime = 'application/json';

if (isset($_GET['callback'])) {
    // JSONP with the defined callback
    $mime = 'application/javascript';
    $json = $_GET['callback'] . '(' . $json . ')';
}

header("access-control-allow-origin: *");
header("Content-type: {$mime}; charset=UTF-8");
exit($json);
