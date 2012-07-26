<?php
header('Content-type: text/html; charset=UTF-8');

#include the search options.
include 'search_option.php';

#include the locale name finder
#include('locale_find.php');

#include the cache files.
include 'cache_import.php';



#fonction de recherche
include 'recherche.php';


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
