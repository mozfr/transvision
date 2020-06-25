<?php
namespace Transvision;

// Build arrays for the search form, ignore mozilla.org
$channel_selector = Utils::getHtmlSelectOptions(
    $repos_nice_names,
    $repo,
    true
);

$reference_locale = Project::getReferenceLocale($repo);
$supported_locales = Project::getRepositoryLocales($repo, [$reference_locale]);
// If the requested locale is not available, fall back to the first
if (! in_array($locale, $supported_locales)) {
    $locale = array_shift($supported_locales);
}
$target_locales_list = Utils::getHtmlSelectOptions($supported_locales, $locale);

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

// Set a default for the number of strings to display
$strings_number = 0;

$source_strings = Utils::getRepoStrings($reference_locale, $repo);
// Remove blanks strings. Using 'strlen' to avoid filtering out strings set to 0
$target_strings = array_filter(Utils::getRepoStrings($locale, $repo), 'strlen');

// No filtered component by default
$filter_message = '';

$duplicated_strings_source = Consistency::filterStrings($source_strings, $repo);
$duplicated_strings_target = Consistency::filterStrings($target_strings, $repo);

if (! empty($source_strings) && $repo == 'gecko_strings') {
    // Remove known problematic strings

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
    $duplicated_strings_source = Consistency::filterComponents(
        $duplicated_strings_source,
        $excluded_components
    );
    $duplicated_strings_target = Consistency::filterComponents(
        $duplicated_strings_target,
        $excluded_components
    );

    /*
        Find strings that are identical in source and target language.
        For target language, perform a case insensitive comparison.
    */
    $duplicated_strings_source = Consistency::findDuplicates($duplicated_strings_source);
    $duplicated_strings_target = Consistency::findDuplicates($duplicated_strings_target, False);
}

if (! empty($duplicated_strings_source)) {
    $inconsistent_translations = [];
    $available_translations = [];

    /*
        Using CachingIterator to be able to look ahead during the foreach
        cycle, since it's an associative array.
        http://php.net/manual/en/class.cachingiterator.php
    */
    $collection = new \CachingIterator(new \ArrayIterator($duplicated_strings_source));
    foreach ($collection as $key => $value) {
        // Ignore this string if is not available in the localization
        if (! isset($target_strings[$key])) {
            $available_translations = [];
            continue;
        }

        $available_translations[] = $target_strings[$key];
        /*
            If the current source string is different from the previous one,
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
}

if (! empty($duplicated_strings_target)) {
    $inconsistent_source = [];
    $available_sources = [];

    $collection = new \CachingIterator(new \ArrayIterator($duplicated_strings_target));
    foreach ($collection as $key => $value) {
        // Ignore this string if is not available in the source
        if (! isset($source_strings[$key])) {
            $available_sources = [];
            continue;
        }
        /*
            If the current target string is different from the previous one,
            or I am at the last element, I need to store it with all available
            sources collected so far.

            Source comparison is case insensitive.

            $collection->getInnerIterator()->current() stores the value of
            the next element.
        */
        if (! $collection->hasNext() || strtolower($collection->getInnerIterator()->current()) != strtolower($value)) {
            $available_sources = array_unique($available_sources);
            /*
                Store this string only if there's more than one translation.
                If there's only one, translations are consistent.
            */
            if (count($available_sources) > 1) {
                $inconsistent_sources[] = [
                    'target' => $value,
                    'source' => $available_sources,
                ];
            }
            $available_sources = [];
        }
    }
}
