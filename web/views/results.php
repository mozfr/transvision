<?php
namespace Transvision;

if (strlen(trim($my_search)) < 2) {
    echo '<p><strong>Search term should be at least 2 characters long.</strong></p>';
    return;
}

// log requests
// $logger->addInfo($locale, array($initial_search, $check['repo']), array($check['repo']));

// The search results are displayed into a table
// (initial_search is the original sanitized searched string before any modification)

$results = new ShowResults();
$search_results = $results->getTMXResults(array_keys($locale1_strings), $tmx_source, $tmx_target);

// Get cached bugzilla components (languages list) or connect to Bugzilla API to retrieve them
$components_array = Utils::getBugzillaComponents();

$source_component_name = Utils::collectLanguageComponent($sourceLocale, $components_array);
echo '<h2><span class="searchedTerm">' . $initial_search . '</span> in ' . $source_component_name . ':</h2>';
echo ShowResults::resultsTable($search_results, $initial_search, $sourceLocale, $locale, $check);

$search_results = Utils::results(array_keys($locale2_strings), $tmx_source, $tmx_target);

$target_component_name = Utils::collectLanguageComponent($locale, $components_array);
echo '<h2><span class="searchedTerm">' . $initial_search . '</span> in ' . $target_component_name . ':</h2>';
echo ShowResults::resultsTable($search_results, $initial_search, $sourceLocale, $locale, $check);
