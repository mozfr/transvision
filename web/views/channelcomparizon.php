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

$locale = 'fr';
$repos  = array('central', 'aurora', 'beta', 'release');
$canal1 = 'aurora';
$canal2 = 'beta';

if(isset($_GET['canal1']) && in_array($_GET['canal1'], $repos)) {
    $canal1 = $_GET['canal1'];
}

if(isset($_GET['canal2']) && in_array($_GET['canal2'], $repos)) {
    $canal2 = $_GET['canal2'];
}

if(isset($_GET['locale']) && in_array($_GET['locale'], $allLocales)) {
    $locale = $_GET['locale'];
}

$strings = array();


foreach($repos as $repo) {
    $strings[$repo] = getRepoStrings($locale, $repo);
}

foreach($strings as $key => $val) {
    echo "$key => " . count($val) . "<br>";
}



$temp = array_intersect_key($strings[$canal1], $strings[$canal2]);
$temp = array_diff($temp, $strings[$canal2]);

echo '<table>';
echo '<tr>';
echo "<th>Key</th><th>$canal1</th><th>$canal2</th>";
echo '</tr>';
foreach($temp as $k => $v) {
    echo '<tr>';
    echo "<td>". TransvisionResults\ShowResults::formatEntity($k) . "</td><td>" . TransvisionResults\ShowResults::highlightFrench($v) . "</td><td>" . TransvisionResults\ShowResults::highlightFrench($strings['release'][$k]) . "</td>";
    echo '</tr>';
}

//~ $result = count(array_diff_key($tmx_target, $tmx_source));
//~ print_r($result);
//~ echo '<pre>';
//~ var_dump(array_diff_key($tmx_source, $tmx_target));

