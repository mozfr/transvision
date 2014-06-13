<?php
namespace Transvision;

// Include all strings
$tmx_source = Utils::getRepoStrings($source_locale, $check['repo']);
$tmx_target = Utils::getRepoStrings($locale, $check['repo']);

// Regex options
$whole_word     = $check['whole_word']     ? '\b' : '';
$case_sensitive = $check['case_sensitive'] ? '' : 'i';

$delimiter = '~';
$regex = $delimiter . $whole_word . $my_search . $whole_word . $delimiter . $case_sensitive;
$entities = preg_grep($regex, array_keys($tmx_source));

if ($check['perfect_match']) {
    $locale1_strings = preg_grep($regex, $tmx_source);
    $locale2_strings = preg_grep($regex, $tmx_target);
} else {
    $search = Utils::uniqueWords($initial_search);
    $locale1_strings = $tmx_source;
    $locale2_strings = $tmx_target;
    foreach ($search as $word) {
        $regex = $delimiter . $whole_word . preg_quote($word, $delimiter) . $whole_word . $delimiter . $case_sensitive;
        $locale1_strings = preg_grep($regex, $locale1_strings);
        $locale2_strings = preg_grep($regex, $locale2_strings);
    }
}

// Limit results to 200 per locale
array_splice($locale1_strings, 200);
array_splice($locale2_strings, 200);

if ($check['search_type'] == 'strings_entities') {
    foreach ($entities as $entity) {
        $locale1_strings[$entity] = $tmx_source[$entity];
    }
}

// build the repository switcher
$repo_list = Utils::getHtmlSelectOptions($repos_nice_names, $check['repo'], true);

// Get the locale list for every repo and build his target/source locale switcher values.
$loc_list = [];
$source_locales_list  = [];
$target_locales_list  = [];

$repositories = Project::getRepositories();

foreach (Project::getRepositories() as $repository) {
    $loc_list[$repository] = Project::getRepositoryLocales($repository);

    // build the source locale switcher
    $source_locales_list[$repository] = Utils::getHtmlSelectOptions(
        $loc_list[$repository],
        Project::getReferenceLocale($repository)
    );

    // build the target locale switcher
    $target_locales_list[$repository] = Utils::getHtmlSelectOptions($loc_list[$repository], $locale);

    // 3locales view: build the target locale switcher for a second locale
    $target_locales_list2[$repository] = Utils::getHtmlSelectOptions($loc_list[$repository], $locale2);
}

// Build the search type switcher
$search_type_descriptions = [
    'strings' => 'Strings',
    'entities'=> 'Entities',
    'strings_entities' => 'Strings & Entities'
];

$search_type_list = Utils::getHtmlSelectOptions(
    $search_type_descriptions,
    $check['search_type'],
    true
);

// Get COOKIES
$get_cookie = function($var) {
    return isset($_COOKIE[$var]) ? $_COOKIE[$var] : '';
};

$cookie_repository     = $get_cookie('default_repository');
$cookie_source_locale  = $get_cookie('default_source_locale');
$cookie_target_locale  = $get_cookie('default_target_locale');
$cookie_target_locale2 = $get_cookie('default_target_locale2');
