<?php
namespace Transvision;

// Closure to get extra parameters set
$get_option = function ($option) use ($request) {
    $value = 0;
    if (isset($request->extra_parameters[$option])
        && (int) $request->extra_parameters[$option] != 0) {
        $value = (int) $request->extra_parameters[$option];
    }

    return $value;
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
$search = (new Search)
    ->setSearchTerms(Utils::cleanString($initial_search))
    ->setRegexWholeWords($get_option('whole_word'))
    ->setRegexCaseInsensitive($get_option('case_sensitive'))
    ->setRegexPerfectMatch($get_option('perfect_match'));

// We loop through all repositories and merge the results
foreach ($repositories as $repository) {
    $source_strings = Utils::getRepoStrings($request->parameters[3], $repository);
    $target_strings = Utils::getRepoStrings($request->parameters[4], $repository);

    foreach ($terms as $word) {
        $search->setRegexSearchTerms($word);
        $source_strings = preg_grep($search->getRegex(), $source_strings);
    }

    $source_strings_merged = array_merge($source_strings, $source_strings_merged);
    $target_strings_merged = array_merge($target_strings, $target_strings_merged);
    unset($source_strings, $target_strings);
}

return $json = ShowResults::getTranslationMemoryResults(
    array_keys($source_strings_merged),
    [$source_strings_merged, $target_strings_merged],
    $initial_search,
    $get_option('max_results'), // Cap results with the ?max_results=number option
    $get_option('min_quality') // Optional quality threshold defined by ?min_quality=50
);
