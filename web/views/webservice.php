<?php

$json_search_results = array();

foreach ($locale1_strings as $key => $str) {
    $json_search_results[$key][$str] = htmlspecialchars_decode($tmx_target[$key], ENT_QUOTES);
}

header('Content-type: application/json; charset=UTF-8');
header("access-control-allow-origin: *");
echo json_encode($json_search_results);
exit;
