<?php
namespace Transvision;

if ($search->isEntireString()) {
    $locale1_strings = $search->grep($tmx_source);
    $locale2_strings = $search->grep($tmx_target);
} else {
    $locale1_strings = $tmx_source;
    $locale2_strings = $tmx_target;
    $search_terms = $search->isEachWord()
        ? Utils::uniqueWords($search->getSearchTerms())
        : [$search->getSearchTerms()];
    foreach ($search_terms as $word) {
        $search->setRegexSearchTerms($word);
        $locale1_strings = $search->grep($locale1_strings);
        $locale2_strings = $search->grep($locale2_strings);
    }
}

if ($search->getSearchType() == 'strings_entities') {
    $entities = ShowResults::searchEntities($tmx_source, $search->getRegex());
    foreach ($entities as $entity) {
        $locale1_strings[$entity] = $tmx_source[$entity];
    }
}

$real_search_results = count($locale1_strings);
$limit_results = 200;
// Limit results to 200 per locale
array_splice($locale1_strings, $limit_results);
array_splice($locale2_strings, $limit_results);

$searches = [
    $source_locale => $locale1_strings,
    $locale        => $locale2_strings,
];

$data = [$tmx_source, $tmx_target];

// 3locales view
if ($url['path'] == '3locales') {
    $check['extra_locale'] = $locale2;
    $searches[$locale2] = $locale3_strings;
    $data[] = $tmx_target2;
}

$search_yields_results = false;

// This will hold the components names for the search filters
$components = [];
foreach ($searches as $key => $value) {
    $search_results = ShowResults::getTMXResults(array_keys($value), $data);
    $components += Project::getComponents($search_results);

    if (count($value) > 0) {
        // We have results, we won't display search suggestions but search results
        $search_yields_results = true;

        $search_id = strtolower(str_replace('-', '', $key));
        $message_count = $real_search_results > $limit_results
            ? "<span class=\"results_count_{$search_id}\">{$limit_results} results</span> out of {$real_search_results}"
            : "<span class=\"results_count_{$search_id}\">" . Utils::pluralize(count($search_results), 'result') . '</span>';

        $output[$key] = "<h2>Displaying {$message_count} for the string "
                        . '<span class="searchedTerm">' . htmlentities($my_search) . "</span> in {$key}:</h2>";
        $output[$key] .= ShowResults::resultsTable($search, $search_results, $page);
    } else {
        $output[$key] = '<h2>No matching results for the string '
                        . '<span class="searchedTerm">' . htmlentities($my_search) . '</span>'
                        . " for the locale {$key}</h2>";
    }
}

// Remove duplicated components
$components = array_unique($components);

// Display a search hint for the closest string we have if we have no search results
if (! $search_yields_results) {
    $merged_strings = [];

    foreach ($data as $key => $values) {
        $merged_strings = array_merge($merged_strings, array_values($values));
    }

    $best_matches = Strings::getSimilar($search->getSearchTerms(), $merged_strings, 3);

    include VIEWS . 'results_similar.php';

    return;
} else {
    if (Project::isDesktopRepository($search->getRepository())) {
        // Build logic to filter components
        $filter_block = '';
        foreach ($components as $value) {
            $filter_block .= " <a href='#{$value}' id='{$value}' class='filter'>{$value}</a>";
        }
    }
}
