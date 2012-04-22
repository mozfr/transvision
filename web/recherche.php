<?php

#escape the / 
#$recherche=str_replace("/", "\/", $recherche);

# If $recherche consits of several words, each are stocked on the $aaa variable
$aaa=explode(" ",$recherche);

#$selected_radio = $_GET["case_sensitive"];

# If case sensitive is checked, 
# The search is made for each word (aa) of the searched string (aaa)

if ($check5=='checked'){$b='\b';}
else{$b='';}
if ($check=='checked'){$i='';}
else{$i='i';} 

$keys = preg_grep("/".$b.$aaa[0].$b."/".$i, $l_en);
$keys2 = preg_grep("/".$b.$aaa[0].$b."/".$i, $l_fr);

foreach ($aaa as $aa)
	{
	$keys = preg_grep("/".$b.$aa.$b."/".$i,$keys);
	$keys2 = preg_grep("/".$aa.$b."/".$i,$keys2);

		}
?>
