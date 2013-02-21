<?php
/*
 * This file initializes l10n support: locale detection, rtl/ltr variables
 */

$allLocales = file(INSTALLROOT . '/central.txt', FILE_IGNORE_NEW_LINES);
$l10n = new tinyl10n\ChooseLocale($allLocales);
$l10n->setDefaultLocale('fr');
$l10n->mapLonglocales = true;
$locale = $l10n->getCompatibleLocale();
$sourceLocale = 'en-US';

// Bypass locale detection if the page sends a valid GET variable
if (isset($_GET['locale']) && in_array($_GET['locale'], $allLocales)) {
    $l10n->setDefaultLocale($_GET['locale']);
    $locale = $l10n->getDefaultLocale();
}

// Bypass default source locale for locale to locale comparison
if (isset($_GET['sourcelocale'])
    && in_array($_GET['sourcelocale'], $allLocales)) {
    $sourceLocale = $_GET['sourcelocale'];
}

// Get rtl attribute for source and target locales
$localeDir = $l10n->getDirection($locale);
$sourceLocaleDir = $l10n->getDirection($sourceLocale);
