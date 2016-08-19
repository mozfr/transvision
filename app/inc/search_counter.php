<?php
namespace Transvision;

use Json\Json;

$json_data = new Json;

// Create a JSON file logging locale/number of requests
$local_filename = CACHE_PATH . 'stats_locales.json';
if (file_exists($local_filename)) {
    $stats = $json_data
        ->setURI($local_filename)
        ->fetchContent();
} else {
    $stats = [];
}

// Save JSON only if PHP was able to read the existing stats
if (is_array($stats)) {
    $stats[$locale] = array_key_exists($locale, $stats) ? $stats[$locale] += 1 : 1;
    $json_data->saveFile($stats, $local_filename);
} else {
    $logger->addError('stats_locales.json exists but couldn\'t extract a valid array from JSON');
}

// Create a JSON file logging search options to determine if some are unused
$local_filename = CACHE_PATH . 'stats_requests.json';
if (file_exists($local_filename)) {
    $stats = $json_data
        ->setURI($local_filename)
        ->fetchContent();
} else {
    $stats = [];
}

// Save JSON only if PHP was able to read the existing stats
if (is_array($stats)) {
    foreach ($check as $k => $v) {
        if (in_array($k, $search->getFormCheckboxes()) && $v == 1) {
            $stats[$k] = array_key_exists($k, $stats) ? $stats[$k] += 1 : 1;
        }

        if (in_array($k, array_diff($search->getFormSearchOptions(), $search->getFormCheckboxes()))) {
            $stats[$v] = array_key_exists($v, $stats) ? $stats[$v] += 1 : 1;
        }

        $json_data->saveFile($stats, $local_filename);
    }
} else {
    $logger->addError('stats_requests.json exists but couldn\'t extract a valid array from JSON');
}
unset($stats);
