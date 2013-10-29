<?php

$template     = true;
$page         = $urls[$url['path']];
$extra        = null;
$experimental = false;
$show_title   = true;

$title = '<a href="/" id="transvision-title">Transvision</a> ' . '<a href="/news/#v' . VERSION . '">' . VERSION . '</a>';

switch ($url['path']) {
    case '/':
        // Bootstrap l10n
        require_once INC . 'l10n-init.php';

        // Include Search Options
        require_once INC . 'search_options.php';

        // Import all strings for source and target locales + search process
        require_once INC . 'recherche.php';

        if (WEBSERVICE) {
            $view = 'webservice';
            $template = false;
            break;
        }
        $view  = 'search_form';
        $show_title = false;
        break;
    case 'news':
        $view  = 'changelog';
        $page_title = 'Transvision News. Version Notes';
        $page_descr = '';
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
        break;
    case 'downloads':
        $view  = 'downloads';
        $page_title = 'TMX Download';
        $page_descr = 'Download the <abbr title="Translation Memory eXchange">TMX</abbr> files used in Transvision.';
        break;
    case 'showrepos':
        $view  = 'showrepos';
        $experimental = true;
        $page_title = 'Status Overview';
        $page_descr = 'Repository status overview.';
        break;
    default:
        $view  = 'search';
        break;
}

$view =  $view . '.php';

if ($template) {
    ob_start();
    include VIEWS . $view;
    $content = ob_get_contents();
    ob_end_clean();
    // display the page
    require_once VIEWS .'template.php';
} else {
    include VIEWS . $view;
}
