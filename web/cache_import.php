<?php

if (!$valid) {
    die("File can't be called directly");
}

// Deduce the memoire.tmx directory name
$tmxfile = TMX . '/memoire_en-US_' . $locale . '.tmx';

include TMX . $base . '/' . $locale . '/cache_' . $locale . '.php'; // localised
$tmx_target = $tmx;

include TMX . $base . '/' . $locale . '/cache_en-US.php'; // English
$tmx_source = $tmx;

