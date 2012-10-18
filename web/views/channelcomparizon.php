<?php
// Page title
$title = 'Transvision glossary <a href="./news/#v' . VERSION . '">' . VERSION . '</a>';
require_once WEBROOT .'classes/ShowResults.class.php';
require_once WEBROOT .'inc/l10n-init.php';

//~ include TMX . $repo . '/en-US/cache_en-US.php';
//~ $tmx_source = $tmx;
//~ unset($tmx);

function getRepoStrings($locale, $repo) {
    $tmx = array();
    include TMX . $repo . '/' . $locale . '/cache_' . $locale . '.php';
    return $tmx;
}

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

$temp = array_intersect_key($strings[$chan1], $strings[$chan2]);
$temp = array_diff($temp, $strings[$chan2]);


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
