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
$search_results = $results->getTMXResults(array_keys($locale1_strings), $tmx_source, $tmx_target, $tmx_target2);
echo '<h2><span class="searchedTerm">' . $initial_search . '</span> in ' . $sourceLocale . ':</h2>';
echo ShowResults::resultsTable($search_results, $initial_search, $sourceLocale, $locale,  $locale2, $check);

$search_results = Utils::results(array_keys($locale2_strings), $tmx_source, $tmx_target, $tmx_target2);
echo '<h2><span class="searchedTerm">' . $initial_search . '</span> in ' . $locale . ':</h2>';
echo ShowResults::resultsTable($search_results, $initial_search, $sourceLocale, $locale, $locale2 ,$check);

if ($locale != $locale2) {
	$search_results = Utils::results(array_keys($locale3_strings), $tmx_source, $tmx_target, $tmx_target2);

	echo '<h2><span class="searchedTerm">' . $initial_search . '</span> in ' . $locale2 . ':</h2>';
	echo ShowResults::resultsTable($search_results, $initial_search, $sourceLocale, $locale, $locale2, $check);
        }




