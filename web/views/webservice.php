<?php

$json = array();

foreach ($locale1_strings as $key => $str) {
    $json[$key][$str] = htmlspecialchars_decode($tmx_target[$key], ENT_QUOTES);
}

$json = json_encode($json);

header("access-control-allow-origin: *");

if (!isset($_GET['callback'])) {
    // JSON if no callback
    header('Content-type: application/json; charset=UTF-8');
} else {
    // JSONP with the defined callback
    header('Content-type: application/javascript; charset=UTF-8');
    $json = $_GET['callback'] . '(' . $json . ')';
}

exit($json);
