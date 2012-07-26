<?php

include '../PAGES/locale_find.php';
#Include the necessary header
require_once '../include/header.php';

include '../PAGES/d_html_base.php';

if ($unique==1) {
    include '/home/pascalc/transvision/TMX/cache/' . $locale . '/doublons_unique_' . $locale . '.php';
} else {
    include '/home/pascalc/transvision/TMX/cache/' . $locale . '/doublons_' . $locale . '.php';
}

$i = 1;
while ($i <= $k) {
   list($rep1, $fichier1, $ent1) = split(':', $entity1[$i]);
   list($rep2, $fichier2, $ent2) = split(':', $entity2[$i]);
   if (($primaire == 'tout' && $secondaire == 'tout')
        or ($primaire == 'tout' && $rep2 == $secondaire)
        or ($secondaire == 'tout' && $rep1 == $primaire)
        or ($rep1 == $primaire && $rep2 == $secondaire)
        or ($rep2 == $primaire && $rep1 == $secondaire)) {
        echo "    <tr>\n";
        echo "      <td>" . $entity1[$i] . "</td>\n";
        echo "      <td>" . $enus[$i] . "</td>\n";
        echo "      <td>" . $fr1[$i] . "</td>\n";
        echo "      <td>" . $fr2[$i] . "</td>\n";
        echo "      <td>" . $entity2[$i] . "</td>\n";
        echo "    </tr>\n\n";
   }
   $i++;
}

echo "</table>\n\n";

require_once '../include/footer.html';
