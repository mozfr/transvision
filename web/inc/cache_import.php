<?php

// Deduce the memoire.tmx directory name
$tmxfile = TMX . '/memoire_en-US_' . $locale . '.tmx';

if ($check['repo'] != 'gaia') {
    include TMX . "{$check['repo']}/{$locale}/cache_{$locale}.php"; // localised
}

// Gaia strings
$gaia_locale = $locale;

// We have only one spanish for Gaia
if (in_array($locale, array('es-AR', 'es-CL', 'es-ES', 'es-MX'))) {
    $gaia_locale = 'es';
}

$file = TMX . 'gaia/' . $gaia_locale . '/cache_' . $gaia_locale . '.php';
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
    && in_array($sourceLocale, array('es-AR', 'es-CL', 'es-ES', 'es-MX'))) {
    $sourceLocale = 'es';
}

$file = TMX . 'gaia/' . $sourceLocale . '/cache_' . $sourceLocale . '.php';

if (file_exists($file)) {
    include $file;
}

$tmx_source = $tmx;
