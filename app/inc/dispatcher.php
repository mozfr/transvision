<?php
namespace Transvision;

if ($api_url) {
    $page = 'api';
} else {
    $page = isset($urls[$url['path']]) ? $urls[$url['path']] : 'notfound';
}

$template = true;
$extra = null;
$experimental = false;
$show_title = true;
$css_files = ['transvision.css'];
$js_files = ['/js/base.js'];

switch ($url['path']) {
    case '/':
        $controller = 'mainsearch';
        $show_title = false;
        $js_files[] = '/js/component_filter.js';
        $js_files[] = '/js/main_search.js';
        $js_files[] = '/js/sorttable.js';
        $js_files[] = '/js/toggle_transliterated_string.js';
        $js_files[] = '/assets/jQuery-Autocomplete/dist/jquery.autocomplete.min.js';
        break;
    case '3locales':
        $controller = 'mainsearch';
        $show_title = true;
        $page_title = '3 locales search';
        $page_descr = 'One source locale, get search results for two target locales';
        $js_files[] = '/js/component_filter.js';
        $js_files[] = '/js/main_search.js';
        $js_files[] = '/js/sorttable.js';
        $js_files[] = '/assets/jQuery-Autocomplete/dist/jquery.autocomplete.min.js';
        break;
    case 'accesskeys':
        $controller = 'accesskeys';
        $page_title = 'Access Keys';
        $page_descr = 'Check your access keys.';
        $js_files[] = '/js/component_filter.js';
        $js_files[] = '/js/sorttable.js';
        break;
    case Strings::StartsWith($url['path'], 'api'):
        $controller = 'api';
        $page_title = 'API response';
        $page_descr = '';
        $template = false;
        break;
    case 'channelcomparison':
        $controller = 'channelcomparison';
        $page_title = 'Channel Comparison';
        $page_descr = 'Compare strings from channel to channel.';
        $js_files[] = '/js/select_column.js';
        $js_files[] = '/js/sorttable.js';
        break;
    case 'consistency':
        $experimental = true;
        $controller = 'consistency';
        $page_title = 'Translation Consistency';
        $page_descr = 'Analyze translation consistency across repositories.';
        break;
    case 'credits':
        $view = 'credits';
        $page_title = 'Credits';
        $page_descr = '';
        break;
    case 'downloads':
        $controller = 'tmx_downloads';
        $page_title = 'TMX Download';
        $page_descr = 'Create and download your own <abbr title="Translation Memory eXchange">TMX</abbr> file containing the strings you need.';
        $css_files[] = 'tmx.css';
        $js_files[] = '/js/select_all.js';
        break;
    case 'empty-strings':
        $experimental = true;
        $controller = 'empty_strings';
        $page_title = 'Empty Strings';
        $page_descr = '';
        $js_files[] = '/js/component_filter.js';
        $js_files[] = '/js/sorttable.js';
        break;
    case 'news':
        $controller = 'changelog';
        $page_title = 'Transvision News and Release Notes';
        $page_descr = '';
        $css_files[] = 'changelog.css';
        break;
    case 'productization':
        $view = 'productization';
        $page_title = 'Productization Overview';
        $page_descr = 'Show productization aspects for this locale.';
        $css_files[] = 'productization.css';
        break;
    case 'rss':
        $controller = 'changelog';
        $template = false;
        break;
    case 'showrepos':
        $experimental = true;
        $controller = 'health_status';
        $page_title = 'Health status';
        $page_descr = 'Check the health status of locales.';
        $css_files[] = 'health.css';
        $js_files[] = '/js/show_hide_tabs.js';
        break;
    case 'stats':
        $controller = 'showrepos';
        $page_title = 'Status Overview';
        $page_descr = 'Repository status overview.';
        $js_files[] = '/js/sorttable.js';
        break;
    case 'string':
        $controller = 'onestring';
        $page_title = 'All translations for this string:';
        $page_descr = '';
        break;
    case 'unchanged':
        $controller = 'unchanged_strings';
        $page_title = 'Unchanged Strings';
        $page_descr = 'Display a list of strings identical to English';
        $js_files[] = '/js/component_filter.js';
        $js_files[] = '/js/sorttable.js';
        break;
    case 'unlocalized':
        $experimental = true;
        $controller = 'unlocalized_words';
        $page_title = 'Commonly Unlocalized Words';
        $page_descr = 'Display the list of the most common untranslated words. Click on the table headers to sort results.';
        $js_files[] = '/js/sorttable.js';
        break;
    case 'unlocalized-all':
        $experimental = true;
        $controller = 'unlocalized_words';
        $page_title = 'Commonly Unlocalized Words (Global view)';
        $page_descr = 'Display the list of the most common untranslated words for all locales. Click on the table headers to sort results.';
        $js_files[] = '/js/sorttable.js';
        $js_files[] = '/js/hide_table_rows.js';
        $js_files[] = '/js/toggle_checkboxes.js';
        break;
    case 'unlocalized-json':
        $controller = 'unlocalized_words';
        $template = false;
        break;
    case 'variables':
        $controller = 'check_variables';
        $page_title = 'Variables Overview';
        $page_descr = 'Show potential errors related to missing or mispelled variables in your strings.';
        $js_files[] = '/js/component_filter.js';
        $js_files[] = '/js/sorttable.js';
        break;
    default:
        $view = '404';
        $page_title = '404: Page Not Found';
        $page_descr = '';
        break;
}

if ($template) {
    ob_start();

    if (isset($view)) {
        include VIEWS . $view . '.php';
    } else {
        include CONTROLLERS . $controller . '.php';
    }

    $content = ob_get_contents();
    ob_end_clean();

    ob_start();
    // display the page
    require_once VIEWS . 'templates/base.php';
    $content = ob_get_contents();
    ob_end_clean();
} else {
    ob_start();
    if (isset($view)) {
        include VIEWS . $view . '.php';
    } else {
        include CONTROLLERS . $controller . '.php';
    }
    $content = ob_get_contents();
    ob_end_clean();
}

ob_start();

// Log script performance in the HTTP headers sent to the browser
Utils::addPerformancesHTTPHeader();
$perf_header = ob_get_contents();

// Log script performance in PHP integrated developement server console
Utils::logScriptPerformances();

ob_end_clean();

print $perf_header . $content;

die;
