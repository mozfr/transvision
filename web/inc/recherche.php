<?php

// If $recherche consits of several words, each are stocked on the $aaa variable
$aaa = explode(' ', $recherche);

// $selected_radio = $_GET["case_sensitive"];

// If case sensitive is checked,
// The search is made for each word (aa) of the searched string (aaa)

$b        = ($check['whole_word']) ? '\b' : '';
$i        = ($check['case_sensitive'])  ? ''   : 'i';
$search   = '/' . $b . $aaa[0] . $b . '/' . $i;
$keys     = preg_grep($search, $tmx_source);
$keys2    = preg_grep($search, $tmx_target);
$entities = preg_grep($search, array_keys($tmx_source));

foreach ($aaa as $aa) {
    $keys  = preg_grep('/' . $b . $aa . $b . '/' . $i, $keys);
    $keys2 = preg_grep('/' . $aa . $b . '/' . $i, $keys2);
}
