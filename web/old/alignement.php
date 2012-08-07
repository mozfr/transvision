<?php
if (!$valid) {
    die("File can't be called directly");
}

include 'function_clean.php';

# Include the locale name finder
include 'locale_find.php';

# Include the cache files.
include 'cache_import.php';

require_once 'header.php';

echo "  <h1>String alignement</h1>\n\n";

echo "    <form method=\"get\" action=\"alignement.php\" >\n";
echo "      <p>\n";
echo "        <input type=\"text\" name=\"recherche\" size=\"30\" />\n";
echo "        <input type=\"submit\" value=\"Search&hellip;\" alt=\"Search&hellip;\" />\n";
echo "      </p>\n";
echo "    </form>\n\n";

$recherche2=$recherche;
//$recherche=$_GET['recherche'];
// recherche is the string to find
if (isset($_GET['recherche'])) {
    $recherche = stripslashes(secureText($_GET['recherche']));
}

$recherche2 = $recherche;
$recherche3 = $recherche;
$recherche  = preg_quote($recherche);
//$recherche=sql_regcase("*".$recherche."*");

if (! isset($_GET['recherche'])) {
    $recherche  = 'Minefield';
    $recherche2 = $recherche;
}


// Exact Match
$rech   = "/^[[:space:]]*" . sql_regcase($recherche) . "[[:space:]]*$/";
$exact  = preg_grep($rech, $l_en);
$rech2  = "*" . sql_regcase($recherche) . "*";
$exact2 = preg_grep($rech2, $l_en);
$exact2 = preg_grep($rech, $exact2, PREG_GREP_INVERT);

$aaa = explode(' ', $recherche);
$all = preg_grep(sql_regcase('*' . $aaa[0] . '*'), $l_en);
foreach($aaa as $aa) {
    $all = preg_grep(sql_regcase("*" . $aa . "*"), $all);
}

$all  = preg_grep($rech, $all, PREG_GREP_INVERT);
$all  = preg_grep($rech2, $all, PREG_GREP_INVERT);
$keys = $exact;

if ($keys != null) {
    echo "    <h2><span class=\"searchedTerm\">$recherche</span> is a perfect match in:</h2>\n\n";
    echo "    <table>\n\n";
    echo "      <tr>\n";
    echo "        <th>Entity</th>\n";
    echo "        <th>en-US</th>\n";
    echo "        <th>$locale</th>\n";
    echo "      </tr>\n\n";
    foreach ($keys as $key => $chaine) {
        echo "      <tr>\n";
        echo "        <td>$key</td>\n";
        echo "        <td>" . strip_tags($chaine) . "</td>\n";
        echo "        <td>" . strip_tags($l_fr[$key]) . "</td>\n";
        echo "      </tr>\n\n";
    }
    echo "    </table>\n\n";
}

if ($keys != null){
    $keys = $exact2;

    echo "    <h2><span class=\"searchedTerm\">$recherche</span> is almost a perfect match in
    :</h2>\n\n";
    echo "    <table>\n\n";
    echo "      <tr>\n";
    echo "        <th>Entity</th>\n";
    echo "        <th>en-US</th>\n";
    echo "        <th>$locale</th>\n";
    echo "      </tr>\n\n";

    foreach($keys as $key => $chaine) {
        echo "      <tr>\n";
        echo "        <td>$key</td>\n";
        echo "        <td>" . strip_tags($chaine) . "</td>\n";
        echo "        <td>" . strip_tags($l_fr[$key]) . "</td>\n";
        echo "      </tr>\n\n";
    }

    echo "    </table>\n\n";
}



$keys2 = $all;

if ($keys2 != null) {
    echo "<h2>The words of <span class=\"searchedTerm\">$recherche</span> are in:</h2>\n\n";
    echo "    <table>\n\n";
    echo "      <tr>\n";
    echo "        <th>Entity</th>\n";
    echo "        <th>en-US</th>\n";
    echo "        <th>$locale</th>\n";
    echo "      </tr>\n\n";
    foreach ($keys2 as $key => $chaine) {
        echo "      <tr>\n";
        echo "        <td>".$key."</td>\n";
        echo "        <td>" . strip_tags($l_en[$key]) . "</td>\n";
        echo "        <td>" . strip_tags($l_fr[$key]) . "</td>\n";
        echo "      </tr>\n\n";
    }
    echo "    </table>\n\n";
}

// aucune distance de trouvée pour le moment
$shortest = -1;

