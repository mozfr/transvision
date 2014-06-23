<?php
namespace Transvision;

// Redirect old JSON API calls to new API
if (isset($_GET['json'])) {

    $repo   = Utils::secureText($_GET['repo']);
    $type   = Utils::secureText($_GET['search_type']);
    $source = Utils::secureText($_GET['sourcelocale']);
    $target = Utils::secureText($_GET['locale']);
    $terms  = Utils::secureText($_GET['recherche']);

    $regex = [];
    $regex['whole']   = isset($_GET['whole_word']) ? 'whole_word=1' : '';
    $regex['case']    = isset($_GET['case_sensitive']) ? 'case_sensitive=1' : '';
    $regex['perfect'] = isset($_GET['perfect_match']) ? 'perfect_match=1' : '';
    $regex = array_filter($regex);
    $regex = count($regex) > 0 ? '?' . implode('&', $regex) : '';

    header('Status: 301 Moved Permanently', false, 301);
    header("Location: http://{$_SERVER['HTTP_HOST']}/api/v1/search/"
           . "{$type}/{$repo}/{$source}/{$target}/{$terms}/{$regex}");
    exit;
}

// Bootstrap l10n
require_once INC . 'l10n-init.php';

// Include Search Options
require_once INC . 'search_options.php';

// Prepare extra data for the 3 locales view
if ($url['path'] == '3locales') {
    require_once MODELS . '3locales_search.php';
}

// The search form is shared by all search views
require_once VIEWS . 'search_form.php';

// Count the number of requests we receive
if ($initial_search != '') {
    include INC . 'search_counter.php';
}

// Search results process
if ($check['t2t']) {
    require_once MODELS . 'mainsearch_glossary.php';
    require_once VIEWS . 'results_glossary.php';
} else {

    // No search
    if ($my_search == '') {
        return;
    }

    // Search not acceptable
    if (mb_strlen(trim($my_search)) < 2) {
        print '<p><strong>Search term should be at least 2 characters long.</strong></p>';
        return;
    }

    // Valid search, we load all the strings
    $tmx_source = Utils::getRepoStrings($source_locale, $check['repo']);
    $tmx_target = Utils::getRepoStrings($locale, $check['repo']);

    if ($check['search_type'] == 'entities') {
        require_once MODELS . 'mainsearch_entities.php';
        require_once VIEWS . 'results_entities.php';
    } else {
        require_once MODELS . 'mainsearch_strings.php';
        require_once VIEWS . 'results_strings.php';
    }
}
