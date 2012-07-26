<?php

include '../PAGES/function_clean.php';

# Find the current directory and the locale short name
$locale = strip_tags(preg_replace("/\/glossaire\/(.*)\/entite.php/", "$1", $_SERVER['REQUEST_URI']));
#$locale1=strip_tags(preg_replace("/\/*index.php.*/","",$_SERVER['REQUEST_URI']));
#$locale2=strip_tags(preg_replace("/\/glossaire\/(.*)/","$1",$locale1));
#$locale=strip_tags(preg_replace("/(.*)\//","$1",$locale2));

## Deduce the memoire.tmx directory name
$tmxfile = "/home/pascalc/transvision/TMX/memoire_en-US-" . $locale . ".tmx";

clearstatcache();

include "/home/pascalc/transvision/TMX/cache/" . $locale . "/cache_" . $locale . ".php"; // localised
$tmx_fr = $tmx;

include "/home/pascalc/transvision/TMX/cache/" . $locale . "/cache_en-US.php"; // english
$tmx_en = $tmx;

// get language arrays
$l_en = $tmx_en;
$l_fr = $tmx_fr;


echo "<"."?"."xml version=\"1.0\" encoding=\"UTF-8\""."?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\" dir=\"ltr\">\n\n";
echo "<head>\n";
echo "  <title>Entity search</title>\n";
echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
echo "  <link rel=\"stylesheet\" href=\"../styles/glossary.css\" type=\"text/css\" media=\"all\" />\n";
echo "</head>\n\n";
echo "<body>\n\n";
echo "  <h1>Search for entities</h1>\n\n";
echo "<script type=\"text/javascript\" src=\"../PAGES/wz_tooltip.js\"></script>\n";


/*echo "<h2>English :</h2>\n";
// list keys and values
echo "<ul>\n";
while (list($key, $val) = each($l_en)) {
    echo "<li>\$tmx-&gt;resource['".$key."'] = ".$val."</li>\n";
}
echo "</ul>\n";

echo "<h2>Français :</h2>\n";
echo "<h2>French:</h2>\n";
// list keys and values
echo "<ul>";
while (list($key, $val) = each($l_fr)) {
    echo "<li>\$tmx-&gt;resource['".$key."'] = ".$val."</li>\n";
}
echo "</ul>\n";
*/

$recherche2 = $recherche;

//$recherche=$_GET['recherche'];
if (isset($_GET['recherche'])) {
    $recherche = stripslashes(secureText($_GET['recherche']));
}

$recherche2 = $recherche;
$recherche  = preg_quote($recherche);
$recherche  = sql_regcase("*" . $recherche . "*");

$keys  = preg_grep(sql_regcase("/\&.*;/"), $l_en);
$keys2 = preg_grep(sql_regcase("/\&.*;/"), $l_fr);


//$keys = preg_grep($recherche, l_en);
//$keys2 = preg_grep($recherche, $l_fr);
echo "  <h2>"."English entity list</h2>\n\n";
echo "  <table>\n\n";
echo "    <tr>\n";
echo "      <th>Entity</th>\n";
echo "      <th>en-US</th>\n";
echo "      <th>".$locale."</th>\n";
echo "    </tr>\n\n";

