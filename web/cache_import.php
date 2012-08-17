<?php

if (!$valid) die;

// Deduce the memoire.tmx directory name
$tmxfile = TMX . '/memoire_en-US_' . $locale . '.tmx';

include TMX . $check['repo'] . '/' . $locale . '/cache_' . $locale . '.php'; // localised
$tmx_target = $tmx;

include TMX . $check['repo'] . '/' . $locale . '/cache_en-US.php'; // English
$tmx_source = $tmx;

