<?php
namespace Transvision;

// Page title
$title = '<a href="/" id="transvision-title">Transvision - GAIA status</a> <a href="/news/#v' . VERSION . '">' . VERSION . '</a>';
require_once WEBROOT .'inc/l10n-init.php';


$strings = array();
$repo = 'gaia';

$strings['en-US'][$repo] = array_filter(Utils::getRepoStrings('en-US', $repo));
$gaiaLocales = Utils::getFilenamesInFolder(TMX . $repo . '/');

// We don't want en-US in the repos
if ($key = array_search('en-US', $gaiaLocales)) {
    unset($gaiaLocales[$key]);
}

$stringCount = array();

// Reference locale count
$countReference = count($strings['en-US'][$repo]);

foreach ($gaiaLocales as $val) {
    $strings[$val][$repo] = array_filter(Utils::getRepoStrings($val, $repo));
    //~ $strings[$val][$repo] = Utils::getRepoStrings($val, $repo);

    $stringCount[$val]['total'] = count($strings[$val][$repo]);
    $stringCount[$val]['missing'] = count(array_diff_key($strings['en-US'][$repo], $strings[$val][$repo]));
    $stringCount[$val]['identical'] = count(array_intersect_assoc($strings['en-US'][$repo], $strings[$val][$repo]));
    unset($strings[$val][$repo]);
}
echo '<style>td {text-align:right;}</style>';
echo '<table>';
echo '<tr>
    <th>Locale</th>
    <th>Total</th>
    <th>Missing</th>
    <th>Translated</th>
    <th>Identical</th>
    <th>Completion</th>
    <th>Confidence</th>
    </tr>';

foreach ($stringCount as $locale => $numbers) {
    echo "<tr id=\"{$locale}\">";
    echo "<th>{$locale}</th>";
    echo "<td>{$numbers['total']}</td>";
    echo "<td>{$numbers['missing']}</td>";
    echo '<td>' . ($numbers['total'] - $numbers['identical']) . '</td>';
    echo "<td>{$numbers['identical']}</td>";
    $completion = $countReference - $numbers['identical'] - $numbers['missing'];
    $completion = number_format($completion/$countReference*100);
    echo "<td>{$completion} %</td>";
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
    echo "<td>{$confidence}</td>";
    echo '</tr>';
}
echo '</table>';
