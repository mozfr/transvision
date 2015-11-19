<?php
namespace Transvision;

use Json\Json;

// Create a JSON file logging locale/number of requests
$json_data = new Json;
$local_filename = CACHE_PATH . 'stats_locales.json';
$stats = $json_data
    ->setURI($local_filename)
    ->fetchContent();

$stats[$locale] = array_key_exists($locale, $stats) ? $stats[$locale] += 1 : 1;
$json_data->saveFile($stats, $local_filename);

// Create a JSON file logging search options to determine if some are unused
$local_filename = CACHE_PATH . 'stats_requests.json';
$stats = $json_data
    ->setURI($local_filename)
    ->fetchContent();

foreach ($check as $k => $v) {
    if (in_array($k, $form_checkboxes) && $v == 1) {
        $stats[$k] = array_key_exists($k, $stats) ? $stats[$k] += 1 : 1;
    }

    if (in_array($k, array_diff($form_search_options, $form_checkboxes))) {
        $stats[$v] = array_key_exists($v, $stats) ? $stats[$v] += 1 : 1;
    }

    $json_data->saveFile($stats, $local_filename);
}
unset($stats);
