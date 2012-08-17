<?php

if (!valid($valid)) return;

// The search results are displayed into a table
// (original_search is the original searched string before any modification)
echo '  <h2><span class="searchedTerm">' . $initial_search . '</span> is in English in:</h2>';
$search_results = results($keys, $tmx_target);
echo resultsTable($search_results, $recherche, 'en-US', $locale, false, $check['repo']);

echo '  <h2><span class="searchedTerm">' . $initial_search . '</span> is in ' . $locale . ' in:</h2>';
$search_results = results($keys2, $tmx_target);
echo resultsTable($search_results, $recherche, 'en-US', $locale, true, $check['repo']);

