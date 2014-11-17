<?php
namespace Transvision;

if ($check['perfect_match']) {
    $locale1_strings = preg_grep($main_regex, $tmx_source);
    $locale2_strings = preg_grep($main_regex, $tmx_target);
} else {
    $locale1_strings = $tmx_source;
    $locale2_strings = $tmx_target;
    foreach (Utils::uniqueWords($initial_search) as $word) {
        $regex = $delimiter . $whole_word . preg_quote($word, $delimiter) . $whole_word . $delimiter . $case_sensitive;
        $locale1_strings = preg_grep($regex, $locale1_strings);
        $locale2_strings = preg_grep($regex, $locale2_strings);
    }
}

if ($check['search_type'] == 'strings_entities') {
    $entities = preg_grep($main_regex, array_keys($tmx_source));
    foreach ($entities as $entity) {
        $locale1_strings[$entity] = $tmx_source[$entity];
    }
}

// Limit results to 200 per locale
array_splice($locale1_strings, 200);
array_splice($locale2_strings, 200);

$searches = [
    $source_locale => $locale1_strings,
    $locale => $locale2_strings
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
    $components = Project::getComponents($search_results);

    if (count($value) > 0) {
        // We have results, we won't display search suggestions but search results
        $search_yields_results = true;
        $output[$key]  = '<h2>Matching results for the string <span class="searchedTerm">'
                         . $initial_search . '</span> in ' . $key . ':</h2>';
        $output[$key] .=  ShowResults::resultsTable($search_results, $initial_search,
                                                    $source_locale, $locale, $check);
    } else {
        $output[$key]  =  "<h2>No matching results for the string "
                        . "<span class=\"searchedTerm\">{$initial_search}</span>"
                        . " for the locale {$key}</h2>";
    }
}
// Display a search hint for the closest string we have if we have no search results
if (! $search_yields_results) {
    $merged_strings = [];

    foreach ($data as $key => $values) {
        $merged_strings = array_merge($merged_strings, array_values($values));
    }

    $best_matches = Strings::getSimilar($initial_search, $merged_strings, 3);

    include VIEWS . 'results_similar.php';
    return;
} else {
    if (in_array($check['repo'], $desktop_repos)) {
        // Build logic to filter components
        $javascript_include = ['component_filter.js'];
        $filter_block = '';
        foreach ($components as $value) {
            $filter_block .= " <a href='#{$value}' id='{$value}' class='filter'>{$value}</a>";
        }
    }
}
