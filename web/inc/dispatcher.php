<?php

$template = true;
$page     = $urls[$url['path']];
$extra    = null;

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
        }
        $title = '<a href="/" id="transvision-title">Transvision</a> '
                 . '<a href="/news/#v' . VERSION . '">' . VERSION . '</a>';
        $view  = 'search_form';
        break;
    case 'news':
        $title = '<a href="/">Transvision</a> changelog';
        $view  = 'changelog';
        break;
    case 'stats':
        $view  = 'stats';
        $title = '<a href="/">Transvision</a> short usage stats';
        break;
    case 'repocomparison':
        $title = '<a href="/" id="transvision-title">Transvision</a> '
                 . '<a href="/news/#v' . VERSION . '">' . VERSION . '</a>';
        $view = 'repocomparison';
        break;
    case 'channelcomparison':
        $title = '<a href="/" id="transvision-title">Transvision</a> '
                 . '<a href="/news/#v' . VERSION . '">' . VERSION . '</a>';
        $view  = 'channelcomparison';
        $extra = '<h2 class="alert">experimental View</h2>';
        break;
    case 'accesskeys':
        $title = '<a href="/" id="transvision-title">Transvision</a> '
                 . '<a href="/news/#v' . VERSION . '">' . VERSION . '</a>';
        $view  = 'accesskeys';
        break;
    case 'credits':
        $title = '<a href="/">Transvision</a> credits';
        $view  = 'credits';
        break;
    case 'downloads':
        $title = '<a href="/">Transvision</a> TMX Downloads';
        $view  = 'downloads';
        break;
    case 'showrepos':
        $title = '<a href="/" id="transvision-title">Transvision</a> '
                 . 'Repository global status</a>';
        $view  = 'showrepos';
        $extra = '<h2 class="alert">experimental View</h2>';
        break;
    default:
        $title = '<a href="/" id="transvision-title">Transvision</a> '
                 . '<a href="/news/#v' . VERSION . '">' . VERSION . '</a>';
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
