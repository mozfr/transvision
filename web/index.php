<?php

// Variable allowing includes
$valid = true;

// Init application
require_once 'inc/init.php';

// Start output buffering, we will output in a template
ob_start();

if(valid($web_service)) {
    // fonction de recherche
    require_once'recherche.php';

    foreach ($keys as $key => $chaine) {
        $ken[$key][$chaine] = htmlspecialchars_decode($tmx_target[$key], ENT_QUOTES);
    }
    foreach ($keys2 as $key => $chaine) {
        $kfr[$key][$chaine] = htmlspecialchars_decode($tmx_source[$key], ENT_QUOTES);
    }

    $json_en = json_encode($ken);
    $json_fr = json_encode($kfr);


    header('Content-type: application/json; charset=UTF-8');

    if (isset($_GET['callback'])) {
        if ($_GET['return_loc'] == 'loc') {
            echo $_GET['callback'] . '(' . $json_fr . ');';
        } else{
            echo $_GET['callback'] . '(' . $json_en . ');';
        }
    } else {
        if (isset($_GET['return_loc']) && $_GET['return_loc'] == 'loc'){
            echo $json_fr;
        } else {
            echo htmlspecialchars_decode($json_en, ENT_QUOTES);
        }
    }
    // end of webservice code
    // XXX: factorize more code with normal display
    exit;
}

// Base html
require_once 'html_base.php';

// fonction de recherche
if ($check['t2t']) {
    require_once 't2t.php';
} else {
    require_once 'recherche.php';

    // result presentation
    if($recherche != '') {
        if ($check['ent']) {
            require_once 'results_ent.php';
        } else {
            require_once 'results.php';
        }
    }
}

$content = ob_get_contents();
ob_end_clean();

// display the page
require_once 'views/template.php';
