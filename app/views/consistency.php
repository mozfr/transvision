<?php
namespace Transvision;

?>
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

        <fieldset class="desktop_repo_only" <?=$filter_visibility?>>
            <label>Filter consistency for</label>
            <div class="select-style">
                <select name="filter" title="Filters" id="simplesearch_filter">
                    <?=$filter_selector?>
                </select>
            </div>
        </fieldset>

        <input type="submit" value="Go" alt="Go" />
        <p class="desktop_repo_only" id="filter_message"><?=$filter_message?></p>
    </fieldset>
</form>
<?php
if ($strings_number == 0) {
    echo '<div class="message"><p>No inconsistent translations found.</p></div>';
} else {
    ?>
    <table>
      <thead>
        <tr class="column_headers">
            <th>English String</th>
            <th>Available Translations</th>
        </tr>
      </thead>
      <tbody>
<?php
    foreach ($inconsistent_translations as $data) {
        $search_link = "/?sourcelocale={$reference_locale}"
        . "&locale={$locale}"
        . "&repo={$repo}"
        . '&search_type=strings&recherche=' . urlencode($data['source'])
        . '&perfect_match=perfect_match';
        echo "<tr>\n";
        echo '<td>';
        echo '<a href="' . $search_link . '" title="Search for this string">' . Utils::secureText($data['source']) . "</a></td>\n";
        echo '<td>';
        foreach ($data['target'] as $target) {
            echo '<div class="inconsistent_translation highlight-specialchars">' . Strings::highlightSpecial(Utils::secureText($target), false) . "</div>\n";
        }
        echo "</td>\n</tr>\n";
    }
    echo "</tbody>\n</table>\n";
}
