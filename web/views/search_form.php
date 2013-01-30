<?php
namespace Transvision;
// Page title
$title = 'Transvision glossary <a href="./news/#v' . VERSION . '">' . VERSION . '</a>';

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

// build the repository switcher
$repo_list = '';
foreach (array('central', 'aurora', 'beta', 'release', 'gaia') as $val) {
    $ch = ($val == $check['repo']) ? ' selected' : '';
    $repo_list  .= "\t<option" . $ch . " value=" . $val . ">" . ucfirst($val) . "</option>\n";
}

?>

  <div id="current" onclick="javascript:t2t();">You are looking at the <?=$check['repo']?> channel <strong><?=$locale?></strong></div>
    <form name="searchform" method="get" action="./" >
        <fieldset id="main">

            <fieldset>
                <legend>Source Locale</legend>
                <select name='sourcelocale'>
                <?=$source_locales_list?>
                </select>
            </fieldset>
            <fieldset>
                <legend>Target Locale</legend>
                <select name='locale'>
                <?=$target_locales_list?>
                </select>
            </fieldset>

            <fieldset>
                <legend>Repository</legend>
                <select name='repo'>
                <?=$repo_list?>
                </select>
            </fieldset>

            <fieldset>
                <legend>Search options</legend>
                <input type="checkbox" name="case_sensitive" id="case_sensitive" value="case_sensitive" <?=Utils::checkboxState($check['case_sensitive'])?> />
                <label for="case_sensitive">Case Sensitive</label>
                <input type="checkbox" name="wild" id="wild" value="wild"          <?=Utils::checkboxState($check['wild'])?> />
                <label for="wild">Wildcard (*)</label>
                <input type="checkbox" name="whole_word" id="whole_word" value="whole_word" <?=Utils::checkboxState($check['whole_word'])?> />
                <label for="whole_word">Whole Word</label>
                <input type="checkbox" name="ent" id="ent" value="ent" <?=Utils::checkboxState($check['ent'])?> onclick="uncheck('ent', 'key_val');" />
                <label for="ent">Entities</label>

<?php if ($locale == 'da'): ?>
                <input type="checkbox" name="key_val" id="key_val" value="key_val" <?=Utils::checkboxState($check['key_val'])?>  onclick="uncheck('key_val', 'ent');" />
                <label for="key_val">Entities and strings</label>
<?php endif; ?>

                <input type="checkbox" name="perfect_match" id="perfect_match" value="perfect_match" <?=Utils::checkboxState($check['perfect_match'])?> />
                <label for="perfect_match">Perfect Match</label>

                <input type="hidden" name="t2t" id="t2t" value="t2t"  <?=Utils::checkboxState($check['t2t'], 't2t')?> onclick="uncheckAll();"/>

            </fieldset>

            <div id="search">
                <input type="text" name="recherche" id="recherche" value="<?=$initial_search?>" placeholder="Type your search here" size="30" />
                <input type="submit" value="Search" alt="Search" />
            </div>
        </fieldset>
 </form>

 <script>
function uncheckAll() {
    var arr = ['case_sensitive', 'wild', 'ent', 'whole_word', 'perfect_match', 'key_val'];
    for (var i = 0; i < arr.length; i++) {
        el = document.getElementById(arr[i]);
        if (el.disabled) {
            el.removeAttribute('checked');
        } else {
            el.setAttribute('checked', 'checked');
        }
    }
}
function uncheck(val1, val2) {
    source = document.getElementById(val1);
    target = document.getElementById(val2);
    if (source.checked == true) {
        target.checked = false;
    }
}

</script>
<?php


if ($initial_search != '') {
    // create a json file logging locale/number of requests
    $stats = json_decode(file_get_contents(WEBROOT . 'stats.json'), true);
    $stats[$locale] = (array_key_exists($locale, $stats)) ?  $stats[$locale] += 1 : 1 ;
    file_put_contents(WEBROOT . 'stats.json', json_encode($stats));

    // create a json file logging search options to determine if some are unused
    $stats = json_decode(file_get_contents(WEBROOT . 'stats_requests.json'), true);
    foreach ($check as $k => $v) {
        if (in_array(
            $k,
            array('case_sensitive', 'wild', 'ent', 'whole_word', 'perfect_match', 't2t', 'key_val')
        )
            && $check[$k] == 1) {
            $stats[$k] = (array_key_exists($k, $stats)) ? $stats[$k] += 1 : 1;
            file_put_contents(WEBROOT . 'stats_requests.json', json_encode($stats)
            );
        }
    }
    unset($stats);
}

// Search results process
if ($check['t2t']) {
    require_once VIEWS . 't2t.php';
} else {
    // result presentation
    if ($recherche != '') {
        if ($check['ent']) {
            require_once VIEWS . 'results_ent.php';
        } else {
            require_once VIEWS . 'results.php';
        }
    }
}
