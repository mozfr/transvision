<?php
namespace Transvision;

// RTL support
$direction1 = RTLSupport::getDirection($source_locale);
$direction2 = RTLSupport::getDirection($locale);

if ($url['path'] == '3locales') {
    $direction3 = RTLSupport::getDirection($locale2);
    $extra_column_header = "<th>{$locale2}</th>";
} else {
    $extra_column_header = '';
}

// Trim the search query when searching for entities, display a warning
if ($search->getSearchTerms() !== trim($search->getSearchTerms())) {
    $search->setRegexSearchTerms(trim($search->getSearchTerms()));
    $warning_whitespaces = '<p id="search_warning"><strong>Warning:</strong> leading or trailing whitespaces have been automatically removed from the search query.</p>';
}

$entities = ShowResults::searchEntities($tmx_source, $search->getRegex());

$real_search_results = count($entities);
$limit_results = 200;
// Limit results to 200 per locale
array_splice($entities, $limit_results);

// Display a search hint for the closest string we have if we have no search results
if (count($entities) == 0) {
    $merged_strings = [];

    $best_matches = Strings::getSimilar($search->getSearchTerms(), array_keys($tmx_source), 3);

    include VIEWS . 'results_similar.php';

    return;
}
