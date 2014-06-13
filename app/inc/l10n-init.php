<?php
namespace Transvision;
/*
 * This file initializes l10n support: locale detection, rtl/ltr variables
 */

if (isset($_GET['repo'])) {
    $repo = Project::isValidRepository($_GET['repo'])
            ? $_GET['repo']
            : 'central';
} else {
    if (! isset($repo)) {
        $repo = 'central';
    }
}

$all_locales = Project::getRepositoryLocales($repo);

// Add en-US as a regular locale without impacting glossaire.sh
$all_locales[] = 'en-US';

$l10n = new \tinyl10n\ChooseLocale($all_locales);
$l10n->setDefaultLocale('fr');
$l10n->mapLonglocales = true;
$locale = $l10n->getCompatibleLocale();
$locale2 = $locale;
$source_locale = 'en-US';

// Bypass locale & source locale detection if there are a COOKIES stored with them
if (isset($_COOKIE['default_source_locale'])) {
    $source_locale = $_COOKIE['default_source_locale'];
}

if (isset($_COOKIE['default_target_locale'])) {
    $locale = $_COOKIE['default_target_locale'];
}

// 3 locales view
if (isset($_COOKIE['default_target_locale2'])) {
    $locale2 = $_COOKIE['default_target_locale2'];
}

// Bypass locale detection if the page sends a valid GET variable
if (isset($_GET['locale']) && in_array($_GET['locale'], $all_locales)) {
    $l10n->setDefaultLocale($_GET['locale']);
    $locale = $l10n->getDefaultLocale();
}

// Bypass locale2 default value or cookie if the page sends a valid GET variable
if (isset($_GET['locale2']) && in_array($_GET['locale2'], $all_locales)) {
    $locale2 = $_GET['locale2'];
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
