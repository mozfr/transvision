<?php

// Always make sure we have a $tmx array defined
$tmx = array();

// Deduce the memoire.tmx directory name
$tmxfile = TMX . '/memoire_en-US_' . $locale . '.tmx';

if ($check['repo'] != 'gaia') {
    include TMX . "{$check['repo']}/{$locale}/cache_{$locale}.php"; // localised
}

// Add Gaia strings to Desktop strings
// We have only one spanish for Gaia
if (in_array($locale, $spanishes)) {
    $file = TMX . 'gaia/es/cache_es.php';
} else {
    $file = TMX . 'gaia/' . $locale . '/cache_' . $locale . '.php';
}

if (file_exists($file)) {
    include $file;
}

$tmx_target = $tmx;
unset($tmx);

if ($check['repo'] != 'gaia') {

    if ($sourceLocale == 'en-US') {
        // English
        include TMX . $check['repo'] . "/{$locale}/cache_en-US.php";
    } else {
        // localised, for a locale to locale comparison
        include TMX . "{$check['repo']}/${sourceLocale}/cache_${sourceLocale}.php";
    }
}

// We have only one spanish for Gaia
if ($check['repo'] == 'gaia'
    && in_array($sourceLocale, $spanishes)) {
    $sourceLocale = 'es';
}

$file = TMX . 'gaia/' . $sourceLocale . '/cache_' . $sourceLocale . '.php';

if (file_exists($file)) {
    include $file;
}

$tmx_source = $tmx;
