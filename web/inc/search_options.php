<?php

// default search value
$recherche = 'Bookmarks';

// recherche is the string to find
if (isset($_GET['recherche'])) {
    $recherche = stripslashes(secureText($_GET['recherche']));
    // Filter out double spaces
    $recherche = mtrim($recherche);
} else {
    $recherche = '';
}

// cloned values
$initial_search = $recherche;

// checkboxes states
$check = array();
$checkboxes = array('case_sensitive', 'wild', 'ent',
                    'whole_word', 'perfect_match', 't2t', 'key_val');

foreach ($checkboxes as $val) {
    $check[$val] = (isset($_GET[$val])) ? true : false;
}


$check['repo'] = 'central';
if (isset($_GET['repo'])
    && in_array($_GET['repo'], array('release', 'beta', 'aurora', 'central', 'gaia')
    )) {
    $check['repo'] = $_GET['repo'];
}

$dirs = array_filter(glob(TMX . $check['repo'] . '/*'), 'is_dir');

foreach ($dirs as $dir) {
    $locs       = explode('/', $dir);
    $loc        = array_pop($locs);
    $loc_list[] = $loc;
}

// deal with special cases depending on checkboxes ticked on or off
if ($check['wild']) {
    $recherche = str_replace('*', '.+', $recherche);
}

// Search for perfectMatch
if ($check['perfect_match']) {
    $recherche = '^' . $recherche . '$';
}

$recherche = trim($recherche);
