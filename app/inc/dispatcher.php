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
    case 'news':
        $view = 'changelog';
        $page_title = 'Transvision News. Version Notes';
        $page_descr = '';
        break;
    case 'stats':
        $view = 'stats';
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
        $view = 'channelcomparison';
        $experimental = true;
        $page_title = 'Channel Comparison';
        $page_descr = 'Compare strings from channel to channel.';
        break;
    case 'accesskeys':
        $view = 'accesskeys';
        $page_title = 'Access Keys';
        $page_descr = 'Check your access keys.';
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
        break;
    case 'showrepos':
        $view = 'showrepos';
        $experimental = true;
        $page_title = 'Status Overview';
        $page_descr = 'Repository status overview.';
        break;
    case 'string':
        $controller = 'onestring';
        $page_title = 'All translations for this string:';
        $page_descr = '';
        break;
    case 'variables':
        $view = 'checkvariables';
        $experimental = true;
        $page_title = 'Variables Overview';
        $page_descr = 'Show potential errors in your strings for the use of variables.';
        break;
    case 'productization':
        $view = 'productization';
        $experimental = true;
        $page_title = 'Productization Overview';
        $page_descr = 'Show productization aspects for this locale.';
        break;
    case Strings::StartsWith($url['path'], 'api'):
        $controller = 'api';
        $page_title = 'API response';
        $page_descr = '';
        $template = false;
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

    // display the page
    require_once VIEWS .'template.php';
} else {
    if (isset($view)) {
        include VIEWS . $view . '.php';
    } else {
        include CONTROLLERS . $controller . '.php';
    }
}
