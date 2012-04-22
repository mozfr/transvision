<?php


header("Content-type: text/html; charset=UTF-8");

include('../PAGES/locale_find.php');

 
#Include the necessary header 
require_once('../include/header.php');

include('../PAGES/d_html_base.php');


if ($unique==1) {
include '/var/www/frmoz/glossaire/TMX/cache/'.$locale.'/doublons_unique_'.$locale.'.php';
}
else
{
include '/var/www/frmoz/glossaire/TMX/cache/'.$locale.'/doublons_'.$locale.'.php';
}


$i=1;
while ($i <=$k)
   {
   list($rep1,$fichier1,$ent1)=split(':',$entity1[$i]);
   list($rep2,$fichier2,$ent2)=split(':',$entity2[$i]);
   if (($primaire=='tout' and $secondaire=='tout') or ($primaire=='tout' and $rep2==$secondaire) or ($secondaire=='tout' and $rep1==$primaire) or ($rep1==$primaire and $rep2==$secondaire) or ( $rep2==$primaire and $rep1==$secondaire)){
	  echo "	<tr>\n";
	  echo "	  <td>".$entity1[$i]."</td>\n";
	  echo "	  <td>".$enus[$i]."</td>\n";
	  echo "	  <td>".$fr1[$i]."</td>\n";
	  echo "	  <td>".$fr2[$i]."</td>\n";
	  echo "	  <td>".$entity2[$i]."</td>\n";
	  echo "    </tr>\n\n";

   }
   $i++;

}
echo "</table>\n\n";

require_once('../include/footer.html');

?>

