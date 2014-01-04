<?php
namespace Transvision;

// build the repository switcher
$repo_list = Utils::getHtmlSelectOptions($repos_nice_names, $check['repo'], true);

// Get the locale list for every repo and build his target/source locale switcher values.
$loc_list = array();
$target_locales_list  = array();
$source_locales_list  = array();

// 3locales view variables
$target_locales_list2 = array();

$repositories = Utils::getFilenamesInFolder(TMX . '/');
foreach ($repositories as $repository) {
    $loc_list[$repository] = Utils::getFilenamesInFolder(TMX . $repository . '/');
    sort($loc_list[$repository]);

    // build the source locale switcher
    $source_locales_list[$repository] = Utils::getHtmlSelectOptions($loc_list[$repository], $source_locale);

    // build the target locale switcher
    $target_locales_list[$repository] = Utils::getHtmlSelectOptions($loc_list[$repository], $locale);

    // 3locales view: build the target locale switcher for a second locale
    $target_locales_list2[$repository] = Utils::getHtmlSelectOptions($loc_list[$repository], $locale2);
}

// Build the search type switcher
$search_type_descriptions = [
    'strings' => 'Strings',
    'entities'=> 'Entities',
    'strings_entities' => 'Strings & Entities'
];

$search_type_list = Utils::getHtmlSelectOptions(
    $search_type_descriptions,
    $check['search_type'],
    true
);

// Get COOKIES
$get_cookie = function($var) {
    return isset($_COOKIE[$var]) ? $_COOKIE[$var] : '';
};

$cookie_repository     = $get_cookie('default_repository');
$cookie_source_locale  = $get_cookie('default_source_locale');
$cookie_target_locale  = $get_cookie('default_target_locale');
$cookie_target_locale2 = $get_cookie('default_target_locale2');

?>

  <div id="current" onclick="javascript:t2t();">You are looking at the <em><?=$repos_nice_names[$check['repo']]?></em> channel <strong><?=$locale?></strong></div>
    <form name="searchform" method="get" action="./" >
        <fieldset id="main">
            <div id="search">
                <input type="text" name="recherche" id="recherche" value="<?=$initial_search?>" placeholder="Type your search here" size="30" />
                <input type="submit" value="Search" alt="Search" />
                <p id="searchcontext">Search will be performed on: <span id="searchcontextvalue"><?=$search_type_descriptions[$check['search_type']]?></span>.</p>
            </div>
            <fieldset>
                <legend>Repository</legend>
                <select id='repository' name='repo'  onchange="changeSourceTargetValues(this.value);">
                <?=$repo_list?>
                </select>
                <label class="default_option">
                    <input type="checkbox"
                           id="default_repository"
                           value="<?=$check['repo']?>"
                           data-cookie="<?=$cookie_repository?>"
                           onclick="setCookie('default_repository',this.value,3650);"
                           <?=Utils::checkboxDefaultOption($check['repo'], $cookie_repository)?>
                     /> <span>Default</span>
                 </label>
            </fieldset>
            <fieldset>
                <legend>Source Locale</legend>
                <select id='source_locale' name='sourcelocale' onchange="changeDefaultSource('source_locale');">
                <?=$source_locales_list[$check['repo']]?>
                </select>
                <label class="default_option">
                    <input type="checkbox"
                           id="default_source_locale"
                           value="<?=$source_locale?>"
                           data-cookie="<?=$cookie_source_locale?>"
                           onclick="setCookie('default_source_locale',this.value,3650);"
                            <?php
                            // Mark as default only if the cookie_source_locale exist in repository array
                            if (in_array($cookie_source_locale, $loc_list[$check['repo']])) {
                                echo Utils::checkboxDefaultOption($source_locale, $cookie_source_locale);
                            }
                            ?>
                     /> <span>Default</span>
                 </label>
            </fieldset>
            <fieldset>
                <legend>Target Locale</legend>
                <select id='target_locale' name='locale' onchange="changeDefaultSource('target_locale');">
                <?=$target_locales_list[$check['repo']]?>
                </select>
                <label class="default_option">
                    <input type="checkbox"
                           id="default_target_locale"
                           value="<?=$locale?>"
                           data-cookie="<?=$cookie_target_locale?>"
                           onclick="setCookie('default_target_locale',this.value,3650);"
                            <?php
                            // Mark as default only if the cookie_target_locale exist in repository array
                            if (in_array($cookie_target_locale, $loc_list[$check['repo']])) {
                                echo Utils::checkboxDefaultOption($locale, $cookie_target_locale);
                            }
                            ?>
                     /> <span>Default</span>
                 </label>
            </fieldset>

