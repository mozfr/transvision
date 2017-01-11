<?php
namespace Transvision;

// Redirect old JSON API calls to new API
if (isset($_GET['json'])) {

    // Define sane fallback values to redirect old API calls to new API calls
    $get_value = function ($value, $fallback) {
        if (isset($_GET[$value])) {
            // 'strings_entities' search is now called 'all' in new API
            return $_GET[$value] == 'strings_entities'
                ? 'all'
                : Utils::secureText($_GET[$value]);
        }

        return $fallback;
    };

    $repo = $get_value('repo', 'release');
    $type = $get_value('search_type', 'strings');
    $source = $get_value('sourcelocale', Project::getReferenceLocale($repo));
    $target = $get_value('locale', 'fr');

    /*
        We need to urlencode() twice because Apache doesn't allow urls with
        escaped slashes, it gives a 404 instead of going through mod_rewrite
        see: http://www.leakon.com/archives/865
     */
    $terms = isset($_GET['recherche'])
        ? urlencode(urlencode(Utils::cleanString($_GET['recherche'])))
        : '';

    $regex = [];
    $regex['each_word'] = isset($_GET['each_word']) ? 'each_word=each_word' : '';
    $regex['case_sensitive'] = isset($_GET['case_sensitive']) ? 'case_sensitive=case_sensitive' : '';
    $regex['entire_string'] = isset($_GET['entire_string']) ? 'entire_string=entire_string' : '';
    $regex['entire_words'] = isset($_GET['entire_words']) ? 'entire_words=entire_words' : '';
    $regex = array_filter($regex);
    $regex = count($regex) > 0 ? '?' . implode('&', $regex) : '';

    header('Status: 301 Moved Permanently', false, 301);
    header('Location:' . APP_SCHEME . "{$_SERVER['HTTP_HOST']}/api/v1/search/"
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
if ($search->getSearchTerms() != '') {
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
    $tmx_source = Utils::getRepoStrings($source_locale, $search->getRepository());
    $tmx_target = Utils::getRepoStrings($locale, $search->getRepository());

    if ($search->getSearchType() == 'entities') {
        require_once MODELS . 'mainsearch_entities.php';
        require_once VIEWS . 'results_entities.php';
    } else {
        require_once MODELS . 'mainsearch_strings.php';
        require_once VIEWS . 'results_strings.php';
    }
}
