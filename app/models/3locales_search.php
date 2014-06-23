<?php
namespace Transvision;

$tmx_target2 = Utils::getRepoStrings($locale2, $check['repo']);

if ($check['perfect_match']) {
    $locale3_strings = preg_grep($regex, $tmx_target2);
} else {
    $locale3_strings = $tmx_target2;
    foreach (Utils::uniqueWords($initial_search) as $word) {
        $regex = $delimiter . $whole_word . preg_quote($word, $delimiter) . $whole_word . $delimiter . $case_sensitive;
        $locale3_strings = preg_grep($regex, $locale3_strings);
    }
}

array_splice($locale3_strings, 200);

foreach (Project::getRepositories() as $repository) {
    $loc_list[$repository] = Project::getRepositoryLocales($repository);
    $target_locales_list2[$repository] = Utils::getHtmlSelectOptions($loc_list[$repository], $locale2);
}
