<?php
namespace Transvision;

// Regex options
$whole_word     = ($check['whole_word']) ? '\b' : '';
$case_sensitive = ($check['case_sensitive']) ? '' : 'i';

if ($check['perfect_match']) {
    $regex = '/' . $whole_word . $initial_search . $whole_word . '/' . $case_sensitive;
    $locale1_strings = preg_grep($regex, $tmx_source);
    $locale2_strings = preg_grep($regex, $tmx_target);
} else {
    $search = Utils::uniqueWords($initial_search);
    $locale1_strings = $tmx_source;
    $locale2_strings = $tmx_target;
    var_dump($search);
    foreach ($search as $word ) {
        $regex = '/' . $whole_word . preg_quote($word, '/') . $whole_word . '/' . $case_sensitive;
        $locale1_strings = preg_grep($regex, $locale1_strings);
        $locale2_strings = preg_grep($regex, $locale2_strings);
    }
}

if (count($locale1_strings) > 200 ) {
    array_splice($locale1_strings, 200);
}

if (count($locale1_strings) > 200 ) {
    array_splice($locale1_strings, 200);
}

$entities = preg_grep($regex, array_keys($tmx_source));

if ( $check['key_val'] ) {
    foreach ($entities as $entity) {
        $locale1_strings[$entity] = $tmx_source[$entity];
    }
}