foreach($l_en as $word){
    // calcule la distance avec le mot mis en entrée,
    // et le mot courant
    #$lev = levenshtein($recherche2, $word);
    $lev = similar_text($recherche2, $word);

    // cherche une correspondance exacte
    if ($lev == 100) {

        // le mot le plus près est celui-ci (correspondance exacte)
        $closest = $word;
        $shortest = 100;

        // on sort de la boucle ; nous avons trouvé une correspondance exacte
        break;
    }

    // Si la distance est plus petite que la prochaine distance trouvée
   // OU, si le prochain mot le plus près n'a pas encore été trouvé
    if ($lev > $shortest) {
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
    echo "    <h2>Similar Text</h2>\n\n";
    echo "      <p>Entered word: <span class=\"searchedTerm\">$recherche2</span>\n";
    echo $shortest;

    if ($shortest == 100) {
        echo "      <p>Exact match found: $closest</p>\n\n";
    } else {

    /*      foreach($closest as $result){
        echo "Vous voulez dire : $result ?\n<p>";
        echo $shortest."<p>";
        }
    */
    echo "      <table>\n\n";
    echo "        <tr>\n";
    echo "          <th>Entity</th>\n";
    echo "          <th>en-US</th>\n";
    echo "          <th>$locale</th>\n";
    echo "        </tr>\n\n";

    foreach($closest as $result) {
        echo "        <tr>\n";
        echo "          <td>" . array_search($result, $l_en) . "</td>\n";
        echo "          <td>" . strip_tags($result) . "</td>\n";
        echo "          <td>" . strip_tags($l_fr[array_search($result, $l_en)]) . "</td>\n";
        echo "        </tr>\n\n";
    }

    echo "      </table>\n\n";

    }
}


$input    = $recherche2;
$shortest = 1000;
$closest  = array();
foreach($l_en as $word) {

    // calcule la distance avec le mot mis en entrée,
    // et le mot courant
    // $lev = levenshtein($input, $word);
    if ((strlen($word) < 250) && strlen($input < 250)) {
        $lev = levenshtein($input, $word);

        // cherche une correspondance exacte
        if ($lev == 0) {
            // le mot le plus près est celui-ci (correspondance exacte)
            $closest  = array($word);
            $shortest = 0;
            // on sort de la boucle ; nous avons trouvé une correspondance exacte
            break;
        }

        // Si la distance est plus petite que la prochaine distance trouvée
        // OU, si le prochain mot le plus près n'a pas encore été trouvé
        if ($lev <= $shortest && $lev > -1) {
            // définission du mot le plus près ainsi que la distance
            $closest = array();
            $closest  = array($word);
            $shortest = $lev;
        }
        if ($lev == $shortest && $lev != -1) {
            // définition du mot le plus près ainsi que la distance
            array_push($closest,$word);
            // echo strip_tags($word).$lev."<p>";
        }
    }
}

if ($shortest<30) {
    echo "    <h2> Levenstein</h2>\n\n";
    echo "    <p>Word entered: <span class=\"searchedTerm\">$input</span></p>\n\n";
    echo "    <p>" . $shortest . "</p>\n";

    if ($shortest == 0) {
        foreach($closest as $result) {
            echo "<p>Exact match found: " . $result . "</p>\n";
            echo "<p>" . $shortest . "</p>\n";
        }
    } else {
        echo "      <table>\n\n";
        echo "        <tr>\n";
        echo "          <th>Entity</th>\n";
        echo "          <th>en-US</th>\n";
        echo "          <th>" . $locale . "</th>\n";
        echo "        </tr>\n\n";

        foreach ($closest as $result) {
            echo "        <tr>\n";
            echo "          <td>" . array_search($result, $l_en) . "</td>\n";
            echo "          <td>" . strip_tags($result) . "</td>\n";
            echo "          <td>" . strip_tags($l_fr[array_search($result, $l_en)]) . "</td>\n";
            echo "        </tr>\n\n";
        }

        echo "      </table>\n\n";
    }
}




// include the footer of the page
require_once '../include/footer.html';

// XXX fonction non utilisée
function levenshtein2($str1, $str2) {
    $strlen1   = strlen($str1);
    $strlen2   = strlen($str2);
    $max       = max($strlen1, $strlen2);
    $splitSize = 250;

    if($max>$splitSize) {
        $lev=0;
        for($cont=0; $cont < $max; $cont += $splitSize) {
            if($strlen1 <= $cont || $strlen2 <= $cont) {
              $lev = $lev/($max/min($strlen1, $strlen2));
              break;
            }

            $lev += levenshtein(substr($str1,$cont,$splitSize), substr($str2, $cont, $splitSize));
        }
    } else {
        $lev=levenshtein($str1, $str2);
        $porcentage= -100*$lev/$max+100;

        if($porcentage>75) { //Ajustar con similar_text
            similar_text($str1, $str2, $porcentage);
        }
    }
   #return $porcentage;
    return $lev;
}
