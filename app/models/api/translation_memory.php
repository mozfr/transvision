<?php
namespace Transvision;

// get all strings
$source_strings = Utils::getRepoStrings($request->parameters[3], $request->parameters[2]);
$target_strings = Utils::getRepoStrings($request->parameters[4], $request->parameters[2]);

// The search
$initial_search = Utils::cleanSearch($request->parameters[5]);
$terms = Utils::uniqueWords($initial_search);

// Regex options (not currenty used)
$delimiter = '~';
$whole_word = isset($check['whole_word']) ? '\b' : '';
$case_sensitive = isset($check['case_sensitive']) ? '' : 'i';
$regex = $delimiter . $whole_word . $initial_search . $whole_word . $delimiter . $case_sensitive;

// Closure to get extra parameters set
$get_option = function($option) use ($request) {
    $value = 0;
    if (isset($request->extra_parameters[$option])
        && (int) $request->extra_parameters[$option] != 0) {
        $value = (int) $request->extra_parameters[$option];
    }

    return $value;
};

foreach ($terms as $word) {
    $regex = $delimiter . $whole_word . preg_quote($word, $delimiter) . $whole_word . $delimiter . $case_sensitive;
    $source_strings = preg_grep($regex, $source_strings);
}


return $json = ShowResults::getTranslationMemoryResults(
    array_keys($source_strings),
    [$source_strings, $target_strings],
    $initial_search,
    $get_option('max_results'), // Cap results with the ?max_results=number option
    $get_option('min_quality') // Optional quality threshold defined by ?min_quality=50
);
