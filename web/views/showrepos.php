<?php
namespace Transvision;

$strings = array();

$repo = (isset($_GET['repo']) && in_array($_GET['repo'], $repos))
        ? $_GET['repo']
        : 'gaia';

$channel_selector = '';

foreach ($repos as $val) {
    $ch = ($val == $repo) ? ' selected' : '';
    $channel_selector .= "\t<option" . $ch . " value=" . $val . ">" . $repos_nice_names[$val] . "</option>\n";
}

// Using a callback with strlen() avoids filtering out numeric strings with a value of 0
$strings['en-US'][$repo] = array_filter(Utils::getRepoStrings('en-US', $repo), 'strlen');
$gaia_locales = Utils::getFilenamesInFolder(TMX . $repo . '/');

// We don't want en-US in the repos
if ($key = array_search('en-US', $gaia_locales)) {
    unset($gaia_locales[$key]);
}

$string_count = array();

// Referen_ce locale count
$count_reference = count($strings['en-US'][$repo]);

foreach ($gaia_locales as $val) {
    $strings[$val][$repo] = array_filter(Utils::getRepoStrings($val, $repo), 'strlen');
    $string_count[$val] = array(
        'total'     => count($strings[$val][$repo]),
        'missing'   => count(array_diff_key($strings['en-US'][$repo], $strings[$val][$repo])),
        'identical' => count(array_intersect_assoc($strings['en-US'][$repo], $strings[$val][$repo])),
    );
    unset($strings[$val][$repo]);
}

$json = array();
$table = '
<style>td {text-align:right;} form[name="searchform"] { text-align: center; }</style>
<table>
<tr>
    <th>Locale</th>
    <th>Total</th>
    <th>Missing</th>
    <th>Translated</th>
    <th>Identical</th>
    <th>Completion</th>
    <th>Status estimate</th>
</tr>';

foreach ($string_count as $locale => $numbers) {

    $completion = $count_reference - $numbers['identical'] - $numbers['missing'];
    $completion = number_format($completion/$count_reference*100);

    if ($completion >= 99) {
        $confidence = 'Highest';
    } elseif ($completion >= 95) {
        $confidence = 'High';
    } elseif ($completion >= 90) {
        $confidence = 'High';
    } elseif ($completion >= 60) {
        $confidence = 'In progress';
    } elseif ($completion >= 50) {
        $confidence = 'Low';
    } elseif ($completion >= 30) {
        $confidence = 'very Low';
    } elseif ($completion >= 10) {
        $confidence = 'Barely started';
    } elseif ($completion >= 1) {
        $confidence = 'just started';
    } else {
        $confidence = 'No localization';
    }

    $json[$locale] = array(
        'total'      => $numbers['total'],
        'missing'    => $numbers['missing'],
        'translated' => ($numbers['total'] - $numbers['identical']),
        'identical'  => $numbers['identical'],
        'completion' => $completion,
        'confidence' => $confidence,
    );

    $table .=
    "<tr id=\"{$locale}\">
    <th>{$locale}</th>
    <td>{$numbers['total']}</td>
    <td>{$numbers['missing']}</td>"
    . '<td>' . ($numbers['total'] - $numbers['identical']) . '</td>'
    . "<td>{$numbers['identical']}</td>
    <td>{$completion} %</td>
    <td>{$confidence}</td>
    </tr>";

}

$table .= '</table>';


if (isset($_GET['json'])) {
    $callback = isset($_GET['callback']) ? $_GET['callback'] : false;
    die(Json::output($json, $callback));
} else {
    // Include the common simple search form
    include __DIR__ . '/simplesearchform.php';

    echo $table;
}
