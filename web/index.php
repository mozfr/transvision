<?php

// Variable allowing includes
$valid = true;

// Init application
require_once 'init.php';

// include the search options.
require_once 'search_options.php';

// Start output buffering, we will output in a template
ob_start();

// Base html
require_once 'html_base.php';

// include the cache files.
require_once 'cache_import.php';

// fonction de recherche
if ($check['t2t']) {
    require_once 't2t.php';
} else {
    require_once 'recherche.php';

    // result presentation
    if($recherche != '') {
        if (!$check['ent']) {
            require_once 'results.php';
        } else {
            require_once 'results_ent.php';
        }
    }
}

$content = ob_get_contents();
ob_end_clean();

// display the page
require_once 'views/template.php';
