<?php
namespace Transvision;
?>
  <div id="current" onclick="javascript:t2t();">You are looking at the <em><?=$repos_nice_names[$check['repo']]?></em> channel <strong><?=$locale?></strong></div>
    <form name="searchform" id="searchform" method="get" action="./" >
        <fieldset id="main">
            <div id="search">
                <p class="smallscreen_notices">Use the <a href="" class="menu-button">Menu tab</a> in the right top corner to select a different view.<p>
                <input type="text"
                       name="recherche"
                       id="recherche"
                       value="<?=$initial_search?>"
                       placeholder="Type your search here"
                       title="Type your search here"
                       size="30"
                />
                <input type="submit" value="Search" alt="Search" />
                <p id="searchcontext">Search will be performed on: <span id="searchcontextvalue"><?=$search_type_descriptions[$check['search_type']]?></span>.</p>
            </div>
            <div id="searchoptions">
                <fieldset>
                    <label>Repository</label>
                    <select
                        id='repository'
                        name='repo'
                        onchange="changeSourceTargetValues(this.value);"
                        title="Repository">
                    <?=$repo_list?>
                    </select>
                    <label class="default_option" for="default_repository">
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
                    <label>Source Locale</label>
                    <select
                        id='source_locale'
                        name='sourcelocale'
                        onchange="changeDefaultSource('source_locale');"
                        title="Source Locale">
                    <?=$source_locales_list[$check['repo']]?>
                    </select>
                    <label class="default_option" for="default_source_locale">
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
                    <label>Target Locale</label>
                    <select
                        id='target_locale'
                        name='locale'
                        onchange="changeDefaultSource('target_locale');"
                        title="Target Locale">
                    <?=$target_locales_list[$check['repo']]?>
                    </select>
                    <label class="default_option" for="default_target_locale">
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
                    <label>Extra Locale</label>
                    <select
                        id='target_locale2'
                        name='locale2'
                        onchange="changeDefaultSource('target_locale2');"
                        title="Extra Locale">
                    <?=$target_locales_list2[$check['repo']]?>
                    </select>
                    <label class="default_option" for="default_target_locale2">
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
                    <label>Search in</label>
                    <select
                        name='search_type'
                        id='search_type'
                        onchange="changeSearchContext(this);"
                        title="Search in">
                    <?=$search_type_list?>
                    </select>
                </fieldset>

                <fieldset id="fs_advanced">
                    <label>Advanced Search options</label>
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
            </div>
            <p id="displaysearchoptions" class="smallscreen_notices"><a class="toggle-searchoptions-link" href="">⇓ Display search options ⇓</a></p>
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
