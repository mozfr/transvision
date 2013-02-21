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

        // Import all strings for source and target locales
        require_once INC . 'cache_import.php';

        // Search process
        require_once INC . 'recherche.php';

        $view  = 'search_form';

        if (WEBSERVICE) {
            $view = 'webservice';
            $template = false;
        }
        break;
    case 'news':
        $view = 'changelog';
        break;
    case 'stats':
        $view = 'stats';
        break;
    case 'repocomparison':
        $view = 'repocomparison';
        break;
    case 'channelcomparison':
        $view  = 'channelcomparison';
        $extra = '<h2 class="alert">experimental View</h2>';
        break;
    case 'accesskeys':
        $view  = 'accesskeys';
        break;
    case 'credits':
        $view  = 'credits';
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
