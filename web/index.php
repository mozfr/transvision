<?php

// Variable allowing includes
$valid = true;

// Init application
require_once 'init.php';

// include the search options.
require_once 'search_options.php';

// Include the necessary header
require_once 'header.php';

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
    if (!$check['ent']) {
        require_once 'results.php';
    } else {
        require_once 'results_ent.php';
    }
}

// include the footer of the page
require_once 'footer.html';
