<?php

if (!valid($valid)) return;

// default search value
$recherche = 'Bookmarks';

// recherche is the string to find
if (isset($_GET['recherche'])) {
    $recherche = stripslashes(secureText($_GET['recherche']));
} else {
    $recherche = '';
}

// cloned values
$initial_search = $recherche;

// checkboxes states
$check = array();
$checkboxes = array('case_sensitive', 'regular', 'wild', 'ent', 'whole_word', 'perfect_match', 'alignement', 't2t', 'result_loc',);
// note: result_loc = Return only english or locale in the webservice
foreach($checkboxes as $val) {
    $check[$val] = (isset($_GET[$val])) ? true : false;
}

$check['repo'] = (isset($_GET['repo'])) ? $_GET['repo'] : 'release';

if (isset($_GET['repo']) && in_array($_GET['repo'], array('release','beta','aurora', 'central'))) {
    $check['repo'] = $_GET['repo'];
} else {
    $check['repo'] = 'release';
}

$dirs = array_filter(glob(TMX . $check['repo'] . '/*'), 'is_dir');

foreach ($dirs as $dir) {
    $locs       = explode('/', $dir);
    $loc        = array_pop($locs);
    $loc_list[] = $loc;
}

// deal with special cases depending on checkboxes ticked on or off
if ($check['wild']) {
    $recherche        = str_replace('*', '.+', $recherche);
    $initial_search   = $recherche;
    $check['regular'] = 'checked';
}

// Search for perfectMatch
if ($check['perfect_match']) {
    $recherche        = '^' . $recherche . '$';
    $initial_search   = $recherche;
    $check['regular'] = 'checked';
}

if (!$check['regular']) {
    $recherche = preg_quote($recherche);
}

$recherche = trim($recherche);
