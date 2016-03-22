<?php
namespace Transvision;

// Closure to use extra parameters
$get_option = function ($option) use ($request) {
    if (isset($request->extra_parameters[$option])) {
        return $request->extra_parameters[$option];
    }

    return false;
};

// Get all strings
$initial_search = urldecode(Utils::cleanString($request->parameters[6]));

$repositories = $request->parameters[3] == 'global'
    ? Project::getRepositories()
    : [$request->parameters[3]];

$entities_merged       = [];
$source_strings_merged = [];
$target_strings_merged = [];

// Define our regex
$search
    ->setSearchTerms(Utils::cleanString($initial_search))
    ->setRegexWholeWords($get_option('whole_word'))
    ->setRegexCaseInsensitive($get_option('case_sensitive'))
    ->setRegexPerfectMatch($get_option('perfect_match'))
    ->setRepository($request->parameters[3]);

// We loop through all repositories searched and merge results
foreach ($repositories as $repository) {
    $source_strings = Utils::getRepoStrings($request->parameters[4], $repository);

    if ($search->isPerfectMatch()) {
        if ($request->parameters[2] == 'entities') {
            $entities = ShowResults::searchEntities($source_strings, $search->getRegex());
            $source_strings = array_intersect_key($source_strings, array_flip($entities));
        } else {
            $source_strings = $search->grep($source_strings);
            $entities = array_keys($source_strings);
        }
    } else {
        foreach (Utils::uniqueWords($initial_search) as $word) {
            $search->setRegexSearchTerms($word);
            if ($request->parameters[2] == 'entities') {
                $entities = ShowResults::searchEntities($source_strings, $search->getRegex());
                $source_strings = array_intersect_key($source_strings, array_flip($entities));
            } else {
                $source_strings = $search->grep($source_strings);
                $entities = array_keys($source_strings);
            }
        }
    }

    // We have our list of filtered source strings, get corresponding target locale strings
    $target_strings = array_intersect_key(
        Utils::getRepoStrings($request->parameters[5], $repository),
        array_flip($entities)
    );

    $source_strings_merged = array_merge($source_strings, $source_strings_merged);
    $target_strings_merged = array_merge($target_strings, $target_strings_merged);
    $entities_merged = array_merge($entities, $entities_merged);
}

// We sort arrays by key before array_splice() to keep matching keys
ksort($source_strings_merged);
ksort($target_strings_merged);

// Limit results to 200
array_splice($source_strings_merged, 500);
array_splice($target_strings_merged, 500);

return $json = ShowResults::getRepositorySearchResults(
    $entities_merged,
    [$source_strings_merged, $target_strings_merged]
);
