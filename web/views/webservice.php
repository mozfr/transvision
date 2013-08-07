<?php
/*
 * This view outputs a json or jsonp representation of search results
 */

namespace Transvision;

$json = array();
$callback = isset($_GET['callback']) ? $_GET['callback'] : false;

if ($check['search_type'] == 'strings') {
    foreach ($locale1_strings as $key => $str) {
        $json[$key][$str] = htmlspecialchars_decode($tmx_target[$key], ENT_QUOTES);
    }
}

if ($check['search_type'] == 'entities') {
    foreach ($entities as $key) {
        if (isset($tmx_target[$key])) {
            $json[$key][$tmx_source[$key]] = htmlspecialchars_decode($tmx_target[$key], ENT_QUOTES);
        } else {
            $json[$key][$tmx_source[$key]] = '';
        }
    }
}

echo Json::jsonOutput($json, $callback);
