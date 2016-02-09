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
    ? Project::getLocaleRepositories($request->parameters[4])
    : [$request->parameters[2]];

// This is the filtered data we will send to getTranslationMemoryResults()
$output = [];

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

    foreach ($terms as $word) {
        $search->setRegexSearchTerms($word);
        $source_strings = preg_grep($search->getRegex(), $source_strings);
    }

    /*
        If we don't have any match for a repo, no need to do heavy calculations,
        just skip to the next repo.
    */
    if (empty($source_strings)) {
        continue;
    }

    /*
        We are only interested in target strings with keys in common with our
        source strings.
    */
    $target_strings = Utils::getRepoStrings($request->parameters[4], $repository);

    foreach ($source_strings as $key => $value) {
        if (isset($target_strings[$key]) && ! empty($target_strings[$key])) {
            $output[] = [
                $value,
                $target_strings[$key],
            ];
        }
    }
    unset($source_strings, $target_strings);
}

return $json = ShowResults::getTranslationMemoryResults(
    $output,
    $initial_search,
    $get_option('max_results'), // Cap results with the ?max_results=number option
    $get_option('min_quality') // Optional quality threshold defined by ?min_quality=50
);
