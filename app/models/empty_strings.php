<?php
namespace Transvision;

// Build arrays for the search form, ignore mozilla.org
$channel_selector = Utils::getHtmlSelectOptions(
    array_diff($repos_nice_names, ['mozilla.org']),
    $repo,
    true
);

$target_locales_list = Utils::getHtmlSelectOptions(
    Project::getRepositoryLocales($repo),
    $locale
);

$reference_locale = Project::getReferenceLocale($repo);

$reference_strings = Utils::getRepoStrings($reference_locale, $repo);
$locale_strings = Utils::getRepoStrings($locale, $repo);

/*
    Identify empty strings in reference locale, store the translation
    only if locale has a non empty string for that ID. Then repeat the
    process for the locale.
*/
$empty_strings = [];

$empty_reference = array_filter($reference_strings, function ($string) {
    return strlen($string) == 0;
});
foreach ($empty_reference as $string_id => $value) {
    if (isset($locale_strings[$string_id]) && $locale_strings[$string_id] != '') {
        $empty_strings[$string_id] = [
            'reference'   => $value,
            'translation' => $locale_strings[$string_id],
        ];
    }
}

$empty_locale = array_filter($locale_strings, function ($string) {
    return strlen($string) == 0;
});
foreach ($empty_locale as $string_id => $value) {
    if ($reference_strings[$string_id] != '') {
        $empty_strings[$string_id] = [
            'reference'   => $reference_strings[$string_id],
            'translation' => $value,
        ];
    }
}

ksort($empty_strings);

unset($reference_strings);
unset($locale_strings);
