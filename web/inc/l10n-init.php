<?php
/*
 * This file initializes l10n support: locale detection, rtl/ltr variable
 */

require_once WEBROOT .'classes/ChooseLocale.class.php';
require_once WEBROOT .'classes/RTLSupport.class.php';

$allLocales = file(INSTALLROOT . '/central.txt', FILE_IGNORE_NEW_LINES);
$l10n = new tinyL10n\ChooseLocale($allLocales);
$l10n->setDefaultLocale('fr');
$l10n->mapLonglocales = true;
$locale = $l10n->getCompatibleLocale();
$sourceLocale = 'en-US';

// bypass locale detection if the page sends a valid GET variable
if (isset($_GET['locale']) && in_array($_GET['locale'], $allLocales)) {
    $l10n->setDefaultLocale($_GET['locale']);
    $locale = $l10n->getDefaultLocale();
}

// bypass default source locale for locale to locale comparizon
if (isset($_GET['sourcelocale']) && in_array($_GET['sourcelocale'], $allLocales)) {
    $sourceLocale = $_GET['sourcelocale'];
}

// get rtl attribute for source and targer locales
$localeDir = $l10n->getDirection($locale);
$sourceLocaleDir = $l10n->getDirection($sourceLocale);
