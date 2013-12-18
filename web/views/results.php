<?php
namespace Transvision;

if (mb_strlen(trim($my_search)) < 2) {
    echo '<p><strong>Search term should be at least 2 characters long.</strong></p>';

    return;
}

// log requests
// $logger->addInfo($locale, array($initial_search, $check['repo']), array($check['repo']));

// The search results are displayed into a table
// (initial_search is the original sanitized searched string before any modification)

$searches = [
	$source_locale => $locale1_strings,
	$locale        => $locale2_strings
];

$data = [$tmx_source, $tmx_target];

// 3locales view
if ($url['path'] == '3locales') {
	$check['extra_locale'] = $locale2;
	$searches[$locale2] = $locale3_strings;
	$data[] = $tmx_target2;

}
// error_log(count($data[0]));

foreach ($searches as $key => $value) {
	$search_results = ShowResults::getTMXResults(array_keys($value), $data);
	if (count($value) > 0) {
	    print '<h2>Matching results for the string <span class="searchedTerm">' . $initial_search . '</span> in ' . $key . ':</h2>';
		print ShowResults::resultsTable($search_results, $initial_search, $source_locale, $locale, $check);
	} else {
		print "<h2>No matching results for the string <span class=\"searchedTerm\">$initial_search</span> for the locale $key</h2>";
	}
}