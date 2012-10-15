<?php

// The search results are displayed into a table
// (initial_search is the original sanitized searched string before any modification)

$results = new TransvisionResults\ShowResults();
$search_results = $results->TMXResults(array_keys($keys), $tmx_source, $tmx_target);

echo '  <h2><span class="searchedTerm">' . $initial_search . '</span> is in ' . $sourceLocale . ' in:</h2>';
echo resultsTable($search_results, $recherche, $sourceLocale, $locale, false, $check);

$search_results = results(array_keys($keys2), $tmx_source, $tmx_target);

echo '  <h2><span class="searchedTerm">' . $initial_search . '</span> is in ' . $locale . ' in:</h2>';
echo resultsTable($search_results, $recherche, $sourceLocale, $locale, true, $check);
