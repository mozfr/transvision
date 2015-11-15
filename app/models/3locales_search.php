<?php
namespace Transvision;

$tmx_target2 = Utils::getRepoStrings($locale2, $check['repo']);

if ($search->isPerfectMatch()) {
    $locale3_strings = preg_grep($search->getRegex(), $tmx_target2);
} else {
    $locale3_strings = $tmx_target2;
    foreach (Utils::uniqueWords($initial_search) as $word) {
        $search->setRegexSearchTerms($word);
        $locale3_strings = preg_grep($search->getRegex(), $locale3_strings);
    }
}

array_splice($locale3_strings, 200);

foreach (Project::getRepositories() as $repository) {
    $loc_list[$repository] = Project::getRepositoryLocales($repository);
    $target_locales_list2[$repository] = Utils::getHtmlSelectOptions($loc_list[$repository], $locale2);
}
