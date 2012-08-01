<?php

// Deduce the memoire.tmx directory name
$tmxfile = TMX . '/memoire_en-US_' . $locale . '.tmx';

# clearstatcache();

include TMX . $base . '/' . $locale . '/cache_' . $locale . '.php'; // localised
$tmx_fr = $tmx;

include TMX . $base . '/' . $locale . '/cache_en-US.php'; // English
$tmx_en = $tmx;


// get language arrays
$l_en = $tmx_en;
$l_fr = $tmx_fr;

// Recherche par entity
if ($check['ent']) {
    $l_en = array_flip($l_en);
}
