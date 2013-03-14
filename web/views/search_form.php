<?php
namespace Transvision;

// Page title
$title = 'Transvision <a href="./news/#v' . VERSION . '">' . VERSION . '</a>';

// Get the locale list
$loc_list = Utils::getFilenamesInFolder(TMX . $check['repo']. '/');

// Gaia hack
$spanish  = array_search('es', $loc_list);
if ($spanish) {
    $loc_list[$spanish] = 'es-ES';
}

// build the target locale switcher
$target_locales_list = Utils::getHtmlSelectOptions($loc_list, $locale);

// build the source locale switcher
$loc_list[] = 'en-US';
sort($loc_list);
$source_locales_list = Utils::getHtmlSelectOptions($loc_list, $sourceLocale);

// build the repository switcher
$repo_list = Utils::getHtmlSelectOptions($repos, $check['repo']);

// Build the search type switcher
$search_type_list = Utils::getHtmlSelectOptions(
    array('strings' => 'Strings', 'entities'=> 'Entities', 'strings_entities' => 'Strings & Entities'),
    $check['search_type'],
    true
);

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
                <legend>Search in</legend>
                <select name='search_type'>
                <?=$search_type_list?>
                </select>
            </fieldset>

            <fieldset>
                <legend>Advanced Search options</legend>
                <input type="checkbox"
                       name="case_sensitive"
                       id="case_sensitive"
                       value="case_sensitive"
                       <?=Utils::checkboxState($check['case_sensitive'])?>
                 />
                <label for="case_sensitive">Case Sensitive</label>

                <input type="checkbox"
                       name="wild"
                       id="wild"
                       value="wild"
                       <?=Utils::checkboxState($check['wild'])?>
                 />
                <label for="wild">Wildcard (*)</label>

                <input type="checkbox"
                       name="whole_word"
                       id="whole_word"
                       value="whole_word"
                       <?=Utils::checkboxState($check['whole_word'])?>
                />
                <label for="whole_word">Whole Word</label>

                <input type="checkbox"
                       name="perfect_match"
                       id="perfect_match"
                       value="perfect_match"
                       <?=Utils::checkboxState($check['perfect_match'])?>
                />
                <label for="perfect_match">Perfect Match</label>

                <?php if ($check['t2t'] == 't2t') :?>
                <input type="hidden"
                       name="t2t"
                       id="t2t"
                       value="t2t"
                       <?=Utils::checkboxState($check['t2t'], 't2t')?> onclick="uncheckAll();"
                />
                <?php endif;?>

            </fieldset>

            <div id="search">
                <input type="text" name="recherche" id="recherche" value="<?=$initial_search?>" placeholder="Type your search here" size="30" />
                <input type="submit" value="Search" alt="Search" />
            </div>
        </fieldset>
 </form>

 <script>
function uncheckAll() {
    var arr = [<?
        $count_form_checkboxes = 0;
        foreach ($form_checkboxes as $v) {
            $end  = ($count_form_checkboxes == count($form_checkboxes) - 1) ? '' : ', ';
            echo "'" . $v . "'" . $end;
            $count_form_checkboxes++;
        }
    ?>];
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
        if (in_array($k, $form_checkboxes) && $v == 1) {
            $stats[$k] = (array_key_exists($k, $stats)) ? $stats[$k] += 1 : 1;
        }
        if (in_array($k, array_diff($form_search_options, $form_checkboxes))) {
            $stats[$v] = (array_key_exists($v, $stats)) ? $stats[$v] += 1 : 1;
        }

        file_put_contents(WEBROOT . 'stats_requests.json', json_encode($stats));
    }
    unset($stats);
}

// Search results process
if ($check['t2t']) {
    require_once VIEWS . 't2t.php';
} else {
    // result presentation
    if ($my_search != '') {
        if ($check['search_type'] == 'entities') {
            require_once VIEWS . 'results_ent.php';
        } else {
            require_once VIEWS . 'results.php';
        }
    }
}
