<?php
namespace Transvision;

// Closure to use extra parameters
$get_option = function ($option) use ($request) {
    if (isset($request->extra_parameters[$option])) {
        return $request->extra_parameters[$option];
    }

    return false;
};

$repositories = $request->parameters[3] == 'global'
    ? Project::getRepositories()
    : [$request->parameters[3]];

$entities_merged = [];
$source_results_merged = [];
$target_results_merged = [];

// Define our search terms and parameters
$search
    ->setSearchTerms(urldecode(Utils::cleanString($request->parameters[6])))
    ->setDistinctWords($get_option('distinct_words'))
    ->setRegexCaseInsensitive($get_option('case_sensitive'))
    ->setRegexEntireString($get_option('entire_string'))
    ->setSearchType($request->parameters[2])
    ->setLocales([$request->parameters[4], $request->parameters[5]]);

// We loop through all repositories searched and merge results
foreach ($repositories as $repository) {
    $source_strings = Utils::getRepoStrings($search->getLocale('source'), $repository);
    $entities = [];
    $source_results = [];

    if ($search->isEntireString()) {
        if ($search->getSearchType() == 'entities') {
            $entities = ShowResults::searchEntities($source_strings, $search->getRegex());
            $source_results = array_intersect_key($source_strings, array_flip($entities));
        } else {
            $source_results = $search->grep($source_strings);
            $entities = array_keys($source_results);
        }
    } else {
        $search_terms = $search->isDistinctWords()
            ? Utils::uniqueWords($search->getSearchTerms())
            : [$search->getSearchTerms()];

        foreach ($search_terms as $word) {
            $search->setRegexSearchTerms($word);
            if ($search->getSearchType() == 'entities') {
                $entities += ShowResults::searchEntities($source_strings, $search->getRegex());
                $source_results += array_intersect_key($source_strings, array_flip($entities));
            } else {
                $source_results += $search->grep($source_strings);
                $entities += array_keys($source_results);
            }
        }
        $entities = array_unique($entities);
        $source_results = array_unique($source_results);
    }

    // We have our list of filtered source strings, get corresponding target locale strings
    $target_results = array_intersect_key(
        Utils::getRepoStrings($search->getLocale('target'), $repository),
        array_flip($entities)
    );

    $source_results_merged = array_merge($source_results, $source_results_merged);
    $target_results_merged = array_merge($target_results, $target_results_merged);
    $entities_merged = array_merge($entities, $entities_merged);
}

// We sort arrays by key before array_splice() to keep matching keys
ksort($source_results_merged);
ksort($target_results_merged);

// Limit results to 500
array_splice($source_results_merged, 500);
array_splice($target_results_merged, 500);

return ShowResults::getRepositorySearchResults(
    $entities_merged,
    [$source_results_merged, $target_results_merged]
);
