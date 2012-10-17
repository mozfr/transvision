<?php

// Get the locale list
$loc_list = scandir(TMX . $check['repo'] . '/');
$loc_list = array_diff($loc_list, array('.', '..'));
$spanish  = array_search('es', $loc_list);

if ($spanish) {
    $loc_list[$spanish] = 'es-ES';
}

// build the target locale switcher
$target_locales_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}

// build the source locale switcher
$source_locales_list = '';
$loc_list[] = 'en-US';
sort($loc_list);

foreach ($loc_list as $loc) {
    $ch = ($loc == $sourceLocale) ? ' selected' : '';
    $source_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}


// select the branch
$tr = $au = $be = $re = $ga = '';
if ($check['repo'] == 'central') $tr = 'checked';
if ($check['repo'] == 'aurora')  $au = 'checked';
if ($check['repo'] == 'beta')    $be = 'checked';
if ($check['repo'] == 'release') $re = 'checked';
if ($check['repo'] == 'gaia')    $ga = 'checked';

?>

  <div id="current" onclick="javascript:t2t();">You are looking at the <?=$check['repo']?> channel (<?=$locale?>)</div>
    <form name="searchform" method="get" action="./" >
        <fieldset id="main">

            <fieldset>
                <legend>Source Locale:</legend>
                <select name='sourcelocale'>
                <?=$source_locales_list?>
                </select>
            </fieldset>
            <fieldset>
                <legend>Target Locale:</legend>
                <select name='locale'>
                <?=$target_locales_list?>
                </select>
            </fieldset>

            <fieldset>
                <legend>Channel</legend>
                <input type="radio" name="repo" value="central" id="central" <?=$tr?> ><label for="central">Central</label>
                <input type="radio" name="repo" value="aurora"  id="aurora"  <?=$au?> ><label for="aurora">Aurora</label>
                <input type="radio" name="repo" value="beta"    id="beta"    <?=$be?> ><label for="beta">Beta</label>
                <input type="radio" name="repo" value="release" id="release" <?=$re?> ><label for="release">Release</label>
                <input type="radio" name="repo" value="gaia"    id="gaia"    <?=$ga?> ><label for="gaia">Gaia only</label>
            </fieldset>

            <fieldset>
                <legend>Search options</legend>
                <input type="checkbox" name="case_sensitive" id="case_sensitive" value="case_sensitive" <?=checkboxState($check['case_sensitive'])?> />
                <label for="case_sensitive">Case sensitive</label>
                <input type="checkbox" name="wild" id="wild" value="wild"          <?=checkboxState($check['wild'])?> />
                <label for="wild">* Wildcard</label>
                <input type="checkbox" name="whole_word" id="whole_word" value="whole_word" <?=checkboxState($check['whole_word'])?> />
                <label for="whole_word">Whole Word</label>
                <input type="checkbox" name="ent" id="ent" value="ent" <?=checkboxState($check['ent'])?> />
                <label for="ent">Entity Search</label>
                <input type="checkbox" name="perfect_match" id="perfect_match" value="perfect_match" <?=checkboxState($check['perfect_match'])?> />
                <label for="perfect_match">Perfect match</label>
                <input type="checkbox" name="t2t" id="t2t" value="t2t"  <?=checkboxState($check['t2t'])?> onclick="uncheck();"/>
                <label for="t2t">Glossary</label>
            </fieldset>

            <fieldset>
                <legend>Start search</legend>
                    <input type="text" name="recherche" id="recherche" value="<?=$initial_search?>" placeholder="Type your search term here" size="30" />
                    <input type="submit" value="Go" alt="Go" />
            </fieldset>
        </fieldset>
 </form>

 <script>
function uncheck() {
    var arr = ['case_sensitive', 'wild', 'ent', 'whole_word', 'perfect_match'];
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
