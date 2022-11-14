<?php
namespace Transvision;

if ($search->isEntireString()) {
    $locale1_strings = $search->grep($tmx_source, false);
    $locale2_strings = $search->grep($tmx_target, false);
} else {
    $locale1_strings = $tmx_source;
    $locale2_strings = $tmx_target;
    $search_terms = $search->isEachWord()
        ? Utils::uniqueWords($search->getSearchTerms())
        : [$search->getSearchTerms()];
    foreach ($search_terms as $word) {
        $search->setRegexSearchTerms($word);
        $locale1_strings = $search->grep($locale1_strings, false);
        $locale2_strings = $search->grep($locale2_strings, false);
    }
}

// Add entity matches to the results
if ($search->getSearchType() == 'strings_entities') {
    $entities = ShowResults::searchEntities($tmx_source, $search->getRegex());
    foreach ($entities as $entity_object) {
        [$repo, $entity] = $entity_object;
        $locale1_strings[$repo][$entity] = $tmx_source[$repo][$entity];
    }
}

// Flatten results to be able to count and slice them
$locale1_strings = Utils::flattenTMX($locale1_strings);
$locale2_strings = Utils::flattenTMX($locale2_strings);

$real_search_results = count($locale1_strings);
$limit_results = 200;
// Limit results to 200 per locale
array_splice($locale1_strings, $limit_results);
array_splice($locale2_strings, $limit_results);

$search_results = [
    $source_locale => $locale1_strings,
];
$data = [$tmx_source, $tmx_target];
unset($tmx_source, $tmx_target);

// Only use data for target locale if it's different from the source locale
if ($locale != $source_locale) {
    $search_results[$locale] = $locale2_strings;
}

/*
    3locales view. Only use data for this locale if it's different from both
    source and target locale.
*/
if ($url['path'] == '3locales') {
    $check['extra_locale'] = $locale2;
    $data[] = $tmx_target2;
    unset($tmx_target2);
    if (! in_array($locale2, [$source_locale, $locale])) {
        $search_results[$locale2] = $locale3_strings;
    }
}

$search_yields_results = false;
// This will hold the components names for the search filters
$requested_repo = $search->getRepository();
$components = [];
foreach ($search_results as $locale => $locale_matches) {
    $search_results = ShowResults::getTMXResultsRepos($locale_matches, $data);
    if (! Project::isMetaRepository($requested_repo)) {
        $components += Project::getComponents($search_results);
    }

    if (count($locale_matches) > 0) {
        // We have results, we won't display search suggestions but search results
        $search_yields_results = true;

        $search_id = strtolower(str_replace('-', '', $locale));
        $message_count = $real_search_results > $limit_results
            ? "<span class=\"results_count_{$search_id}\">{$limit_results} results</span> out of {$real_search_results}"
            : "<span class=\"results_count_{$search_id}\">" . Utils::pluralize(count($search_results), 'result') . '</span>';

        $output[$locale] = "<h2>Displaying {$message_count} for the string "
                         . '<span class="searchedTerm">' . htmlentities($search->getSearchTerms()) . "</span> in {$locale}:</h2>";
        $output[$locale] .= ShowResults::resultsTable($search, $search_id, $search_results, $page);
    } else {
        $output[$locale] = '<h2>No matching results for the string '
                         . '<span class="searchedTerm">' . htmlentities($search->getSearchTerms()) . '</span>'
                         . " for the locale {$locale}</h2>";
    }
}

// Remove duplicated components
$components = array_unique($components);

// Display a search hint for the closest string we have if we have no search results
if (! $search_yields_results) {
    $merged_strings = [];

    foreach ($data as $locale => $locale_strings) {
        foreach ($locale_strings as $repo => $repo_strings) {
            $merged_strings = array_merge($merged_strings, array_values($repo_strings));
        }
    }

    $best_matches = Strings::getSimilar($search->getSearchTerms(), $merged_strings, 3);

    include VIEWS . 'results_similar.php';

    return;
} else {
    // Build components filter
    if (Project::isDesktopRepository($search->getRepository())) {
        $filter_block = ShowResults::buildComponentsFilter($components);
    }
}
