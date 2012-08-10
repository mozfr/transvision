<?php

if (!isset($valid) || $valid == false) return;

// default search value
$recherche = 'Bookmarks';

// recherche is the string to find
if (isset($_GET['recherche'])) {
    include_once 'function_clean.php';
    $recherche = stripslashes(secureText($_GET['recherche']));
} else {
    $recherche = '';
}

// cloned values
$recherche2 = $recherche3 = $recherche;

// helper function to set checkboxes value
function checkboxState($str, $disabled='') {
    if($str == 't2t') {
        return ($str) ? ' checked="checked"' : '';
    }

    if(isset($_GET['t2t'])) {
        return ' disabled="disabled"';
    } else {
        return ($str) ? ' checked="checked"' : '';
    }
}


// checkboxes states
$check = array();
$checkboxes = array('case_sensitive', 'regular', 'wild', 'ent', 'whole_word', 'perfect_match', 'alignement', 't2t', 'result_loc',);
// note: result_loc = Return only english or locale in the webservice
foreach($checkboxes as $val) {
    $check[$val] = (isset($_GET[$val])) ? true : false;
}

$check['repo'] = (isset($_GET['repo'])) ? $_GET['repo'] : 'release';
$base          = $check['repo'];

$dirs = array_filter(glob(TMX . $base . '/*'), 'is_dir');

foreach ($dirs as $dir) {
    $locs       = explode('/', $dir);
    $loc        = array_pop($locs);
    $loc_list[] = $loc;
}

// deal with special cases depending on checkboxes ticked on or off
if ($check['wild']) {
    $recherche        = str_replace('*', '.+', $recherche);
    $recherche2       = $recherche;
    $check['regular'] = 'checked';
}

// Search for perfectMatch
if ($check['perfect_match']) {
    $recherche        = '^' . $recherche . '$';
    $recherche2       = $recherche;
    $check['regular'] = 'checked';
}

if (!$check['regular']) {
    $recherche = preg_quote($recherche);
}

$recherche = trim($recherche);
