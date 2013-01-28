<?php

if (strlen(trim($recherche)) < 2) {
    echo '<p><strong>Search term should be at least 2 characters long.</strong></p>';
    return;
}

// log requests
// $logger->addInfo($locale, array($initial_search, $check['repo']), array($check['repo']));

// The search results are displayed into a table
// (initial_search is the original sanitized searched string before any modification)

$results = new Transvision\ShowResults();
$search_results = $results->getTMXResults(array_keys($locale1_strings), $tmx_source, $tmx_target);

echo '<h2><span class="searchedTerm">' . $initial_search . '</span> is in ' . $sourceLocale . ' in:</h2>';
echo resultsTable($search_results, $initial_search, $sourceLocale, $locale, $check);

$search_results = results(array_keys($locale2_strings), $tmx_source, $tmx_target);

echo '<h2><span class="searchedTerm">' . $initial_search . '</span> is in ' . $locale . ' in:</h2>';
echo resultsTable($search_results, $initial_search, $sourceLocale, $locale, $check);
