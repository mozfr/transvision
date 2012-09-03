<?php

if (!valid($valid)) return;

// The search results are displayed into a table
// (initial_search is the original sanitized searched string before any modification)

$search_results = results(array_flip($keys), $tmx_source, $tmx_target);
echo '  <h2><span class="searchedTerm">' . $initial_search . '</span> is in English in:</h2>';
echo resultsTable($search_results, $recherche, $sourceLocale, $locale, false, $check);

$search_results = results(array_flip($keys2), $tmx_source, $tmx_target);
echo '  <h2><span class="searchedTerm">' . $initial_search . '</span> is in ' . $locale . ' in:</h2>';
echo resultsTable($search_results, $recherche, $sourceLocale, $locale, true, $check);

