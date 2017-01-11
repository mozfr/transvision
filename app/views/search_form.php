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
                <p class="smallscreen_notices">Use the <a href="" class="menu-button">Menu tab</a> in the right top corner to select a different view.</p>
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
                    <span>
                        <input type="checkbox"
                               name="case_sensitive"
                               id="case_sensitive"
                               value="case_sensitive"
                               class="search_options"
                               <?=Utils::checkboxState($check['case_sensitive'])?>
                         />
                        <label for="case_sensitive" aria-describedby="tooltip_case">Case Sensitive</label>
                        <div id="tooltip_case" class="search_tooltip" role="tooltip">If selected, search with the <strong>exact case</strong> used in the search query. By default, case is ignored.</div>
                    </span>
                    <span>
                        <input type="checkbox"
                               name="each_word"
                               id="each_word"
                               value="each_word"
                               class="search_options"
                               <?=Utils::checkboxState($check['each_word'])?>
                        />
                        <label for="each_word" aria-describedby="tooltip_eachword">Each Word</label>
                        <div id="tooltip_eachword" class="search_tooltip" role="tooltip">If selected, search for <strong>each word</strong> in the search query (at least 2 characters long). By default, the search query is used as a whole.</div>
                    </span>
                    <span>
                        <input type="checkbox"
                               name="entire_string"
                               id="entire_string"
                               value="entire_string"
                               class="search_options"
                               <?=Utils::checkboxState($check['entire_string'])?>
                        />
                        <label for="entire_string" aria-describedby="tooltip_entirestring">Entire String</label>
                        <div id="tooltip_entirestring" class="search_tooltip" role="tooltip">If selected, the <strong>entire string</strong> needs to match the search query. By default, partial matches are included in the results.</div>
                    </span>
                    <span>
                        <input type="checkbox"
                               name="entire_words"
                               id="entire_words"
                               value="entire_words"
                               class="search_options"
                               <?=Utils::checkboxState($check['entire_words'])?>
                        />
                        <label for="entire_words" aria-describedby="tooltip_entirewords">Entire Words</label>
                        <div id="tooltip_entirewords" class="search_tooltip" role="tooltip">If selected, each search term needs to match an <strong>entire word</strong>. By default, partial word matches are included in the results.</div>
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