<?php if ($url['path'] == '3locales'): ?>
            <fieldset>
                <legend>Extra Locale</legend>
                <select id='target_locale2' name='locale2' onchange="changeDefaultSource('target_locale2');">
                <?=$target_locales_list2[$check['repo']]?>
                </select>
                <label class="default_option">
                    <input type="checkbox"
                           id="default_target_locale2"
                           value="<?=$locale2?>"
                           data-cookie="<?=$cookie_target_locale2?>"
                           onclick="setCookie('default_target_locale2',this.value,3650);"
                            <?php
                            // Mark as default only if the cookie_target_locale exist in repository array
                            if (in_array($cookie_target_locale2, $loc_list[$check['repo']])) {
                                echo Utils::checkboxDefaultOption($locale2, $cookie_target_locale2);
                            }
                            ?>
                     /> <span>Default</span>
                 </label>
            </fieldset>
<?php endif; ?>

            <fieldset>
                <legend>Search in</legend>
                <select name='search_type' id='search_type' onchange="changeSearchContext(this);">
                <?=$search_type_list?>
                </select>
            </fieldset>

            <fieldset id="fs_advanced">
                <legend>Advanced Search options</legend>
                <span>
                    <input type="checkbox"
                           name="case_sensitive"
                           id="case_sensitive"
                           value="case_sensitive"
                           <?=Utils::checkboxState($check['case_sensitive'])?>
                     />
                    <label for="case_sensitive">Case Sensitive</label>
                </span>
                <span>
                    <input type="checkbox"
                           name="wild"
                           id="wild"
                           value="wild"
                           <?=Utils::checkboxState($check['wild'])?>
                     />
                    <label for="wild">Wildcard (*)</label>
                </span>
                <span>
                    <input type="checkbox"
                           name="whole_word"
                           id="whole_word"
                           value="whole_word"
                           <?=Utils::checkboxState($check['whole_word'])?>
                    />
                    <label for="whole_word">Whole Word</label>
                </span>
                <span>
                    <input type="checkbox"
                           name="perfect_match"
                           id="perfect_match"
                           value="perfect_match"
                           <?=Utils::checkboxState($check['perfect_match'])?>
                    />
                    <label for="perfect_match">Perfect Match</label>
                </span>

                <?php if ($check['t2t'] == 't2t') :?>
                <input type="hidden"
                       name="t2t"
                       id="t2t"
                       value="t2t"
                       <?=Utils::checkboxState($check['t2t'], 't2t')?> onclick="uncheckAll();"
                />
                <?php endif; ?>
            </fieldset>
        </fieldset>
 </form>

<script>
function uncheckAll() {
    var arr = [<?php
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
//Change related imput value and check if default
function changeDefaultSource(selector) {
    var selectValue = document.getElementById(selector).value;
    var defaultInput = document.getElementById('default_' + selector);
    defaultInput.value = selectValue;
    if (defaultInput.value == defaultInput.getAttribute('data-cookie')) {
        defaultInput.checked = true;
    } else {
        defaultInput.checked = false;
    }
}
//Set Cookie to store default value
function setCookie(cookieName,cookieValue,nDays) {
    var today = new Date();
    var expire = new Date();
    var defaultInput = document.getElementById(cookieName);
    if(!defaultInput.checked) {
        //Input un-checked -> Delete cookie
        cookieValue = '';
        nDays = 0;
    }
    expire.setTime(today.getTime() + 3600000*24*nDays);
    document.cookie = cookieName + '=' + escape(cookieValue) + ';expires=' + expire.toGMTString();
}
//Change related imput value and check if default
function changeSourceTargetValues(repository) {
    var repo_source = {};
    var repo_target = {};
<?php
foreach ($repositories as $repository) {
    echo "    repo_source['" . $repository . "'] = '" . $source_locales_list[$repository] . "'; \n";
    echo "    repo_target['" . $repository . "'] = '" . $target_locales_list[$repository] . "'; \n";
}
?>
    document.getElementById('source_locale').innerHTML = repo_source[repository];
    changeDefaultSource('source_locale');
    document.getElementById('target_locale').innerHTML = repo_target[repository];
    changeDefaultSource('target_locale');

<?php if ($url['path'] == '3locales'):?>
    document.getElementById('target_locale2').innerHTML = repo_target[repository];
    changeDefaultSource('target_locale2');
<?php endif;?>


    changeDefaultSource('repository');
}
//Change the label below the search field to reflect the value of "Search in"
function changeSearchContext(element) {
    document.getElementById('searchcontextvalue').innerHTML = element.options[element.selectedIndex].text;
}
</script>
<?php


if ($initial_search != '') {
    // create a json file logging locale/number of requests
    $stats = Json::fetch(WEBROOT . 'stats.json');
    $stats[$locale] = (array_key_exists($locale, $stats)) ?  $stats[$locale] += 1 : 1 ;
    file_put_contents(WEBROOT . 'stats.json', json_encode($stats));

    // create a json file logging search options to determine if some are unused
    $stats = Json::fetch(WEBROOT . 'stats_requests.json');
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
