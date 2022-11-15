<?php
namespace Transvision;

$tmx_target2 = Utils::getRepoStrings($locale2, $search->getRepository(), false);

if ($search->isEntireString()) {
    $locale3_strings = $search->grep($tmx_target2, false);
} else {
    $locale3_strings = $tmx_target2;

    $search_terms = $search->isEachWord()
        ? Utils::uniqueWords($search->getSearchTerms())
        : [$search->getSearchTerms()];

    foreach ($search_terms as $word) {
        $search->setRegexSearchTerms($word);
        $locale3_strings = $search->grep($locale3_strings, false);
    }
}

// Flatten results to be able to count and slice them
$locale3_strings = Utils::flattenTMX($locale3_strings);
array_splice($locale3_strings, 200);

foreach (Project::getRepositories() as $repository) {
    $loc_list[$repository] = Project::getRepositoryLocales($repository);
    $target_locales_list2[$repository] = Utils::getHtmlSelectOptions($loc_list[$repository], $locale2);
}
