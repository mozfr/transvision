<?php

$rouge = '<span style="color: rgb( 200,0, 0);">';
$bleu  = '<span style="color: rgb( 0, 0,200);">';
$vert  = '<span style="color: rgb( 0, 0,0);">';

if ($base == 'trunk') {
    $base2 = 'central';
} else {
    $base2 = $base;
}

if ($base == 'trunk') {
    $base3 = 'central';
} else {
    $base3 = 'mozilla-' . $base;
}


#The search results are displayed into a table (recherche2 is the original searched string before any modification)
echo "  <h2><span class=\"searchedTerm\">" . $recherche2 . "</span> is in English in:</h2>\n\n";
echo "  <table>\n\n";
echo "    <tr>\n";
echo "      <th>Entity</th>\n";
echo "      <th>en-US</th>\n";
echo "      <th>" . $locale . "</th>\n";
echo "    </tr>\n\n";

foreach ($keys as $key => $chaine) {
    $key_color = preg_replace("/(.+:)(.+:)(.+)/",'<span style="color: rgb(221, 0, 0);">${1}</span>' . $bleu . '\2</span>' . $vert . '${3}</span>', $key);
    $key_link = preg_replace("/(.+:)(.+:)(.+)/",'<a href="http://mxr.mozilla.org/comm-release/search?find=\1.*${2}&string=${3}&tree=comm-', $key);
    $key_link = str_replace(":.", ".", $key_link);
    $key_link = str_replace(":&", "&", $key_link);
    echo "    <tr>\n";
    echo "      <td>" . $key_link . $base2 . '">' . $key_color . "</a></td>\n";
    echo "      <td>".preg_replace("/(" . $recherche . ")/i", '<span style="color: rgb(221, 0, 0);">${1}</span>', strip_tags($chaine)) . "</td>\n";
    echo "      <td>" . strip_tags($l_fr[$key]) . "</td>\n";
    echo "    </tr>\n\n";
}

echo "  </table>\n\n";
echo "  <h2><span class=\"searchedTerm\">" . $recherche2 . "</span> is in " . $locale . " in:</h2>\n\n";
echo "  <table>\n\n";
echo "    <tr>\n";
echo "      <th>Entity</th>\n";
echo "      <th>en-US</th>\n";
echo "      <th>" . $locale . "</th>\n";
echo "    </tr>\n\n";

foreach ($keys2 as $key => $chaine) {
    $key_color = preg_replace("/(.+:)(.+:)(.+)/", '<span style="color: rgb(221, 0, 0);">${1}</span>' . $bleu . '\2</span>' . $vert . '${3}</span>', $key);
    $key_link = preg_replace("/(.+:)(.+:)(.+)/",'<a href="http://mxr.mozilla.org/l10n-' . $base3 . '/search?find=/' . $locale . '/.*${2}&string=${3}', $key);
    $key_link = str_replace(':.', '.', $key_link);
    $key_link = str_replace(':&', '&', $key_link);
    echo "    <tr>\n";
    echo "      <td>" . $key_link . '">' . $key_color . "</a></td>\n";
    echo "      <td>" . strip_tags($l_en[$key]) . "</td>\n";
    echo "      <td>" . preg_replace("/(".$recherche.")/i", $rouge . '${1}</span>', strip_tags($chaine)) . "</td>\n";
    echo "    </tr>\n\n";
}
echo "  </table>\n\n";
