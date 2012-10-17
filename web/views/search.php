<?php

// Page title
$title = 'Transvision glossary <a href="./news/#v' . VERSION . '">' . VERSION . '</a>';

// Base html
//~ if ($url['path'] == 'stats') {
    //~ // Include Search Options
    //~ require_once INC . 'search_options.php';
    //~ // Import all strings for source and target locales
    //~ require_once INC . 'cache_import.php';
    //~ require_once VIEWS . 'stats.php';
//~ }

// Search results process
if ($check['t2t']) {
    require_once VIEWS . 't2t.php';
} else {
    require_once INC . 'recherche.php';

    // result presentation
    if ($recherche != '') {
        require_once WEBROOT .'classes/ShowResults.class.php';
        if ($check['ent']) {
            require_once VIEWS . 'results_ent.php';
        } else {
            require_once VIEWS . 'results.php';
        }
    }
}
