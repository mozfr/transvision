<?php

// If $recherche consits of several words, each are stocked on the $aaa variable
$aaa = explode(' ', $recherche);

// If case sensitive is checked,
// The search is made for each word (aa) of the searched string (aaa)

$b               = ($check['whole_word']) ? '\b' : '';
$i               = ($check['case_sensitive'])  ? ''   : 'i';
$search          = '/' . $b . $aaa[0] . $b . '/' . $i;
$locale1_strings = preg_grep($search, $tmx_source);
$locale2_strings = preg_grep($search, $tmx_target);
$entities        = preg_grep($search, array_keys($tmx_source));

foreach ($aaa as $aa) {
    $locale1_strings = preg_grep("/{$b}{$aa}{$b}/{$i}", $locale1_strings);
    $locale2_strings = preg_grep("/{$b}{$aa}{$b}/{$i}", $locale2_strings);
}
