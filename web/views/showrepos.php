<?php
namespace Transvision;

// Page title
$title = '<a href="/" id="transvision-title">Transvision - repository global status</a> <a href="/news/#v' . VERSION . '">' . VERSION . '</a>';

$strings = array();

$repo = (isset($_GET['repo']) && in_array($_GET['repo'], $repos))
        ? $_GET['repo']
        : 'gaia';

$chanSelector = '';

foreach ($repos as $val) {
    $ch = ($val == $repo) ? ' selected' : '';
    $chanSelector .= "\t<option" . $ch . " value=" . $val . ">" . $val . "</option>\n";
}

$form = <<<FORM
<form name="searchform" method="get" action="">
    <fieldset id="main">
        <fieldset>
            <legend>Repository</legend>
            <select name='repo'>
            <?=$chanSelector?>
            </select>
        </fieldset>
        <input type="submit" value="Go" alt="Go" />
    </fieldset>
 </form>
FORM;

// Using a callback with strlen() avoids filtering out numeric strings with a value of 0
$strings['en-US'][$repo] = array_filter(Utils::getRepoStrings('en-US', $repo), 'strlen');
$gaiaLocales = Utils::getFilenamesInFolder(TMX . $repo . '/');

// We don't want en-US in the repos
if ($key = array_search('en-US', $gaiaLocales)) {
    unset($gaiaLocales[$key]);
}

$stringCount = array();

// Reference locale count
$countReference = count($strings['en-US'][$repo]);

foreach ($gaiaLocales as $val) {
    $strings[$val][$repo] = array_filter(Utils::getRepoStrings($val, $repo), 'strlen');
    $stringCount[$val] = array(
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

foreach ($stringCount as $locale => $numbers) {

    $completion = $countReference - $numbers['identical'] - $numbers['missing'];
    $completion = number_format($completion/$countReference*100);

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
    die(Json::jsonOutput($json, $callback));
} else {
    echo $form;
    echo $table;
}
