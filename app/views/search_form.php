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
    <form name="searchform" id="searchform" method="get" action="./" >
        <fieldset id="main_search">
            <div id="search">
                <p class="smallscreen_notices">Use the <a href="" class="menu-button">Menu tab</a> in the right top corner to select a different view.</p>
                <div id="search_field_container">
                    <input type="text"
                           name="recherche"
                           id="recherche"
                           value="<?=Utils::secureText($search->getSearchTerms())?>"
                           placeholder="Type your search here"
                           title="Type your search here"
                           size="30"
                           required
                    />
                    <span id="clear_search" alt="Clear the search field" title="Clear the search field"></span>
                </div>
                <input type="submit" value="Search" alt="Search" />
                <p id="searchcontext">Search will be performed on: <span id="searchcontextvalue"><?=$search_type_descriptions[$search->getSearchType()]?></span>.</p>
            </div>
            <div id="searchoptions">
                <fieldset>
                    <label>Repository</label>
                    <div class="select-style">
                      <select
                          id='repository'
                          name='repo'
                          title="Repository">
                          <?=$repo_list?>
                      </select>
                    </div>
                    <input type="checkbox"
                           id="default_repository"
                           class="mainsearch_default_checkbox"
                           value="<?=$cookies['repository']?>"
                           <?=Utils::checkboxDefaultOption($search->getRepository(), $cookies['repository'])?>
                     /><label class="default_option" for="default_repository">Default</label>
                </fieldset>
                <fieldset>
                    <label>Source Locale</label>
                    <div class="select-style">
                      <select
                          id='source_locale'
                          name='sourcelocale'
                          class='mainsearch_locale_selector'
                          title="Source Locale">
                          <?=$source_locales_list[$search->getRepository()]?>
                      </select>
                    </div>
                      <input type="checkbox"
                             id="default_source_locale"
                             class="mainsearch_default_checkbox"
                             value="<?=$cookies['source_locale']?>"
                             <?=$cookie_option($cookies['source_locale'], $source_locale)?>
                         /><label class="default_option" for="default_source_locale">Default</label>
                </fieldset>
                <fieldset id="locale_switch" alt="Switch source and target locales" title="Switch source and target locales">
                </fieldset>
                <fieldset>
                    <label>Target Locale</label>
                    <div class="select-style">
                      <select
                          id='target_locale'
                          name='locale'
                          class='mainsearch_locale_selector'
                          title="Target Locale">
                          <?=$target_locales_list[$search->getRepository()]?>
                      </select>
                    </div>
                    <input type="checkbox"
                           id="default_target_locale"
                           class="mainsearch_default_checkbox"
                           value="<?=$cookies['target_locale']?>"
                           <?=$cookie_option($cookies['target_locale'], $locale)?>
                     /><label class="default_option" for="default_target_locale">Default</label>
                </fieldset>

    <?php if ($url['path'] == '3locales'): ?>
                <fieldset>
                    <label>Extra Locale</label>
                    <div class="select-style">
                      <select
                          id='target_locale2'
                          name='locale2'
                          class='mainsearch_locale_selector'
                          title="Extra Locale">
                          <?=$target_locales_list2[$search->getRepository()]?>
                      </select>
                    </div>
                    <input type="checkbox"
                           id="default_target_locale2"
                           class="mainsearch_default_checkbox"
                           value="<?=$cookies['target_locale2']?>"
                           <?=$cookie_option($cookies['target_locale2'], $locale2)?>
                     /><label class="default_option" for="default_target_locale2">Default</label>
                </fieldset>
    <?php endif; ?>

                <fieldset>
                    <label>Search in</label>
                    <div class="select-style">
                      <select
                          name='search_type'
                          id='search_type'
                          title="Search in">
                      <?=$search_type_list?>
                      </select>
                    </div>
                    <input type="checkbox"
                           id="default_search_type"
                           class="mainsearch_default_checkbox"
                           value="<?=$cookies['search_type']?>"
                           <?=Utils::checkboxDefaultOption($search->getSearchType(), $cookies['search_type'])?>
                     /><label class="default_option" for="default_search_type">Default</label>
                </fieldset>

                <fieldset id="advanced_search">
                    <label>Advanced Search options</label>
                    <a href="#" id="tooltip_search"><span>i</span></a>
                    <div class="tooltip" id="tooltip_search_text">
                        <a href="#" id="tooltip_search_close" data-tooltip-id="tooltip_search_text" title="Close this panel">×</a>
                        <h2>Case Sensitive</h2>
                        <p>If selected, search with the <strong>exact case</strong> used in the search query. By default, case is ignored.</p>
                        <h2>Each Word</h2>
                        <p>If selected, search for <strong>each word</strong> in the search query (at least 2 characters long). By default, the search query is used as a whole.</p>
                        <h2>Entire String</h2>
                        <p>If selected, the <strong>entire string</strong> needs to match the search query. By default, partial matches are included in the results.</p>
                        <h2>Entire Words</h2>
                        <p>If selected, each search term needs to match an <strong>entire word</strong>. By default, partial word matches are included in the results.</p>
                    </div>
                    <span>
                        <input type="checkbox"
                               name="case_sensitive"
                               id="case_sensitive"
                               value="case_sensitive"
                               class="search_options"
                               <?=Utils::checkboxState($check['case_sensitive'])?>
                         />
                        <label for="case_sensitive">Case Sensitive</label>
                    </span>
                    <span>
                        <input type="checkbox"
                               name="each_word"
                               id="each_word"
                               value="each_word"
                               class="search_options"
                               <?=Utils::checkboxState($check['each_word'])?>
                        />
                        <label for="each_word">Each Word</label>
                    </span>
                    <span>
                        <input type="checkbox"
                               name="entire_string"
                               id="entire_string"
                               value="entire_string"
                               class="search_options"
                               <?=Utils::checkboxState($check['entire_string'])?>
                        />
                        <label for="entire_string">Entire String</label>
                    </span>
                    <span>
                        <input type="checkbox"
                               name="entire_words"
                               id="entire_words"
                               value="entire_words"
                               class="search_options"
                               <?=Utils::checkboxState($check['entire_words'])?>
                        />
                        <label for="entire_words">Entire Words</label>
                    </span>
                </fieldset>
            </div>
            <p id="displaysearchoptions" class="smallscreen_notices"><a class="toggle-searchoptions-link" href="">⇓ Display search options ⇓</a></p>
        </fieldset>
 </form>