foreach ($keys as $key=>$chaine) {
    $aaa=strip_tags(preg_replace("/.*(\&.*;).*/","$1",$chaine));
    $bbb=strip_tags(preg_replace("/.*(\&.*;).*/","$1",$l_fr[$key]));
    #echo "      <td>".strip_tags(preg_replace("/.*(\&.*;).*/","$1",$chaine))."</td>\n";
    #echo "      <td>".strip_tags(preg_replace("/.*(\&.*;).*/","$1",$l_fr[$key]))."</td>\n";
    #echo "    </tr>\n\n";
    if ($aaa == $bbb) {
        #echo "      <td>".$key."</td>\n";
        #echo "      <td><a  href=\"javascript:void(0);\" onmouseover=\"Tip('.".$chaine."', CLICKSTICKY, true);\" onmouseout=\"UnTip()\">".htmlspecialchars($aaa)."</a></td>\n";
        #echo "      <td><a  href=\"javascript:void(0);\" onmouseover=\"Tip('.".$l_fr[$key]."', CLICKSTICKY, true);\" onmouseout=\"UnTip()\">".htmlspecialchars($bbb)."</a></td>\n";
        #echo "    </tr>\n\n";
    } else {
        echo "    <tr>\n";
        echo "      <td>".$key."</td>\n";
        echo "      <td><a href=\"javascript:void(0);\" onmouseover=\"Tip('.".htmlspecialchars($chaine)."', CLICKSTICKY, true);\" onmouseout=\"UnTip()\" class=\"entities\">".htmlspecialchars($aaa)."</a></td>\n";
        echo "      <td><a href=\"javascript:void(0);\" onmouseover=\"Tip('.".htmlspecialchars($l_fr[$key])."', CLICKSTICKY, true);\" onmouseout=\"UnTip()\" class=\"entities\">".htmlspecialchars($bbb)."</a></td>\n";
        echo "    </tr>\n\n";
    }
}

echo "  </table>\n\n";
echo "  <h2>".$locale." entity list</h2>\n\n";
echo "  <table>\n\n";
echo "    <tr>\n";
echo "      <th>Entity</th>\n";
echo "      <th>en-US</th>\n";
echo "      <th>".$locale."</th>\n";
echo "    </tr>\n\n";

foreach ($keys2 as $key => $chaine) {
    $bbb = strip_tags(preg_replace("/.*(\&.*;).*/", "$1", $chaine));
    $aaa = strip_tags(preg_replace("/.*(\&.*;).*/", "$1", $l_en[$key]));
    #echo "      <td>".strip_tags(preg_replace("/.*(\&.*;).*/","$1",$l_en[$key]))."</td>\n";
    #echo "      <td>".strip_tags(preg_replace("/.*(\&.*;).*/","$1",$chaine))."</td>\n";
    #echo "    </tr>\n\n";

    if ($aaa == $bbb) {
    #echo "      <td>".$key."</td>\n";
    #echo "      <td><a  href=\"javascript:void(0);\" onmouseover=\"Tip('.".$l_en[$key]."', CLICKSTICKY, true);\" onmouseout=\"UnTip()\">".htmlspecialchars($aaa)."</a></td>\n";
    #echo "      <td><a  href=\"javascript:void(0);\" onmouseover=\"Tip('.".$chaine."', CLICKSTICKY, true);\" onmouseout=\"UnTip()\">".htmlspecialchars($bbb)."</a></td>\n";
    #echo "    </tr>\n\n";
    } else {
        echo "    <tr>\n";
        echo "      <td>".$key."</td>\n";
        echo "      <td><a href=\"javascript:void(0);\" onmouseover=\"Tip('.".htmlspecialchars($l_en[$key])."', CLICKSTICKY, true);\" onmouseout=\"UnTip()\" class=\"entities\">".htmlspecialchars($aaa)."</a></td>\n";
        echo "      <td><a href=\"javascript:void(0);\" onmouseover=\"Tip('.".htmlspecialchars($chaine)."', CLICKSTICKY, true);\" onmouseout=\"UnTip()\" class=\"entities\">".htmlspecialchars($bbb)."</a></td>\n";
        echo "    </tr>\n\n";
    }
}
echo "  </table>\n\n";
//print_r($keys);
//echo  $l_en($key) . "<br />";
echo "  <div id=\"links\">\n";
echo "    <ul>\n";
echo "      <li><a href=\"index.php\" title=\"Search in the Glossary\">Glossary</a></li>\n";
echo "      <li><a href=\"alignement.php\" title=\"Search for similarities\">Alignment</a></li>\n";
echo "      <li><a href=\"doublons.php\" title=\"Search for Duplicates\">Duplicates</a></li>\n";
echo "      <li><a href=\"entite.php\" title=\"Search for Entities\">Entities</a></li>\n";
echo "      <li><a href=\"http://www.frenchmozilla.fr\" title=\"Home of Frenchmozilla\"
hreflang=\"fr\">Frenchmozilla</a></li>\n";
echo "    </ul>\n";
echo "  </div>\n\n";

echo "</body></html>";
