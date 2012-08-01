<?php

if (!$valid) {
    die("File can't be called directly");
}

#The search results are displayed into a table (recherche2 is the original searched string before any modification)
echo "  <h2><span class=\"searchedTerm\">" . $recherche2 . "</span> is in:</h2>\n\n";
echo "  <table>\n\n";
echo "    <tr>\n";
echo "      <th>Entity FOO</th>\n";
echo "      <th>en-US</th>\n";
echo "      <th>" . $locale . "</th>\n";
echo "    </tr>\n\n";


foreach ($keys as $key => $chaine) {
    echo "    <tr>\n";
    echo "      <td>" . preg_replace("/(".$recherche.")/i", '<span style="color: rgb(221, 0, 0);">${1}</span>', strip_tags($chaine)) . "</td>\n";
    // echo "      <td>".strip_tags($chaine)."</td>\n";
    echo "      <td>" . strip_tags($key) . "</td>\n";
    echo "      <td dir='$direction'>" . strip_tags($l_fr[$chaine]) . "</td>\n";
    echo "    </tr>\n\n";
}

echo "  </table>\n\n";
