<?php
namespace Transvision;

// Closure to use extra parameters
$get_option = function($option) use ($request) {
    if (isset($request->extra_parameters[$option])) {
        return $request->extra_parameters[$option];
    }
    return false;
};

// Get all strings
$initial_search = urldecode(Utils::cleanSearch($request->parameters[6]));
$source_strings = Utils::getRepoStrings($request->parameters[4], $request->parameters[3]);
$target_strings = Utils::getRepoStrings($request->parameters[5], $request->parameters[3]);

// Regex options
$whole_word     = $get_option('whole_word') ? '\b' : '';
$case_sensitive = $get_option('case_sensitive') ? '' : 'i';

if ($get_option('perfect_match')) {
    $regex = '~' . $whole_word . trim('^' . $initial_search . '$') . $whole_word . '~' . $case_sensitive;
    if ($request->parameters[2] == 'entities') {
        $entities = preg_grep($regex, array_keys($source_strings));
    } else {
        $source_strings = preg_grep($regex, $source_strings);
        $entities = array_keys($source_strings);
    }
} else {
    foreach (Utils::uniqueWords($initial_search) as $word) {
        $regex = '~' . $whole_word . preg_quote($word, '~') . $whole_word . '~' . $case_sensitive;
        if ($request->parameters[2] == 'entities') {
            $entities = preg_grep($regex, array_keys($source_strings));
            $source_strings = array_intersect_key($source_strings, array_flip($entities));
        } else {
            $source_strings = preg_grep($regex, $source_strings);
            $entities = array_keys($source_strings);
        }
    }
}

// Limit results to 200
array_splice($source_strings, 200);

return $json = ShowResults::getRepositorySearchResults(
    $entities,
    [$source_strings, $target_strings]
);
