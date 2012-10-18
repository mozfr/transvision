<?php
// Page title
$title = 'Transvision glossary <a href="./news/#v' . VERSION . '">' . VERSION . '</a>';

//~ include TMX . $repo . '/en-US/cache_en-US.php';
//~ $tmx_source = $tmx;
//~ unset($tmx);

$locale  = 'fr';
$repos   = array('central', 'aurora', 'beta', 'release');
$strings = array();

function getRepoStrings($locale, $repo) {
    $tmx = array();
    include TMX . $repo . '/' . $locale . '/cache_' . $locale . '.php';
    return $tmx;
}

foreach($repos as $repo) {
    $strings[$repo] = getRepoStrings($locale, $repo);
}

foreach($strings as $key => $val) {
    echo "$key => " . count($val) . "<br>";
}
//~ dump(array_diff_assoc($strings['central'], $strings['release']));
$temp = array_intersect_key($strings['central'], $strings['release']);
dump(array_diff($temp, $strings['release']));

//~ $result = count(array_diff_key($tmx_target, $tmx_source));
//~ print_r($result);
//~ echo '<pre>';
//~ var_dump(array_diff_key($tmx_source, $tmx_target));

