<?php
// Page title
$title = 'Transvision glossary <a href="/news/#v' . VERSION . '">' . VERSION . '</a>';
require_once WEBROOT .'classes/ShowResults.class.php';
require_once WEBROOT .'inc/l10n-init.php';

$repos = array('central', 'aurora', 'beta', 'release');
$chan1 = 'aurora';
$chan2 = 'beta';

if (isset($_GET['chan1']) && in_array($_GET['chan1'], $repos)) {
    $chan1 = $_GET['chan1'];
}

if (isset($_GET['chan2']) && in_array($_GET['chan2'], $repos)) {
    $chan2 = $_GET['chan2'];
}

if (isset($_GET['locale']) && in_array($_GET['locale'], $allLocales)) {
    $locale = $_GET['locale'];
}

foreach ($repos as $repo) {

    if (!isset($strings)) {
        $strings = array();
    }

    $strings[$repo] = getRepoStrings($locale, $repo);
}

$chanSelector1 = $chanSelector2 = '';

foreach ($repos as $repo) {
    $ch1 = ($repo == $chan1) ? ' selected' : '';
    $ch2 = ($repo == $chan2) ? ' selected' : '';
    $chanSelector1 .= "\t<option" . $ch1 . " value=" . $repo . ">" . $repo . "</option>\n";
    $chanSelector2 .= "\t<option" . $ch2 . " value=" . $repo . ">" . $repo . "</option>\n";
}

// Get the locale list
$loc_list = scandir(TMX . $repo . '/');
$loc_list = array_diff($loc_list, array('.', '..'));
$spanish  = array_search('es', $loc_list);

if ($spanish) {
    $loc_list[$spanish] = 'es-ES';
}

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
                <?=$chanSelector1?>
                </select>
            </fieldset>
            <fieldset>
                <legend>Channel 2:</legend>
                <select name='chan2'>
                <?=$chanSelector2?>
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
    echo '<tr>';
    echo "<td>". TransvisionResults\ShowResults::formatEntity($k). "</td>" .
    "<td>" . TransvisionResults\ShowResults::highlight($v, $locale) . "</td>";
    //~ if (isset($strings[$chan2][$k])) {
        echo "<td>" . TransvisionResults\ShowResults::highlight($strings[$chan2][$k], $locale) . "</td>";
    //~ } else {
        //~ echo "<td> <em>Missing String</em> </td>";
    //~ }
    echo '</tr>';
}
echo '</table>';
