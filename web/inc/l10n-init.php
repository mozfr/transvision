<?php
/*
 * This file initializes l10n support: locale detection, rtl/ltr variables
 */

if (isset($_GET['repo']) && in_array($_GET['repo'], $repos)) {
    $all_locales = file(INSTALLROOT . '/' . $_GET['repo'] . '.txt', FILE_IGNORE_NEW_LINES);
} else {
    $all_locales = file(INSTALLROOT . '/central.txt', FILE_IGNORE_NEW_LINES);
}

$all_locales[] = 'en-US'; // Add en-US as a regular locale without impacting glossaire.sh

// Don't try to guess locales with the Json API as it is used by scripts, not humans
if (WEBSERVICE) {
    $locale = isset($_GET['locale'])
              ? $_GET['locale']
              : '';
    $source_locale = isset($_GET['sourcelocale'])
                    ? $_GET['sourcelocale']
                    : '';

    return;
}

$l10n = new tinyl10n\ChooseLocale($all_locales);
$l10n->setDefaultLocale('fr');
$l10n->mapLonglocales = true;
$locale = $l10n->getCompatibleLocale();
$source_locale = 'en-US';

// Bypass locale & source locale detection if there are a COOKIES stored with them

if (isset($_COOKIE['default_target_locale'])) {
    $locale = $_COOKIE['default_target_locale'];
}
if (isset($_COOKIE['default_source_locale'])) {
    $source_locale = $_COOKIE['default_source_locale'];
}

// Bypass locale detection if the page sends a valid GET variable
if (isset($_GET['locale']) && in_array($_GET['locale'], $all_locales)) {
    $l10n->setDefaultLocale($_GET['locale']);
    $locale = $l10n->getDefaultLocale();
}

// Bypass default source locale for locale to locale comparison
if (isset($_GET['sourcelocale']) && $_GET['sourcelocale'] == 'en-US') {
    $source_locale = 'en-US';
} elseif (isset($_GET['sourcelocale']) && in_array($_GET['sourcelocale'], $all_locales)) {
    $source_locale = $_GET['sourcelocale'];
}

// Get rtl attribute for source and target locales
$locale_dir = $l10n->getDirection($locale);
$source_locale_dir = $l10n->getDirection($source_locale);
