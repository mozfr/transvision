<?php
namespace Transvision;

require_once WEBROOT .'inc/l10n-init.php';

$chan1 = 'aurora';
$chan2 = 'beta';

if (isset($_GET['chan1']) && in_array($_GET['chan1'], $desktop_repos)) {
    $chan1 = $_GET['chan1'];
}

if (isset($_GET['chan2']) && in_array($_GET['chan2'], $desktop_repos)) {
    $chan2 = $_GET['chan2'];
}

if (isset($_GET['locale']) && in_array($_GET['locale'], $all_locales)) {
    $locale = $_GET['locale'];
}

$strings = array();
$strings[$chan1] = Utils::getRepoStrings($locale, $chan1);
$strings[$chan2] = Utils::getRepoStrings($locale, $chan2);

$chan_selector1 = $chan_selector2 = '';

foreach ($desktop_repos as $repo) {
    $ch1 = ($repo == $chan1) ? ' selected' : '';
    $ch2 = ($repo == $chan2) ? ' selected' : '';
    $chan_selector1 .= "\t<option" . $ch1 . " value=" . $repo . ">" .$repos_nice_names[$repo] . "</option>\n";
    $chan_selector2 .= "\t<option" . $ch2 . " value=" . $repo . ">" .$repos_nice_names[$repo] . "</option>\n";
}

// Get the locale list
$loc_list = Utils::getFilenamesInFolder(TMX . $repo . '/');

// build the target locale switcher
$target_locales_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}

$temp = array_intersect_key($strings[$chan1], $strings[$chan2]);
$temp = array_diff($temp, $strings[$chan2]);
?>
  <form name="searchform" method="get" action="">
        <fieldset id="main">

            <fieldset>
                <legend>Locale:</legend>
                <select name='locale'>
                <?=$target_locales_list?>
                </select>
            </fieldset>
            <fieldset>
                <legend>Channel 1:</legend>
                <select name='chan1'>
                <?=$chan_selector1?>
                </select>
            </fieldset>
            <fieldset>
                <legend>Channel 2:</legend>
                <select name='chan2'>
                <?=$chan_selector2?>
                </select>
            </fieldset>
            <input type="submit" value="Go" alt="Go" />

        </fieldset>
 </form>

<?php

echo "\n<table>";
echo '<tr>';
echo "<th colspan='3'>Locale: $locale</th>";
echo '</tr>';
echo '<tr>';
echo "<th>Key</th><th>$chan1</th><th>$chan2</th>";
echo '</tr>';

foreach ($temp as $k => $v) {
    echo   "<tr>"
         . "<td>" . ShowResults::formatEntity($k) . "</td>"
         . "<td>" . ShowResults::highlight($v, $locale) . "</td>"
         . "<td>" . ShowResults::highlight($strings[$chan2][$k], $locale) . "</td>
           </tr>";
}
echo '</table>';
