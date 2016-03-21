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

$available_filters = [
    'all'         => 'All products',
    'firefox'     => 'Firefox for desktop and Android',
    'seamonkey'   => 'SeaMonkey',
    'thunderbird' => 'Thunderbird',
];

if (isset($_GET['filter'])) {
    $selected_filter = isset($available_filters[$_GET['filter']])
        ? Utils::secureText($_GET['filter'])
        : 'all';
} else {
    $selected_filter = 'all';
}
$filter_visibility = Project::isDesktopRepository($repo)
    ? ''
    : 'style="display: none;"';
$filter_selector = Utils::getHtmlSelectOptions(
    $available_filters,
    $selected_filter,
    true
);

$reference_locale = Project::getReferenceLocale($repo);

// Set a default for the number of strings to display
$strings_number = 0;

$source_strings = Utils::getRepoStrings($reference_locale, $repo);
// Remove blanks strings. Using 'strlen' to avoid filtering out strings set to 0
$target_strings = array_filter(Utils::getRepoStrings($locale, $repo), 'strlen');

if (! empty($source_strings)) {
    // Remove known problematic strings
    $duplicated_strings_english = Consistency::filterStrings($source_strings, $repo);

    // Filter out components
    switch ($selected_filter) {
        case 'firefox':
            $excluded_components = [
                'calendar', 'chat', 'editor', 'extensions', 'mail', 'suite',
            ];
            break;
        case 'seamonkey':
            $excluded_components = [
                'browser', 'calendar', 'chat', 'extensions', 'mail', 'mobile',
            ];
            break;
        case 'thunderbird':
            $excluded_components = [
                'browser', 'extensions', 'mobile', 'suite',
            ];
            break;
        default:
            $excluded_components = [];
            break;
    }
    $filter_message = empty($excluded_components)
        ? ''
        : 'Currently excluding the following folders: ' . implode(', ', $excluded_components) . '.';
    $duplicated_strings_english = Consistency::filterComponents(
        $duplicated_strings_english,
        $excluded_components
    );

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
