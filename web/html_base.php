<?php

# Get the locale list

$dirs = array_filter(glob('/var/www/frenchmozilla.org/frmoz/transvision/TMX/'.$base.'/*'), 'is_dir');
foreach ($dirs as $dir) {
	$locs=explode('/',$dir);
	$loc=array_pop($locs);
	$loc_list[]=$loc;}



# Begining of the html code



echo "<body>\n\n";
echo "  <h1>Transvision glossary</h1>\n\n";
echo "  <h1>".$base." ".$locale."</h1>\n\n";

echo "    <form method=\"get\" action=\"index.php\" >\n";
echo "      <p>\n";
echo "        <input type=\"text\" name=\"recherche\" value=\"".$recherche3."\" size=\"30\" />\n";
echo"       </p>\n";
echo "	    <p>\n";

if ($check6=='trunk'){
	$tr='checked';
	$au='';
	$be='';
	$re='';}
if ($check6=='aurora'){
	$tr='';
	$au='checked';
	$be='';
	$re='';}
if ($check6=='beta'){
	$tr='';
	$au='';
	$be='checked';
	$re='';}
if ($check6=='release'){
	$tr='';
	$au='';
	$be='';
	$re='checked';}

echo"	<select name='locale'>";
foreach ($loc_list as $loc) {
	if($loc==$locale)
		{$ch=' selected';}
	 else 
		{$ch='';}
	echo "<option".$ch." value=".$loc.">".$loc."</option>";
	}
echo"	</select>";


	
echo "	    <input type=\"radio\" name=\"repo\" value=\"trunk\"".$tr.">Central";
echo "	    <input type=\"radio\" name=\"repo\" value=\"aurora\"".$au.">Aurora";
echo "	    <input type=\"radio\" name=\"repo\" value=\"beta\"".$be.">Beta";
echo "	    <input type=\"radio\" name=\"repo\" value=\"release\"".$re.">Release";
echo "	    <p>\n";
echo "	    <p>\n";
echo"         <input type=\"checkbox\" name=\"case_sensitive\" value=\"case_sensitive\"".$check." />Case sensitive\n";
echo "        <input type=\"checkbox\" name=\"regular\" value=\"regular\"".$check2." />Regular Expression\n";
echo "        <input type=\"checkbox\" name=\"wild\" value=\"wild\" ".$check3." />* wildcard\n";
echo "        <input type=\"checkbox\" name=\"whole_word\" value=\"whole_word\" ".$check5." /> whole word\n";
echo "        <input type=\"checkbox\" name=\"ent\" value=\"ent\" ".$check4." /> entity search\n";
echo "        <input type=\"checkbox\" name=\"perfect_match\" value=\"perfect_match\" ".$check7." /> Perfect match\n";
//echo "        <input type=\"checkbox\" name=\"alignement\" value=\"alignement\" ".$check8." /> Alignement\n";
echo "        <input type=\"checkbox\" name=\"t2t\" value=\"t2t\" ".$check9." /> Glossaire\n";
echo"       </p>\n";
echo "      <p>\n";
echo"         <input type=\"submit\" value=\"Search&hellip;\" alt=\"Search&hellip;\" />\n";
echo "      </p>\n";
echo "    </form>\n\n";



?>
