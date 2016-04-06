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

$entities_merged       = [];
$source_strings_merged = [];
$target_strings_merged = [];

// Define our search terms and parameters
$search
    ->setSearchTerms(urldecode(Utils::cleanString($request->parameters[6])))
    ->setRegexWholeWords($get_option('whole_word'))
    ->setRegexCaseInsensitive($get_option('case_sensitive'))
    ->setRegexPerfectMatch($get_option('perfect_match'))
    ->setSearchType($request->parameters[2])
    ->setLocales([$request->parameters[4], $request->parameters[5]]);

// We loop through all repositories searched and merge results
foreach ($repositories as $repository) {
    $source_strings = Utils::getRepoStrings($search->getLocale('source'), $repository);

    if ($search->isPerfectMatch()) {
        if ($search->getSearchType() == 'entities') {
            $entities = ShowResults::searchEntities($source_strings, $search->getRegex());
            $source_strings = array_intersect_key($source_strings, array_flip($entities));
        } else {
            $source_strings = $search->grep($source_strings);
            $entities = array_keys($source_strings);
        }
    } else {
        foreach (Utils::uniqueWords($search->getSearchTerms()) as $word) {
            $search->setRegexSearchTerms($word);
            if ($search->getSearchType() == 'entities') {
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
        Utils::getRepoStrings($search->getLocale('target'), $repository),
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
