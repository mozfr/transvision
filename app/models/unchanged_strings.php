<?php
namespace Transvision;

// Set up the repository selector, remove meta project, mozilla.org, firefox.com
$repositories = Project::getSupportedRepositories(true);
unset($repositories['mozilla_org']);
unset($repositories['firefox_com']);
$repository_selector = Utils::getHtmlSelectOptions($repositories, $repo, true);

$reference_locale = Project::getReferenceLocale($repo);
// Exclude all en-* from this view
$supported_locales = array_filter(Project::getRepositoryLocales($repo), function($loc) {
    return ! Strings::startsWith($loc, 'en-');
});
// If the requested locale is not available, fall back to the first
if (! in_array($locale, $supported_locales)) {
    $locale = array_shift($supported_locales);
}

$target_locales_list = Utils::getHtmlSelectOptions($supported_locales, $locale);

// Load strings
$strings_locale = Utils::getRepoStrings($locale, $repo);
$strings_reference = Utils::getRepoStrings(
    Project::getReferenceLocale($repo),
    $repo
);

// Filter out unwanted keys
foreach ($strings_reference as $string_id => $string_value) {
    if (strlen($string_value) <= 1 ||
        strpos($string_id, 'region.properties') !== false) {
        /*
            Ignore accesskeys, shortcuts and empty strings.
            Ignore keys in region.properties
        */
        unset($strings_reference[$string_id]);
    }
}

// Find strings identical to English
$unchanged_strings = [];
foreach ($strings_reference as $string_id => $string_value) {
    if (isset($strings_locale[$string_id])) {
        // Compare only if the localized string exists
        if ($strings_locale[$string_id] == $string_value) {
            $unchanged_strings[$string_id] = $string_value;
        }
    }
}

// Build components filter
if (in_array($repo, $desktop_repos)) {
    $components = Project::getComponents($unchanged_strings);
    $filter_block = ShowResults::buildComponentsFilter($components);
}
