<?php

// If $recherche consists of several words, each are stocked oin the $exploded_search array
$exploded_search = explode(' ', $recherche);
$whole_word      = ($check['whole_word']) ? '\b' : '';
$case_sensitive  = ($check['case_sensitive']) ? '' : 'i';
$search          = '/' . $whole_word . preg_quote($exploded_search[0], '/') . $whole_word . '/' . $case_sensitive;
$locale1_strings = preg_grep($search, $tmx_source);
$locale2_strings = preg_grep($search, $tmx_target);

$entities        = preg_grep($search, array_keys($tmx_source));

// The search is done for each word ($word) of the searched string ($exploded_search)
foreach ($exploded_search as $word) {
    $word = preg_quote($word, '/');
    $locale1_strings = preg_grep("/{$whole_word}{$word}{$whole_word}/{$case_sensitive}", $locale1_strings);
    $locale2_strings = preg_grep("/{$whole_word}{$word}{$whole_word}/{$case_sensitive}", $locale2_strings);
}
