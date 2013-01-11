<?php

$title = '<a href="/">Transvision</a> short usage stats';


// create a json file logging locale/number of requests
$stats = json_decode(file_get_contents(WEBROOT . 'stats.json'), true);
arsort($stats);

echo '<table>';
echo '<tr><th>Locale</th><th>Searches</th></tr>';
foreach ($stats as $k => $v) {
    echo "<tr><th>$k</th><td>$v</td></tr>";
}
echo '<tr><th>' . count($stats) . '</th><th>' . array_sum($stats) . '</th></tr>';
echo '</table>';
