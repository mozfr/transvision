<?php
namespace Transvision;

// Include all strings
$tmx_source = Utils::getRepoStrings($source_locale, $search->getRepository());
$tmx_target = Utils::getRepoStrings($locale, $search->getRepository());
$locale1_strings = $tmx_source;
$search_terms = Utils::uniqueWords($search->getSearchTerms());

foreach ($search_terms as $word) {
    $search->setRegexSearchTerms($word);
    $locale1_strings = $search->grep($locale1_strings);
}

// Limit results to 200
array_splice($locale1_strings, 200);

$perfect = $imperfect = [];

// We want to test compound words as well, /ex: 'switch to'
$compound_search = (count($search_terms) > 1) ? true : false;

foreach ($search_terms as $word) {
    // If the word is one or two letters, we skip it
    if (mb_strlen($word) < 3) {
        continue;
    }

    // Perfect matches are hits for a single word or a compound word
    if ($compound_search || count($search_terms) == 1) {
        $alternate1 = ucfirst($word);
        $alternate2 = ucwords($word);
        $alternate3 = strtolower($word);
        if (in_array($word, $tmx_source)
            || in_array($alternate1, $tmx_source)
            || in_array($alternate2, $tmx_source)
            || in_array($alternate3, $tmx_source)) {
            $perfect = array_merge($perfect, array_keys($tmx_source, $word));
            $perfect = array_merge($perfect, array_keys($tmx_source, $alternate1));
            $perfect = array_merge($perfect, array_keys($tmx_source, $alternate2));
            $perfect = array_merge($perfect, array_keys($tmx_source, $alternate3));
            $perfect = array_unique($perfect); // remove duplicates
        }
        $compound_search = false;
    }

    /*
        We use a closure here to extract imperfect matches without having to
        use a loop to search all strings.
    */
    $imperfect = array_keys(
        array_filter(
            $tmx_source,
            function ($element) use ($word) {
                $bingo = Strings::inString($element, $word);
                if (!$bingo) {
                    $bingo = Strings::inString(strtolower($element), strtolower($word));
                }

                return $bingo;
            }
        )
    );
}

// Remove duplicates
$imperfect = array_unique($imperfect);

$get_results = function ($arr) use ($tmx_target) {
    $results = [];
    foreach ($arr as $val) {
        if (isset($tmx_target[$val]) && $tmx_target[$val] != '') {
            $results[$val] = $tmx_target[$val];
        }
    }

    $results = array_unique($results);

    return $results;
};

$perfect_results = $get_results($perfect);
$imperfect_results = $get_results($imperfect);
