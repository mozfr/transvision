<?php
header('Content-type: text/html; charset=UTF-8');

#include the search options.
include 'search_option.php';

#include the locale name finder
#include 'locale_find.php';

#include the cache files.
include 'cache_import.php';



#fonction de recherche
include'recherche.php';


foreach ($keys as $key => $chaine) {
    $ken[$key][$chaine] = $l_fr[$key];
}
foreach ($keys2 as $key = >$chaine) {
    $kfr[$key][$chaine] = $l_en[$key];
}



$json_en = json_encode($ken);
$json_fr = json_encode($kfr);

#echo json_encode($keys2);


if (isset($_GET['callback'])) {
    if ($_GET['return_loc'] == 'loc'){
    echo $_GET['callback'] . '(' . $json_fr . ');';}
    else{
    echo $_GET['callback'] . '(' . $json_en . ');';}
} else {
    if ($_GET['return_loc'] == 'loc'){
        echo $json_fr;
    } else {
        echo $json_en;
    }
}
//echo $callback . '(' . $json_en . ')';
