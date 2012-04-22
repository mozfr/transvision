<?php

// Search for the sting
include('recherche.php');
//Get the locale results
foreach ($keys as $key=>$chaine) {
	$results[$key]=strtolower($l_fr[$key]);
}

// regroup the results in one string
if (count($results)>3){

$total=implode(' ',$results);
$mots=explode(" ",$total);
// Count the occurence
$compte=array_count_values($mots);
$compte2=array_flip($compte);

ksort($compte2);


//print_r($compte2);
$i=0;
$j=0;


while ($i<3) {
	$mot=array_pop($compte2);
		while(strlen($mot)<4){
			$mot=array_pop($compte2);
		}	
	$moti[$i]=$mot;
	$i=$i+1;
}
foreach ($moti as $mot){
	echo $mot.'<br>';

}

}
?>

