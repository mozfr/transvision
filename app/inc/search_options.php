<?php
namespace Transvision;

// Default search value
$_GET['recherche'] = isset($_GET['recherche']) ? $_GET['recherche'] : '';
$my_search = Utils::cleanString($_GET['recherche']);

// Checkboxes states
$check = [];

foreach ($search->getFormCheckboxes() as $val) {
    $check[$val] = isset($_GET[$val]);
}

// Check for default_repository cookie if we don't have a GET value
if (! isset($_GET['repo']) && isset($_COOKIE['default_repository'])) {
    $repo = $_COOKIE['default_repository'];
}

if (isset($_GET['search_type'])) {
    $search_type = Utils::secureText($_GET['search_type']);
} elseif (isset($_COOKIE['default_search_type'])) {
    $search_type = $_COOKIE['default_search_type'];
} else {
    $search_type = $search->getSearchType();
}

// Define our regex and search parameters
$search
    ->setSearchTerms($my_search)
    ->setDistinctWords($check['distinct_words'])
    ->setRegexCaseInsensitive($check['case_sensitive'])
    ->setRegexEntireString($check['entire_string'])
    ->setRepository($repo)
    ->setSearchType($search_type)
    ->setLocales([$source_locale, $locale, $locale2]);

// Build the repository switcher
$repo_list = Utils::getHtmlSelectOptions(
    $repos_nice_names,
    $search->getRepository(),
    true
);

// Get the locale list for every repo and build its target/source locale switcher values.
$source_locales_list = [];
$target_locales_list = [];

foreach (Project::getRepositories() as $repository) {
    // Closure to build the source locale switcher
    $select_options = function ($locale) use ($repository) {
        return Utils::getHtmlSelectOptions(
            Project::getRepositoryLocales($repository),
            Project::getLocaleInContext($locale, $repository)
        );
    };

    // Build the source locale HTML switcher
    $source_locales_list[$repository] = $select_options($source_locale);

    // Build the target locale HTML switcher
    $target_locales_list[$repository] = $select_options($locale);

    // 3locales view: build the target locale HTML switcher for a second locale
    $target_locales_list2[$repository] = $select_options($locale2);
}

// Build the search type switcher
$search_type_descriptions = [
    'strings'          => 'Strings',
    'entities'         => 'Entities',
    'strings_entities' => 'Strings & Entities',
];

$search_type_list = Utils::getHtmlSelectOptions(
    $search_type_descriptions,
    $search->getSearchType(),
    true
);

// Get status of cookies
$get_cookie = function ($var) {
    return isset($_COOKIE[$var]) ? $_COOKIE[$var] : '';
};

$cookies = [
    'repository'     => $get_cookie('default_repository'),
    'source_locale'  => $get_cookie('default_source_locale'),
    'target_locale'  => $get_cookie('default_target_locale'),
    'target_locale2' => $get_cookie('default_target_locale2'),
    'search_type'    => $get_cookie('default_search_type'),
];
