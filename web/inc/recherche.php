<?php

// If $recherche consists of several words, each are stocked oin the $exploded_search array
$exploded_search = explode(' ', $recherche);
$whole_word      = ($check['whole_word']) ? '\b' : '';
$case_sensitive  = ($check['case_sensitive']) ? '' : 'i';

if ($check['perfect_match'] == false) {
    $midsearch = preg_quote($recherche, '/');
} else {
    $midsearch = $recherche;
}

$search = '/' . $whole_word . $midsearch . $whole_word . '/' . $case_sensitive;
$locale1_strings = preg_grep($search, $tmx_source);
if (count($locale1_strings) > 200 ) {
    array_splice($locale1_strings, 200);
}

$locale2_strings = preg_grep($search, $tmx_target);
if (count($locale1_strings) > 200 ) {
    array_splice($locale1_strings, 200);
}

$entities = preg_grep($search, array_keys($tmx_source));

// The search is done for each word ($word) of the searched string ($exploded_search)
if ($check['perfect_match'] == false) {
    foreach ($exploded_search as $word) {
        //~ $word = preg_quote($word, '/');
        $locale1_strings = preg_grep("/{$whole_word}{$word}{$whole_word}/{$case_sensitive}", $locale1_strings);
        $locale2_strings = preg_grep("/{$whole_word}{$word}{$whole_word}/{$case_sensitive}", $locale2_strings);
    }
}

if ( $check['key_val'] ) {
    foreach ($entities as $entity) {
        $locale1_strings[$entity] = $tmx_source[$entity];
    }
}
