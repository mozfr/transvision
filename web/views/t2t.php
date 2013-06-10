<?php
namespace Transvision;

// Search for the string
include INC . 'recherche.php';

// Get the locale results
$results = array();

foreach ($locale1_strings as $key => $chaine) {
    $results[$key] = $tmx_target[$key];
}

$perfect = $imperfect = array();

// we want to test compound words as well, /ex: 'switch to'
$compound_search = false;

if (count($search) > 1) {
    $aaa[] = implode(" ", $search);
    $aaa = array_reverse($search);
    $compound_search = true;
}

foreach ($search as $word) {
    // if the word is one or two letters, we skip it
    if (strlen($word) < 3) {
        continue;
    }

    // Perfect matches are hits for a single word or a compound word
    if ($compound_search || count($search) == 1) {
        $alternate1 = ucfirst($word);
        $alternate2 = ucwords($word);
        $alternate3 = strtolower($word);
        if (in_array($word, $tmx_source)
            || in_array($alternate1, $tmx_source)
            || in_array($alternate2, $tmx_source)
            || in_array($alternate3, $tmx_source)) {
            $perfect = array_merge($perfect, array_keys($tmx_source, $word));
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
    $imperfect = array_keys(array_filter(
            $tmx_source,
            function ($element) use ($word) {
                $bingo = Strings::inString($element, $word);
                if (!$bingo) {
                    $bingo = Strings::inString(strtolower($element), strtolower($word));
                }
                return $bingo;
                })
            );
}


$imperfect = array_unique($imperfect); // remove duplicates

$perfect_results = $imperfect_results = array();

foreach ($perfect as $val) {
    if ($tmx_target[$val] != '') {
        $perfect_results[] = $tmx_target[$val];
    }
}

foreach ($imperfect as $key => $val) {
    if ($tmx_target[$val] != '') {
        $imperfect_results[$val] = $tmx_target[$val];
    }
}

$imperfect_results = array_unique($imperfect_results);
$perfect_results   = array_unique($perfect_results);

if (count($perfect_results) > 0) {
    echo '<b>Perfect matches</b>';
    echo "<ol dir='$localeDir'>";
    foreach ($perfect_results as $val) {
        echo '<li>' . strip_tags(htmlspecialchars_decode($val)) . '</li>';
    }
    echo "</ol>";
} else {
    echo "<p>No perfect match found.</p>";
}

echo '<b>Used in</b>';
echo "<table>";
foreach ($imperfect_results as $key => $val) {
    echo '<tr>';
    echo "<td dir='$localeDir'>" . strip_tags(htmlspecialchars_decode($val)) . '</td>';
    echo "<td dir='$sourceLocaleDir'>" . strip_tags(htmlspecialchars_decode($tmx_source[$key])) . '</td>';
    echo '</tr>';
}
echo "</table>";
