<?php
// aucune distance de trouvée pour le moment
$shortest = -1;

foreach($l_en as $word) {

    // calcule la distance avec le mot mis en entrée,
    // et le mot courant
    #$lev = levenshtein($recherche2, $word);
    $lev = similar_text($recherche, $word);

    // cherche une correspondance exacte
    if ($lev == 100) {
        // le mot le plus près est celui-ci (correspondance exacte)
        $closest  = $word;
        $shortest = 100;
        // on sort de la boucle ; nous avons trouvé une correspondance exacte
        break;
    }

    // Si la distance est plus petite que la prochaine distance trouvée
    // OU, si le prochain mot le plus près n'a pas encore été trouvé
    if ($lev > $shortest ) {
        // définission du mot le plus près ainsi que la distance
        $closest  = array();
        $closest  = array($word);
        $shortest = $lev;
    }

    if ($lev == $shortest ) {
        // définission du mot le plus près ainsi que la distance
        array_push($closest,$word);
    }
}

if ($shortest > 40) {
    foreach ($closest as $result_en) {
        $result_ent = array_search($result_en, $l_en);
        $keys[$result_ent] = $result_en;
    }
} else {
    $keys = array();
}

//In the desired locale
$shortest = -1;

foreach ($l_fr as $word){
    // calcule la distance avec le mot mis en entrée,
    // et le mot courant
    #$lev = levenshtein($recherche2, $word);
    $lev = similar_text($recherche, $word);

        // cherche une correspondance exacte
    if ($lev == 100) {
        // le mot le plus près est celui-ci (correspondance exacte)
        $closest  = $word;
        $shortest = 100;
        // on sort de la boucle ; nous avons trouvé une correspondance exacte
        break;
    }

    // Si la distance est plus petite que la prochaine distance trouvée
    // OU, si le prochain mot le plus près n'a pas encore été trouvé

    if ($lev > $shortest ) {
           // définission du mot le plus près ainsi que la distance
        $closest = array();
        $closest  = array($word);
        $shortest = $lev;
    }

    if ($lev == $shortest ) {
        // définission du mot le plus près ainsi que la distance
        array_push($closest, $word);
    }
}
if ($shortest > 10) {
    foreach ($closest as $result_fr) {
        $result_ent = array_search($result_fr, $l_fr);
        $keys2[$result_ent] = $result_fr;
    }
} else {
    $keys2 = array();
}
