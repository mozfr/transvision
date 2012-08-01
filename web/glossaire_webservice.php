<?php

// Variable allowing includes
$valid = true;

// Init application
require_once 'init.php';

header('Content-type: application/json; charset=UTF-8');

// include the search options.
require_once 'search_options.php';

// include the locale name finder
// include 'locale_find.php';

// include the cache files.
require_once 'cache_import.php';

// fonction de recherche
require_once'recherche.php';


foreach ($keys as $key => $chaine) {
    $ken[$key][$chaine] = $l_fr[$key];
}
foreach ($keys2 as $key => $chaine) {
    $kfr[$key][$chaine] = $l_en[$key];
}

echo json_encode($ken);
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo json_encode($kfr);

#echo json_encode($keys2);
