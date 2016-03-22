<?php
namespace Transvision;

// Closure to use extra parameters
$get_option = function ($option) use ($request) {
    if (isset($request->extra_parameters[$option])) {
        return $request->extra_parameters[$option];
    }

    return false;
};

$repositories = ($request->parameters[2] == 'global')
    ? Project::getRepositories()
    : [$request->parameters[2]];

$source_strings_merged = [];
$target_strings_merged = [];

// The search
$initial_search = Utils::cleanString($request->parameters[5]);
$terms = Utils::uniqueWords($initial_search);

// Define our regex
$search
    ->setSearchTerms(Utils::cleanString($initial_search))
    ->setRegexWholeWords($get_option('whole_word'))
    ->setRegexCaseInsensitive($get_option('case_sensitive'))
    ->setRegexPerfectMatch($get_option('perfect_match'));

 // Loop through all repositories searching in both source and target languages
foreach ($repositories as $repository) {
    $source_strings = Utils::getRepoStrings($request->parameters[3], $repository);
    foreach ($terms as $word) {
        $search->setRegexSearchTerms($word);
        $source_strings = $search->grep($source_strings);
    }
    $source_strings_merged = array_merge($source_strings, $source_strings_merged);

    $target_strings = Utils::getRepoStrings($request->parameters[4], $repository);
    foreach ($terms as $word) {
        $search->setRegexSearchTerms($word);
        $target_strings = $search->grep($target_strings);
    }
    $target_strings_merged = array_merge($target_strings, $target_strings_merged);
}

// Closure to get extra parameters set
$get_option = function ($option) use ($request) {
    $value = 0;
    if (isset($request->extra_parameters[$option])
        && (int) $request->extra_parameters[$option] != 0) {
        $value = (int) $request->extra_parameters[$option];
    }

    return $value;
};

return $json = ShowResults::getSuggestionsResults(
    $source_strings_merged,
    $target_strings_merged,
    $initial_search,
    $get_option('max_results')
);
