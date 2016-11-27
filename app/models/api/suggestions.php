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

// Define our search terms and parameters
$search
    ->setSearchTerms(Utils::cleanString($request->parameters[5]))
    ->setEachWord($get_option('each_word'))
    ->setRegexCaseInsensitive($get_option('case_sensitive'))
    ->setRegexEntireString($get_option('entire_string'))
    ->setRegexEntireWords($get_option('entire_words'))
    ->setLocales([$request->parameters[3], $request->parameters[4]]);

$terms = Utils::uniqueWords($search->getSearchTerms());

// Loop through all repositories searching in both source and target languages
foreach ($repositories as $repository) {
    $source_strings = Utils::getRepoStrings($search->getLocale('source'), $repository);
    foreach ($terms as $word) {
        $search->setRegexSearchTerms($word);
        $source_strings = $search->grep($source_strings);
    }
    $source_strings_merged = array_merge($source_strings, $source_strings_merged);

    $target_strings = Utils::getRepoStrings($search->getLocale('target'), $repository);
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

return ShowResults::getSuggestionsResults(
    $source_strings_merged,
    $target_strings_merged,
    $search->getSearchTerms(),
    $get_option('max_results')
);
