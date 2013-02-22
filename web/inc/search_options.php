<?php
namespace Transvision;

// Default search value
$recherche = '';

// $recherche is the string to find
if (isset($_GET['recherche'])) {
    $recherche = stripslashes(Utils::secureText($_GET['recherche']));
    // Filter out double spaces
    $recherche = Utils::mtrim($recherche);
}

// Cloned value for reference
$initial_search = $recherche;

// Checkboxes states
$check = array();

foreach ($form_checkboxes as $val) {
    $check[$val] = (isset($_GET[$val])) ? true : false;
}

$check['repo'] = 'central';

if (isset($_GET['repo']) && in_array($_GET['repo'], $repos)) {
    $check['repo'] = $_GET['repo'];
}

$check['search_type'] = 'strings';

if (isset($_GET['search_type'])
    && in_array($_GET['search_type'], array('strings', 'entities', 'strings_entities')
    )) {
    $check['search_type'] = $_GET['search_type'];
}

$dirs = array_filter(glob(TMX . $check['repo'] . '/*'), 'is_dir');

foreach ($dirs as $dir) {
    $locs       = explode('/', $dir);
    $loc        = array_pop($locs);
    $loc_list[] = $loc;
}

// Deal with special cases depending on checkboxes ticked on or off
if ($check['wild']) {
    $recherche = str_replace('*', '.+', $recherche);
}

// Search for perfectMatch
if ($check['perfect_match']) {
    $recherche = trim('^' . $recherche . '$');
} else {
    $recherche = preg_quote($recherche, '/');
}

