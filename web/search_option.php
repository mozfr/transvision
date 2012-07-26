<?php

// default value
$recherche = 'Minefield';

// recherche is the string to find
if (isset($_GET['recherche'])) {
    include_once 'function_clean.php';
    $recherche = stripslashes(secureText($_GET['recherche']));
}

// cloned value
$recherche2 = $recherche;
$recherche3 = $recherche;

// helper function to set checkboxes value
function checkState($str)
{
    return (isset($str)) ? 'checked' : 'unchecked';
}

// If the case sensitive button is checked, check it on the page
$check  = checkState($_GET['case_sensitive']);
// other checkboxes state
$check2 = checkState($_GET['regular']);
$check3 = checkState($_GET['wild']);
$check4 = checkState($_GET['ent']);
$check5 = checkState($_GET['whole_word']);
$check7 = checkState($_GET['perfect_match']);
$check8 = checkState($_GET['alignement']);
$check9 = checkState($_GET['t2t']);

//Return only english or locale in the webservice
$resultloc=checkState($_GET['result_loc']);

if (isset($_GET['repo'])){
    $check6 = $_GET['repo'];
    $base   = $check6;
} else {
    $check6='release';
    $base= $check6;
}

if (isset($_GET['locale'])){
    $locale = $_GET['locale'];
} else {
    $locale = 'fr';
}

$dirs = array_filter(glob('/home/pascalc/newtransvision/TMX/' . $base . '/*'), 'is_dir');
foreach ($dirs as $dir) {
    $locs       = explode('/', $dir);
    $loc        = array_pop($locs);
    $loc_list[] = $loc;
}

// deal with special cases depending on checkboxes ticked on or off
if ($check3 == 'checked') {
    $recherche       = str_replace('*', '.+', $recherche);
    $recherche2      = $recherche;
    $_GET['regular'] = true;
 //   $check2 = 'checked';
}

// Search for perfectMatch
if ($check7 == 'checked') {
    $recherche       = '^' . $recherche . '$';
    $recherche2      = $recherche;
    $_GET['regular'] = true;
//    $check2 = 'checked';
}

if ($_GET['regular'] == false) {
    $recherche = preg_quote($recherche);
}

//if ($check4 == 'checked') {
 //   $l_en = array_flip($l_en);
//}
