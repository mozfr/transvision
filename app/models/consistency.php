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

// Set a default for the number of strings to display
$strings_number = 0;

$source_strings = Utils::getRepoStrings($reference_locale, $repo);
// Ignore empty strings in translations
$target_strings = array_filter(Utils::getRepoStrings($locale, $repo));

if (! empty($source_strings)) {
    // Remove known problematic strings
    $duplicated_strings_english = Consistency::filterStrings($source_strings, $repo);
    // Find strings that are identical in English
    $duplicated_strings_english = Consistency::findDuplicates($duplicated_strings_english);
}

if (! empty($duplicated_strings_english)) {
    $inconsistent_translations = [];
    $available_translations = [];

    /*
        Using CachingIterator to be able to look ahead during the foreach
        cycle, since it's an associative array.
        http://php.net/manual/en/class.cachingiterator.php
    */
    $collection = new \CachingIterator(new \ArrayIterator($duplicated_strings_english));
    foreach ($collection as $key => $value) {
        // Ignore this string if is not available in the localization
        if (! isset($target_strings[$key])) {
            $available_translations = [];
            continue;
        }

        $available_translations[] = $target_strings[$key];
        /*
            If the current English string is different from the previous one,
            or I am at the last element, I need to store it with all available
            translations collected so far.

            $collection->getInnerIterator()->current() stores the value of
            the next element.
        */
        if (! $collection->hasNext() || $collection->getInnerIterator()->current() != $value) {
            $available_translations = array_unique($available_translations);
            /*
                Store this string only if there's more than one translation.
                If there's only one, translations are consistent.
            */
            if (count($available_translations) > 1) {
                $inconsistent_translations[] = [
                    'source' => $value,
                    'target' => $available_translations,
                ];
            }
            $available_translations = [];
        }
    }

    $strings_number = count($inconsistent_translations);
}
