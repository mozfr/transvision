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

$entities = ShowResults::searchEntities($tmx_source, $search->getRegex());

$real_search_results = count($entities);
$limit_results = 200;
// Limit results to 200 per locale
array_splice($entities, $limit_results);

// Display a search hint for the closest string we have if we have no search results
if (count($entities) == 0) {
    $all_entities = [];
    foreach (array_keys($tmx_source) as $repo) {
        $all_entities = array_merge($all_entities, $tmx_source[$repo]);
    }
    $best_matches = Strings::getSimilar($search->getSearchTerms(), $all_entities, 3);

    include VIEWS . 'results_similar.php';

    return;
}
