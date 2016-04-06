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

// Display a search hint for the closest string we have if we have no search results
if (count($entities) == 0) {
    $merged_strings = [];

    $best_matches = Strings::getSimilar($search->getSearchTerms(), array_keys($tmx_source), 3);

    include VIEWS . 'results_similar.php';

    return;
}
