<?php
namespace Transvision;

$template     = true;
$page         = $api_url ? 'api' : $urls[$url['path']];
$extra        = null;
$experimental = false;
$show_title   = true;

$title = '<a href="/" id="transvision-title">Transvision</a>';

switch ($url['path']) {
    case '/':
        $controller = 'mainsearch';
        $show_title = false;
        break;
    case '3locales':
        $controller = 'mainsearch';
        $show_title = true;
        $page_title = '3 locales search';
        $page_descr = 'One source locale, get search results for two target locales';
        break;
    case 'accesskeys':
        $view = 'accesskeys';
        $page_title = 'Access Keys';
        $page_descr = 'Check your access keys.';
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
        $css_include = ['tmx.css'];
        break;
    case 'gaia':
        $view = 'gaia';
        $page_title = 'Gaia Comparison';
        $page_descr = 'Check the Status of your GAIA strings across repositories.';
        break;
    case 'news':
        $controller = 'changelog';
        $page_title = 'Transvision News and Release Notes';
        $page_descr = '';
        $css_include = ['changelog.css'];
        break;
    case 'productization':
        $view = 'productization';
        $page_title = 'Productization Overview';
        $page_descr = 'Show productization aspects for this locale.';
        $css_include = ['productization.css'];
        break;
    case 'repocomparison':
        $view = 'repocomparison';
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
        $css_include = ['health.css'];
        break;
    case 'stats':
        $view = 'showrepos';
        $page_title = 'Status Overview';
        $page_descr = 'Repository status overview.';
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
        break;
    case 'unlocalized':
        $experimental = true;
        $controller   = 'unlocalized_words';
        $page_title   = 'Commonly Unlocalized Words';
        $page_descr   = 'Display the list of the most common untranslated words. Click on the table headers to sort results.';
        break;
    case 'unlocalized-json':
        $controller = 'unlocalized_words';
        $template   = false;
        break;
    case 'variables':
        $controller = 'check_variables';
        $page_title = 'Variables Overview';
        $page_descr = 'Show potential errors related to missing or mispelled variables in your strings.';
        break;
    default:
        $controller = 'mainsearch';
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
