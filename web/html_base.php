<?php

if (!$valid) {
    die("File can't be called directly");
}

// Get the locale list
$loc_list = scandir('/home/pascalc/transvision/TMX/' . $base . '/');
$loc_list = array_diff($loc_list, array('.', '..'));


// build a locale switcher
$option_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $option_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}


// select the branch
$tr = $au = $be = $re = '';
if ($check['repo'] == 'trunk')   $tr = 'checked';
if ($check['repo'] == 'aurora')  $au = 'checked';
if ($check['repo'] == 'beta')    $be = 'checked';
if ($check['repo'] == 'release') $re = 'checked';

?>

<body>
  <h1>Transvision glossary</h1>
  <h1><?=$base?> <?=$locale?></h1>
    <form method="get" action="index.php" >
      <p>
        <input type="text" name="recherche" value="<?=$recherche3?>" size="30" />
      </p>
      <p>

      <select name='locale'>
      <?=$option_list?>
      </select>

      <input type="radio" name="repo" value="trunk"   <?=$tr?> >Central
      <input type="radio" name="repo" value="aurora"  <?=$au?> >Aurora
      <input type="radio" name="repo" value="beta"    <?=$be?> >Beta
      <input type="radio" name="repo" value="release" <?=$re?> >Release
   </p>

   <p>
     <input type="checkbox" name="case_sensitive" value="case_sensitive" <?=checkboxState($check['case_sensitive'])?> />Case sensitive
     <input type="checkbox" name="regular"        value="regular"        <?=checkboxState($check['regular'])?> />Regular Expression
     <input type="checkbox" name="wild"           value="wild"           <?=checkboxState($check['wild'])?> />* wildcard
     <input type="checkbox" name="whole_word"     value="whole_word"     <?=checkboxState($check['whole_word'])?> />whole word
     <input type="checkbox" name="ent"            value="ent"            <?=checkboxState($check['ent'])?> />entity search
     <input type="checkbox" name="perfect_match"  value="perfect_match"  <?=checkboxState($check['perfect_match'])?> />Perfect match
<!--
     <input type="checkbox" name="alignement"     value="alignement"     <?=checkboxState($check['alignement'])?> /> Alignement
-->
     <input type="checkbox" name="t2t"            value="t2t"            <?=checkboxState($check['t2t'])?> /> Glossaire
   </p>

   <p>
     <input type="submit" value="Search&hellip;" alt="Search&hellip;" />
   </p>
 </form>
