<?php

#include the search options.
include 'search_option.php';

#Include the necessary header
require_once 'header.php';


#Base html
include 'html_base.php';

#include the locale name finder
#include 'locale_find.php';

#include the cache files.
include'cache_import.php';

#fonction de recherche
if ($check9 == 'checked') {
    include 't2t.php';
} else {
    include 'recherche.php';

    #result presentation
    if ($check4 == 'unchecked') {
        include 'results.php';
    } else {
        include 'results_ent.php';
    }
}

#include the footer of the page
require_once 'footer.html';
