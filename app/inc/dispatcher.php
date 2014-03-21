<?php
namespace Transvision;

$template     = true;
$page         = $urls[$url['path']];
$extra        = null;
$experimental = false;
$show_title   = true;

$title = '<a href="/" id="transvision-title">Transvision</a>';

// Bootstrap l10n for all views, this way locale is always set
require_once INC . 'l10n-init.php';

switch ($url['path']) {
    case '/':
        // Include Search Options
        require_once INC . 'search_options.php';

        // Import all strings for source and target locales + search process
        require_once INC . 'recherche.php';

        if (JSON_API) {
            $view = 'json_api';
            $template = false;
            break;
        }
        $view  = 'search_form';
        $show_title = false;
        break;
    case '3locales':
        // Include Search Options
        require_once INC . 'search_options.php';

        // Import all strings for source and target locales + search process
        require_once INC . 'recherche.php';
        $view  = 'search_form';
        $page_title = '3 locales search';
        $page_descr = 'One source locale, get search results for two target locales';
        $show_title = true;
        break;
    case 'news':
        $view  = 'changelog';
        $page_title = 'Transvision News. Version Notes';
        $page_descr = '';
        // Unset $locale for cache purposes (page is identical for all locales)
        $locale = '';
        break;
    case 'stats':
        $view  = 'stats';
        $page_title = 'Statistics';
        $page_descr = 'Light usage statistics.';
        break;
    case 'repocomparison':
        $view = 'repocomparison';
        break;
    case 'gaia':
        $view = 'gaia';
        $experimental = true;
        $page_title = 'Gaia Comparison';
        $page_descr = 'Check the Status of your GAIA strings across repositories.';
        break;
    case 'channelcomparison':
        $view  = 'channelcomparison';
        $experimental = true;
        $page_title = 'Channel Comparison';
        $page_descr = 'Compare strings from channel to channel.';
        break;
    case 'accesskeys':
        $view  = 'accesskeys';
        $page_title = 'Access Keys';
        $page_descr = 'Check your access keys.';
        break;
    case 'credits':
        $view  = 'credits';
        $page_title = 'Credits';
        $page_descr = '';
        // Unset $locale for cache purposes (page is identical for all locales)
        $locale = '';
        break;
    case 'downloads':
        $view  = 'downloads';
        $page_title = 'TMX Download';
        $page_descr = 'Download the <abbr title="Translation Memory eXchange">TMX</abbr> files used in Transvision.';
        // Unset $locale for cache purposes (page is identical for all locales)
        $locale = '';
        break;
    case 'showrepos':
        $view  = 'showrepos';
        $experimental = true;
        $page_title = 'Status Overview';
        $page_descr = 'Repository status overview.';
        if (JSON_API) {
            $template = false;
        }
        // Unset $locale for cache purposes (page is identical for all locales)
        $locale = '';
        break;
    case 'string':
        $controller  = 'onestring';
        $page_title = 'All translations for this string:';
        $page_descr = '';
        if (JSON_API) {
            $template = false;
        }
        break;
    case 'variables':
        $view  = 'checkvariables';
        $experimental = true;
        $page_title = 'Variables Overview';
        $page_descr = 'Show potential errors in your strings for the use of variables.';
        break;
    case 'productization':
        $view  = 'productization';
        $experimental = true;
        $page_title = 'Productization Overview';
        $page_descr = 'Show productization aspects for this locale.';
        break;
    default:
        $view  = 'search';
        break;
}

// NOCACHE is defined in constants.php, it's enabled by passing a param
// "nocache" to the request (value is irrelevant).

// Define type of request
if ($template) {
    // There's a template, it's a standard HTML page
    $type = 'html';
}  else {
    if (isset($_GET['callback'])) {
            // There's a callback, type is jsonp
            $type = 'jsonp';
        } else {
            // No callback, type is json
            $type = 'json';
        }
}

if (NOCACHE) {
    $cache_id = false;
} else {
    $cacheView = new CacheViews;
    // Store content of $_REQUEST, overwrite locale if is in $_REQUEST, add
    // name of the current view and type
    $request_params = $_REQUEST;
    $request_params['locale'] = $locale;
    if (isset($view)) {
        $request_params['viewname'] = $view;
    } else {
        $request_params['viewname'] = $controller;
    }
    $request_params['type'] = $type;
    ksort($request_params);
    // Check if a valid cache entry exists for this request
    $cache_id = $cacheView->searchCacheEntry(var_export($request_params, true));
    if ($cache_id) {
        // Read and display the cache file
        $cacheView::readCacheFile($cache_id, $type);
        if (DEBUG && $template) {
            // Debug info (only for HTML pages)
            $memory_usage = round(memory_get_usage()/1024, 0);
            echo "\n<!-- Memory usage: {$memory_usage} kB -->";
            echo "\n<!-- CACHED - Elapsed time (s): " . round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 4) . " --> \n";
        }
    }
}

if (! $cache_id) {
    // I don't have a valid cache entry for this request.
    // First step: store content generated in the requested view
    ob_start();
    if (isset($view)) {
        include VIEWS . $view . '.php';
    } else {
        include CONTROLLERS . $controller . '.php';
    }
    $view_content = ob_get_contents();
    ob_end_clean();

    // If it's a HTML page, I need also to include the template
    if ($template) {
        ob_start();
        require_once VIEWS .'template.php';
        $html_output = ob_get_contents();
        ob_end_clean();
    } else {
        // Send view_content to Json:output, requesting a full (with headers)
        // json/jsonp output without decoding
        $html_output = Json::output($view_content, ($type == 'jsonp') ? true : false, false, false, true);
    }

    // Output the complete page
    print $html_output;

    if (! NOCACHE) {
        // Cache is enabled, store the cache entry
        $cache_id = uniqid('', true);
        $cacheView->createCacheEntry($cache_id, var_export($request_params, true));
        // Store the generated HTML in a gz compressed file
        $cacheView::writeCacheFile($cache_id, $html_output);
    }

    if (DEBUG && $template) {
        // Debug info (only for HTML pages), I don't want this stored in the
        // cache file, so just echo without adding it to $html_output
        $memory_usage = round(memory_get_usage()/1024, 0);
        echo "\n<!-- Memory usage: {$memory_usage} kB -->";
        echo "\n<!-- Elapsed time (s): " . round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 4) . " -->\n\n";
    }
}
