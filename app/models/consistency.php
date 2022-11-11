<?php
namespace Transvision;

// Set up the repository selector, remove "all_projects"
$repositories = Project::getSupportedRepositories();
unset($repositories['all_projects']);
$repository_selector = Utils::getHtmlSelectOptions($repositories, $repo, true);

$reference_locale = Project::getReferenceLocale($repo);
$supported_locales = Project::getRepositoryLocales($repo, [$reference_locale]);
// If the requested locale is not available, fall back to the first
if (! in_array($locale, $supported_locales)) {
    $locale = array_shift($supported_locales);
}
$target_locales_list = Utils::getHtmlSelectOptions($supported_locales, $locale);

$source_strings = Utils::getRepoStrings($reference_locale, $repo);
// Remove blanks strings. Using 'strlen' to avoid filtering out strings set to 0
$target_strings = array_filter(Utils::getRepoStrings($locale, $repo), 'strlen');

// Remove strings that should not be checked, like accesskeys, CSS, etc.
$duplicated_strings_source = Consistency::filterStrings($source_strings, $repo);
$duplicated_strings_target = Consistency::filterStrings($target_strings, $repo);

/*
    Find strings that are identical in source and target language.
    For target language, perform a case insensitive comparison.
*/
$duplicated_strings_source = Consistency::findDuplicates($duplicated_strings_source);
$duplicated_strings_target = Consistency::findDuplicates($duplicated_strings_target, False);

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
        /*
            Ignore this string if is not available in the localization.
            If the next string changes, we also need to reset the array of
            available translations.
        */
        if (! isset($target_strings[$key])) {
            if (! $collection->hasNext() || $collection->getInnerIterator()->current() != $value) {
                $available_translations = [];
            }
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
                asort($available_translations);
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
        /*
            Ignore this string if is not available in the source.
            If the next string changes, we also need to reset the array of
            available sources.
        */
        if (! isset($source_strings[$key])) {
            if (! $collection->hasNext() || strtolower($collection->getInnerIterator()->current()) != strtolower($value)) {
                $available_sources = [];
            }
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
                asort($available_sources);
                $inconsistent_sources[] = [
                    'target' => $value,
                    'source' => $available_sources,
                ];
            }
            $available_sources = [];
        }
    }
}
