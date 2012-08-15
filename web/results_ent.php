<?php

if (!$valid) {
    die("File can't be called directly");
}

#The search results are displayed into a table (recherche2 is the original searched string before any modification)
echo "  <h2><span class=\"searchedTerm\">" . $recherche2 . "</span> is in:</h2>\n\n";
echo "  <table>\n\n";
echo "    <tr>\n";
echo "      <th>Entity</th>\n";
echo "      <th>en-US</th>\n";
echo "      <th>" . $locale . "</th>\n";
echo "    </tr>\n\n";

foreach ($entities as $val) {
    echo "    <tr>\n";
    echo "      <td>" . preg_replace("/(".$recherche.")/i", '<span style="color: rgb(221, 0, 0);">${1}</span>', htmlspecialchars($val)) . "</td>\n";
    echo "      <td>" . $tmx_source[$val] . "</td>\n";
    echo "      <td dir='$direction'>" . str_replace(' ', '<span style="' . $gray .'"> </span>', $tmx_target[$val]) . "</td>\n";
    echo "    </tr>\n\n";
}

echo "  </table>\n\n";
