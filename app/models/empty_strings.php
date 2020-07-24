<?php
namespace Transvision;

$reference_locale = Project::getReferenceLocale($repo);
$supported_locales = Project::getRepositoryLocales($repo, [$reference_locale]);
// If the requested locale is not available, fall back to the first
if (! in_array($locale, $supported_locales)) {
    $locale = array_shift($supported_locales);
}
$target_locales_list = Utils::getHtmlSelectOptions($supported_locales, $locale);

$reference_strings = Utils::getRepoStrings($reference_locale, $repo);
$locale_strings = Utils::getRepoStrings($locale, $repo);

/*
    Identify empty strings in reference locale, store the translation
    only if locale has a non empty string for that ID. Then repeat the
    process for the locale.
*/
$empty_strings = [];

$empty_reference = array_filter($reference_strings, function ($string) {
    return strlen($string) == 0 || $string === '{ "" }' || $string === '{""}';
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
    return strlen($string) == 0 || $string === '{ "" }' || $string === '{""}';
});
foreach ($empty_locale as $string_id => $value) {
    if (isset($reference_strings[$string_id]) && $reference_strings[$string_id] != '') {
        $empty_strings[$string_id] = [
            'reference'   => $reference_strings[$string_id],
            'translation' => $value,
        ];
    }
}

unset($reference_strings);
unset($locale_strings);

// Build components filter
if (count($empty_strings) > 0) {
    ksort($empty_strings);
    $components = Project::getComponents($empty_strings);
    $filter_block = ShowResults::buildComponentsFilter($components);
}
