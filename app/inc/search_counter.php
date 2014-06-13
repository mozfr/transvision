<?php
namespace Transvision;

// Create a JSON file logging locale/number of requests
$stats = Json::fetch(WEB_ROOT . 'stats.json');
$stats[$locale] = array_key_exists($locale, $stats) ?  $stats[$locale] += 1 : 1;
file_put_contents(WEB_ROOT . 'stats.json', json_encode($stats));

// Create a JSON file logging search options to determine if some are unused
$stats = Json::fetch(WEB_ROOT . 'stats_requests.json');

foreach ($check as $k => $v) {
    if (in_array($k, $form_checkboxes) && $v == 1) {
        $stats[$k] = array_key_exists($k, $stats) ? $stats[$k] += 1 : 1;
    }

    if (in_array($k, array_diff($form_search_options, $form_checkboxes))) {
        $stats[$v] = array_key_exists($v, $stats) ? $stats[$v] += 1 : 1;
    }

    file_put_contents(WEB_ROOT . 'stats_requests.json', json_encode($stats));
}
unset($stats);
