<?php
namespace Transvision;

// Build arrays for the search form, ignore mozilla_org
$channel_selector = Utils::getHtmlSelectOptions(
    array_diff($repos_nice_names, ['mozilla.org']),
    $repo,
    true
);
$target_locales_list = Utils::getHtmlSelectOptions(
    Project::getRepositoryLocales($repo),
    $locale
);

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

// Sanitize strings
$unchanged_strings = Utils::secureText($unchanged_strings);
$strings_locale = Utils::secureText($strings_locale);

if (in_array($repo, $desktop_repos)) {
    // Build logic to filter components
    $components = Project::getComponents($unchanged_strings);
    $filter_block = '';
    foreach ($components as $value) {
        $filter_block .= " <a href='#{$value}' id='{$value}' class='filter'>{$value}</a>";
    }
}
