<?php
namespace Transvision;

/*
    This function will mark a <select> option as selected if a cookie is set.
*/
$cookie_option = function ($cookie, $locale) use ($search) {
    if (in_array($cookie, Project::getRepositoryLocales($search->getRepository()))) {
        return Utils::checkboxDefaultOption($locale, $cookie);
    }
};
?>
  <div id="current">You are looking at the <em><?=$repos_nice_names[$search->getRepository()]?></em> channel <strong><?=$locale?></strong></div>
    <form name="searchform" id="searchform" method="get" action="./" >
        <fieldset id="main_search">
            <div id="search">
                <p class="smallscreen_notices">Use the <a href="" class="menu-button">Menu tab</a> in the right top corner to select a different view.<p>
                <div id="search_field_container">
                    <input type="text"
                           name="recherche"
                           id="recherche"
                           value="<?=Utils::secureText($search->getSearchTerms())?>"
                           placeholder="Type your search here"
                           title="Type your search here"
                           size="30"
                    />
                    <span id="clear_search" alt="Clear the search field" title="Clear the search field"></span>
                </div>
                <input type="submit" value="Search" alt="Search" />
                <p id="searchcontext">Search will be performed on: <span id="searchcontextvalue"><?=$search_type_descriptions[$search->getSearchType()]?></span>.</p>
            </div>
            <div id="searchoptions">
                <fieldset>
                    <label>Repository</label>
                    <select
                        id='repository'
                        name='repo'
                        title="Repository">
                        <?=$repo_list?>
                    </select>
                    <label class="default_option" for="default_repository">
                        <input type="checkbox"
                               id="default_repository"
                               class="mainsearch_default_checkbox"
                               value="<?=$cookies['repository']?>"
                               <?=Utils::checkboxDefaultOption($search->getRepository(), $cookies['repository'])?>
                         /> <span>Default</span>
                     </label>
                </fieldset>
                <fieldset>
                    <label>Source Locale</label>
                    <select
                        id='source_locale'
                        name='sourcelocale'
                        class='mainsearch_locale_selector'
                        title="Source Locale">
                        <?=$source_locales_list[$search->getRepository()]?>
                    </select>
                    <label class="default_option" for="default_source_locale">
                        <input type="checkbox"
                               id="default_source_locale"
                               class="mainsearch_default_checkbox"
                               value="<?=$cookies['source_locale']?>"
                               <?=$cookie_option($cookies['source_locale'], $source_locale)?>
                         /> <span>Default</span>
                     </label>
                </fieldset>
                <fieldset id="locale_switch" alt="Switch source and target locales" title="Switch source and target locales">
                </fieldset>
                <fieldset>
                    <label>Target Locale</label>
                    <select
                        id='target_locale'
                        name='locale'
                        class='mainsearch_locale_selector'
                        title="Target Locale">
                        <?=$target_locales_list[$search->getRepository()]?>
                    </select>
                    <label class="default_option" for="default_target_locale">
                        <input type="checkbox"
                               id="default_target_locale"
                               class="mainsearch_default_checkbox"
                               value="<?=$cookies['target_locale']?>"
                               <?=$cookie_option($cookies['target_locale'], $locale)?>
                         /> <span>Default</span>
                     </label>
                </fieldset>

    <?php if ($url['path'] == '3locales'): ?>
                <fieldset>
                    <label>Extra Locale</label>
                    <select
                        id='target_locale2'
                        name='locale2'
                        class='mainsearch_locale_selector'
                        title="Extra Locale">
                        <?=$target_locales_list2[$search->getRepository()]?>
                    </select>
                    <label class="default_option" for="default_target_locale2">
                        <input type="checkbox"
                               id="default_target_locale2"
                               class="mainsearch_default_checkbox"
                               value="<?=$cookies['target_locale2']?>"
                               <?=$cookie_option($cookies['target_locale2'], $locale2)?>
                         /> <span>Default</span>
                     </label>
                </fieldset>
    <?php endif; ?>

                <fieldset>
                    <label>Search in</label>
                    <select
                        name='search_type'
                        id='search_type'
                        title="Search in">
                    <?=$search_type_list?>
                    </select>
                    <label class="default_option" for="default_search_type">
                        <input type="checkbox"
                               id="default_search_type"
                               class="mainsearch_default_checkbox"
                               value="<?=$cookies['search_type']?>"
                               <?=Utils::checkboxDefaultOption($search->getSearchType(), $cookies['search_type'])?>
                         /> <span>Default</span>
                     </label>
                </fieldset>

                <fieldset id="advanced_search">
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
                           <?=Utils::checkboxState($check['t2t'], 't2t')?>
                    />
                    <?php endif; ?>
                </fieldset>
            </div>
            <p id="displaysearchoptions" class="smallscreen_notices"><a class="toggle-searchoptions-link" href="">⇓ Display search options ⇓</a></p>
        </fieldset>
 </form>
