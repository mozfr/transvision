<?php
namespace Transvision;

?>
<p class="intro">This view displays strings that are empty in the reference language
    but have a translation in the requested language, and viceversa.
    It can be used to identify strings that should have been localized and
    are empty by mistake, and strings that should actually remain empty.
</p>
<form name="searchform" id="simplesearchform" method="get" action="">
    <fieldset id="main_search">

        <?php if (isset($target_locales_list)) : ?>
        <fieldset>
            <label>Locale</label>
            <div class="select-style">
                <select name="locale" title="Locale" id="simplesearch_locale">
                <?=$target_locales_list?>
                </select>
            </div>
        </fieldset>
        <?php endif; ?>

        <?php if (isset($channel_selector)) : ?>
        <fieldset>
            <label>Repository</label>
            <div class="select-style">
                <select name="repo" title="Repository" id="simplesearch_repository">
                <?=$channel_selector?>
                </select>
            </div>
        </fieldset>
        <?php endif; ?>

        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>
<?php if (isset($filter_block)) {
    ?>
<div id="filters">
    <h4>Filter by folder:</h4>
    <a href="#showall" id="showall" class="filter">Show all results</a>
    <?=$filter_block; ?>
</div>
<?php

}

if (count($empty_strings) == 0) {
    echo "<div class=\"message\"><p>No strings found.</p></div>";
} else {
    $text_direction = RTLSupport::getDirection($locale);
    $table = "<table class='collapsable results_table sortable'>
                 <thead>
                   <tr class='column_headers'>
                     <th>Entity</th>
                     <th>{$reference_locale}</th>
                     <th>{$locale}</th>
                   </tr>
                 </thead>
                 <tbody>\n";

    foreach ($empty_strings as $key => $strings) {
        $entity = ShowResults::formatEntity($key);
        $component = explode('/', $key)[0];
        $reference_string = htmlspecialchars($strings['reference']);
        $locale_string = Strings::highlightSpecial(htmlspecialchars($strings['translation']), false);

        $entity_link = "?sourcelocale={$reference_locale}"
        . "&locale={$locale}"
        . "&repo={$repo}"
        . "&search_type=entities&recherche={$key}"
        . "&perfect_match=perfect_match";

        $bugzilla_link = [Bugzilla::reportErrorLink(
            $locale, $key, $reference_string, $locale_string, $repo, $entity_link
        )];

        $reference_path = VersionControl::hgPath($reference_locale, $repo, $key);
        $locale_path = VersionControl::hgPath($locale, $repo, $key);

        if (! $reference_string) {
            $reference_string = '<em class="error">(empty)</em>';
        }
        if ($locale_string == '@@missing@@') {
            $locale_string = '<em class="error">Missing string</em>';
        } elseif ($locale_string == '') {
            $locale_string = '<em class="error">(empty)</em>';
        }

        // Replace / and : in the key name and use it as an anchor name
        $anchor_name = str_replace(['/', ':'], '_', $key);

        $table .= "
            <tr class='{$component}'>
              <td>
                <span class='celltitle'>Entity</span>
                <a class='resultpermalink tag' id='{$anchor_name}' href='#{$anchor_name}' title='Permalink to this string'>link</a>
                <a class='l10n tag' href='/string/?entity={$key}&amp;repo={$repo}' title='List all translations for this entity'>l10n</a>
                <a class='link_to_entity' href=\"/{$entity_link}\">{$entity}</a>
              </td>
              <td dir='ltr' lang='{$reference_locale}'>
                <span class='celltitle'>{$reference_locale}</span>
                <div class='string'>
                  {$reference_string}
                </div>
                <div dir='ltr' class='result_meta_link'>
                  <a class='source_link' href='{$reference_path}'>
                    &lt;source&gt;
                  </a>
                </div>
              </td>
              <td dir='{$text_direction}' lang='{$locale}'>
                <span class='celltitle'>{$locale}</span>
                <div class='string'>{$locale_string}</div>
                <div dir='ltr' class='result_meta_link'>
                  <a class='source_link' href='{$locale_path}'>
                    &lt;source&gt;
                  </a>
                  &nbsp;
                  <a class='bug_link' target='_blank' href='{$bugzilla_link[0]}'>
                    &lt;report a bug&gt;
                  </a>
                </div>
              </td>
            </tr>";
    }
    $table .= "  </tbody>\n</table>\n";

    echo $table;
}
