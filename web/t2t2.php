<?php

if (!$valid) die;

// Search for the string
include 'recherche.php';

// Get the locale results
$results = array();

foreach ($keys as $key => $chaine) {
    $results[$key] = $tmx_target[$key];
}

$perfect = $imperfect = array();

// we want to test compound words as well, /ex: 'switch to'
if(count($aaa) > 1) {
    $aaa[] = implode(" ", $aaa);
    $aaa = array_reverse($aaa);
    $compound_search = true;
}

foreach($aaa as $search) {
    // if the word is one or two letters, we skip it
    if(strlen($search) < 3) continue;

    // Perfect matches are hits for a single word or a compound word
    if($compound_search || count($aaa) == 1) {
        $alternate1 = ucfirst($search);
        $alternate2 = ucwords($search);
        $alternate3 = strtolower($search);
        if (in_array($search, $tmx_source)
            || in_array($alternate1, $tmx_source)
            || in_array($alternate2, $tmx_source)
            || in_array($alternate3, $tmx_source)) {
            $perfect = array_merge($perfect, array_keys($tmx_source, $search));
            $perfect = array_merge($perfect, array_keys($tmx_source, $alternate1));
            $perfect = array_merge($perfect, array_keys($tmx_source, $alternate2));
            $perfect = array_merge($perfect, array_keys($tmx_source, $alternate3));
            $perfect = array_unique($perfect); // remove duplicates
        }
        $compound_search = false;
    }

    /*
     * We use a closure here to extract imperfect matches without having to
     * use a loop to search all strings
     */
    $imperfect = array_keys(array_filter($tmx_source,
                    function ($element) use ($search) {
                        $bingo = (strpos($element, $search)) ? true : false;
                        if(!$bingo) {
                            $bingo = (strpos($element, strtolower($search))) ? true : false;
                        }
                        return $bingo;
                        })
                );
}


$imperfect = array_unique($imperfect); // remove duplicates

$perfect_results = $imperfect_results = array();

foreach ($perfect as $val){
    if($tmx_target[$val] != '') {
        $perfect_results[] = $tmx_target[$val];
    }
}

foreach ($imperfect as $key => $val){
    if($tmx_target[$val] != '') {
        $imperfect_results[$val] = $tmx_target[$val];
    }
}

$imperfect_results = array_unique($imperfect_results);
$perfect_results = array_unique($perfect_results);

if (count($perfect_results) > 0) {
    echo '<b>Perfect matches</b>';
    echo "<ol dir='$direction'>";
    foreach($perfect_results as $val) {
        echo '<li>' . strip_tags(htmlspecialchars_decode($val)) . '</li>';
    }
    echo "</ol>";
} else {
    echo "<p>No perfect match found.</p>";
}

echo '<b>Used in</b>';
echo "<table>";
foreach ($imperfect_results as $key => $val){
    echo '<tr>';
    echo "<td dir='$direction'>" . strip_tags(htmlspecialchars_decode($val)) . '</td>';
    echo '<td>' . strip_tags(htmlspecialchars_decode($tmx_source[$key])) . '</td>';
    echo '</tr>';
}
echo "</table>";



/*
 * the code below is commented out, this is some investigation work for
 * getting potential candidates from strings in which we have an imperfect match
 *
// we only want imperfect strings that don't use strings we already defined as perfect
foreach ($imperfect_results as $key => $val) {
    $str = strip_tags(htmlspecialchars_decode($val));
    foreach ($perfect_results as $hit) {
        $hit = strip_tags(htmlspecialchars_decode($hit));
        if(stristr($str, $hit)) {
            unset($imperfect_results[$key]);
        }
    }
}


echo '<b>Used in</b>';
echo "<ol dir='$direction'>";
foreach ($imperfect_results as $key => $val){
    echo '<li>' . strip_tags(htmlspecialchars_decode($val)) . '</li>';
}
echo "</ol>";

*/
