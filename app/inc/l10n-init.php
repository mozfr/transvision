<?php
namespace Transvision;

// This file initializes l10n support: locale detection, RTL/LTR variables

if (isset($_GET['repo'])) {
    $search->setRepository(Utils::secureText($_GET['repo']));
}
$repo = $search->getRepository();

$all_locales = Project::getRepositoryLocales($repo);

$l10n = new \tinyl10n\ChooseLocale($all_locales);
$l10n->setDefaultLocale('fr');
$l10n->mapLonglocales = true;
$locale = $l10n->getCompatibleLocale();
$locale2 = $locale;
$source_locale = Project::getReferenceLocale($repo);

/*
    Bypass locale & source locale detection if there are COOKIES stored with them.
    Cookies are user-controlled input that end up in HTML pages, so we only
    accept values that are known locale codes.
*/
$known_locales = array_merge(Project::getAllLocales(), $all_locales);
$get_cookie_locale = function ($name) use ($known_locales) {
    return isset($_COOKIE[$name]) && is_string($_COOKIE[$name])
        && in_array($_COOKIE[$name], $known_locales, true)
        ? $_COOKIE[$name]
        : null;
};

$source_locale = $get_cookie_locale('default_source_locale') ?? $source_locale;
$locale = $get_cookie_locale('default_target_locale') ?? $locale;
// 3 locales view
$locale2 = $get_cookie_locale('default_target_locale2') ?? $locale2;

// Bypass locale detection if the page sends a valid GET variable
if (isset($_GET['locale'])) {
    // Redirect locale to a different one if necessary
    $requested_locale = $_GET['locale'];
    $requested_locale = Project::getLocaleInContext($requested_locale, $repo);
    if (in_array($requested_locale, $all_locales)) {
        $l10n->setDefaultLocale($requested_locale);
        $locale = $l10n->getDefaultLocale();
    }
}

// Bypass locale detection if the page sends a valid GET variable
if (isset($_GET['locale2'])) {
    // Redirect locale to a different one if necessary
    $requested_locale2 = $_GET['locale2'];
    $requested_locale2 = Project::getLocaleInContext($requested_locale2, $repo);
    if (in_array($requested_locale2, $all_locales)) {
        $locale2 = $requested_locale2;
    }
}

// Bypass default source locale for locale to locale comparison
if (isset($_GET['sourcelocale'])) {
    // Redirect locale to a different one if necessary
    $requested_sourcelocale = $_GET['sourcelocale'];
    $requested_sourcelocale = Project::getLocaleInContext($requested_sourcelocale, $repo);
    if (in_array($requested_sourcelocale, $all_locales)) {
        $source_locale = $requested_sourcelocale;
    }
}

// Get RTL attribute for source and target locales
$locale_dir = $l10n->getDirection($locale);
$source_locale_dir = $l10n->getDirection($source_locale);
