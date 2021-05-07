<?php
namespace Transvision;

$reference_locale = Project::getReferenceLocale($repo);
// Exclude all en-* from this view
$supported_locales = array_filter(Project::getRepositoryLocales($repo), function($loc) {
    return ! Strings::startsWith($loc, 'en-') && $loc != 'en';
});

$target_locales_list = Utils::getHtmlSelectOptions($supported_locales, $locale);
