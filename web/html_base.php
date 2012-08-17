<?php

if (!isset($valid) || $valid == false) {
    die("File can't be called directly");
}

// Get the locale list
$loc_list = scandir(TMX . $check['repo'] . '/');
$loc_list = array_diff($loc_list, array('.', '..'));


// build a locale switcher
$option_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $option_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}


// select the branch
$tr = $au = $be = $re = '';
if ($check['repo'] == 'central') $tr = 'checked';
if ($check['repo'] == 'aurora')  $au = 'checked';
if ($check['repo'] == 'beta')    $be = 'checked';
if ($check['repo'] == 'release') $re = 'checked';

?>

  <div id="current" onclick="javascript:t2t();">You are looking at the <?=$check['repo']?> channel (<?=$locale?>)</div>
    <form name="searchform" method="get" action="./" >
        <fieldset id="main">
            <fieldset>
                <legend>Locale:</legend>
                <select name='locale'>
                <?=$option_list?>
                </select>
            </fieldset>

            <fieldset>
                <legend>Channel</legend>
                <input type="radio" name="repo" value="central" id="central" <?=$tr?> ><label for="central">Central</label>
                <input type="radio" name="repo" value="aurora"  id="aurora"  <?=$au?> ><label for="aurora">Aurora</label>
                <input type="radio" name="repo" value="beta"    id="beta"    <?=$be?> ><label for="beta">Beta</label>
                <input type="radio" name="repo" value="release" id="release" <?=$re?> ><label for="release">Release</label>
            </fieldset>

            <fieldset>
                <legend>Search options</legend>
                <input type="checkbox" name="case_sensitive" id="case_sensitive" value="case_sensitive" <?=checkboxState($check['case_sensitive'])?> />
                <label for="case_sensitive">Case sensitive</label>
                <input type="checkbox" name="regular" id="regular" value="regular" <?=checkboxState($check['regular'])?> />
                <label for="regular">Regular Expression</label>
                <input type="checkbox" name="wild" id="wild" value="wild"          <?=checkboxState($check['wild'])?> />
                <label for="wild">* wildcard</label>
                <input type="checkbox" name="whole_word" id="whole_word" value="whole_word" <?=checkboxState($check['whole_word'])?> />
                <label for="whole_word">whole word</label>
                <input type="checkbox" name="ent" id="ent" value="ent" <?=checkboxState($check['ent'])?> />
                <label for="ent">entity search</label>
                <input type="checkbox" name="perfect_match" id="perfect_match" value="perfect_match" <?=checkboxState($check['perfect_match'])?> />
                <label for="perfect_match">Perfect match</label>
                <!--
                <input type="checkbox" name="alignement" id="alignement" value="alignement" <?=checkboxState($check['alignement'])?> />
                <label for="alignement">Alignement</label>
                -->
                <input type="checkbox" name="t2t" id="t2t" value="t2t"  <?=checkboxState($check['t2t'])?> onclick="uncheck();"/>
                <label for="t2t">Glossary</label>
            </fieldset>

            <fieldset>
                <legend>Start search</legend>
                    <input type="text" name="recherche" id="recherche" value="<?=$recherche?>" placeholder="Type your search term here" size="30" />
                    <input type="submit" value="Go" alt="Go" />
            </fieldset>
        </fieldset>
 </form>

 <script>
function uncheck() {
    var arr = ['case_sensitive', 'regular', 'wild', 'ent', 'whole_word', 'perfect_match'];
    for (var i = 0; i < arr.length; i++) {
        el = document.getElementById(arr[i]);
        if (el.disabled) {
            el.removeAttribute('disabled');
        } else {
            el.setAttribute('disabled', 'disabled');
        }
    }
}

</script>
