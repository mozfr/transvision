<?php
namespace Transvision;

// Get locales/number of requests
$stats = Json::fetchJson(WEBROOT . 'stats.json');
arsort($stats);

echo '<table>';
echo '<tr><th>Locale</th><th>Searches</th></tr>';
foreach ($stats as $k => $v) {
    echo "<tr><th>$k</th><td>$v</td></tr>";
}
echo '<tr><th>' . count($stats) . '</th><th>' . array_sum($stats) . '</th></tr>';
echo '</table>';

unset($stats);

// Get use of options
$stats = Json::fetchJson(WEBROOT . 'stats_requests.json');
arsort($stats);

echo '<table>';
echo '<tr><th>Option</th><th>Searches</th></tr>';
foreach ($stats as $k => $v) {
    echo "<tr><th>$k</th><td>$v</td></tr>";
}
echo '</table>';

unset($stats);
