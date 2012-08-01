<?php

if (!$valid) {
    die("File can't be called directly");
}

// default value
$recherche = 'Bookmarks';

// recherche is the string to find
if (isset($_GET['recherche'])) {
    include_once 'function_clean.php';
    $recherche = stripslashes(secureText($_GET['recherche']));
}

// cloned values
$recherche2 = $recherche3 = $recherche;

// helper function to set checkboxes value
function checkboxState($str) {
    return ($str) ? ' checked="checked"' : '';
}


// checkboxes states
$check = array();
$checkboxes = array('case_sensitive', 'regular', 'wild', 'ent', 'whole_word', 'perfect_match', 'alignement', 't2t', 'result_loc', 'regular');
// note: result_loc = Return only english or locale in the webservice
foreach($checkboxes as $val) {
    $check[$val] = (isset($_GET[$val])) ? true : false;
}


$check['repo'] = (isset($_GET['repo'])) ? $_GET['repo'] : 'release';
$base          = $check['repo'];
$locale        = (isset($_GET['locale'])) ? $_GET['locale'] : $detectedLocale;
$direction     = (in_array($locale, array('ar', 'fa', 'he'))) ? 'rtl' : 'ltr';

$dirs = array_filter(glob(TMX . $base . '/*'), 'is_dir');

foreach ($dirs as $dir) {
    $locs       = explode('/', $dir);
    $loc        = array_pop($locs);
    $loc_list[] = $loc;
}

// deal with special cases depending on checkboxes ticked on or off
if ($check['wild']) {
    $recherche       = str_replace('*', '.+', $recherche);
    $recherche2      = $recherche;
    $_GET['regular'] = true;
 //   $check['regular'] = 'checked';
}

// Search for perfectMatch
if ($check['perfect_match']) {
    $recherche       = '^' . $recherche . '$';
    $recherche2      = $recherche;
    $_GET['regular'] = true;
//    $check['regular'] = 'checked';
}

if (!$check['regular']) {
    $recherche = preg_quote($recherche);
}

//if ($check['perfect_match']) {
 //   $l_en = array_flip($l_en);
//}
