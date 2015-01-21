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

// We loop through all repositories searched and merge results
foreach ($repositories as $repository) {
    $source_strings = Utils::getRepoStrings($request->parameters[4], $repository);

    // Regex options
    $whole_word     = $get_option('whole_word') ? '\b' : '';
    $case_sensitive = $get_option('case_sensitive') ? '' : 'i';

    if ($get_option('perfect_match')) {
        $regex = '~' . $whole_word . trim('^' . preg_quote($initial_search, '~') . '$') .
                 $whole_word . '~' . $case_sensitive . 'u';
        if ($request->parameters[2] == 'entities') {
            $entities = ShowResults::searchEntities($source_strings, $regex);
            $source_strings = array_intersect_key($source_strings, array_flip($entities));
        } else {
            $source_strings = preg_grep($regex, $source_strings);
            $entities = array_keys($source_strings);
        }
    } else {
        foreach (Utils::uniqueWords($initial_search) as $word) {
            $regex = '~' . $whole_word . preg_quote($word, '~') .
                     $whole_word . '~' . $case_sensitive . 'u';
            if ($request->parameters[2] == 'entities') {
                $entities = ShowResults::searchEntities($source_strings, $regex);
                $source_strings = array_intersect_key($source_strings, array_flip($entities));
            } else {
                $source_strings = preg_grep($regex, $source_strings);
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
