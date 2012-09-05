<?php

if (!$valid) die;

// Deduce the memoire.tmx directory name
$tmxfile = TMX . '/memoire_en-US_' . $locale . '.tmx';

include TMX . $check['repo'] . '/' . $locale . '/cache_' . $locale . '.php'; // localised
$tmx_target = $tmx;
unset($tmx);

if ($sourceLocale == 'en-US') {
    include TMX . $check['repo'] . '/' . $locale . '/cache_en-US.php'; // English
} else {
    include TMX . $check['repo'] . "/${sourceLocale}/cache_${sourceLocale}.php"; // localised, for a locale to locale comparizon
}
$tmx_source = $tmx;

