<?php

// fonction de recherche
require_once INC . 'recherche.php';

$json_search_results = array();

foreach ($keys as $key => $str) {
    $json_search_results[$key][$str] = htmlspecialchars_decode($tmx_target[$key], ENT_QUOTES);
}

header('Content-type: application/json; charset=UTF-8');

echo htmlspecialchars_decode(json_encode($json_search_results), ENT_QUOTES);

exit;
