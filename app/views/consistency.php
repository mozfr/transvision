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

        <?php if (isset($repository_selector)) : ?>
        <fieldset>
            <label>Repository</label>
            <div class="select-style">
                <select name="repo" title="Repository" id="simplesearch_repository">
                <?=$repository_selector?>
                </select>
            </div>
        </fieldset>
        <?php endif; ?>

        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>

<?php
if (isset($inconsistent_translations) && count($inconsistent_translations) > 0) {
    ?>
    <h3>Inconsistent Translations</h3>
    <p class="subtitle">This table includes strings that are identical in the source language (English), but translated in different ways in the target language.</p>
    <table>
      <thead>
        <tr class="column_headers">
            <th>Source String</th>
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
        . '&entire_string=entire_string';
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
} else {
    echo '<h3>Inconsistent Translations</h3><div class="message"><p>No inconsistent translations found.</p></div>';
}

if (isset($inconsistent_sources) && count($inconsistent_sources) > 0) {
    ?>
    <h3>Inconsistent Sources</h3>
    <p class="subtitle">This table includes strings that are identical in the target language, but have a different source string. Case is ignored when evaluating the source string.</p>
    <table>
      <thead>
        <tr class="column_headers">
            <th>Translation</th>
            <th>Source Strings</th>
        </tr>
      </thead>
      <tbody>
<?php
    foreach ($inconsistent_sources as $data) {
        $search_link = "/?sourcelocale={$reference_locale}"
        . "&locale={$locale}"
        . "&repo={$repo}"
        . '&search_type=strings&recherche=' . urlencode($data['target'])
        . '&entire_string=entire_string';
        echo "<tr>\n";
        echo '<td>';
        echo '<a href="' . $search_link . '" title="Search for this string">' . Utils::secureText($data['target']) . "</a></td>\n";
        echo '<td>';
        foreach ($data['source'] as $source) {
            echo '<div class="inconsistent_translation highlight-specialchars">' . Strings::highlightSpecial(Utils::secureText($source), false) . "</div>\n";
        }
        echo "</td>\n</tr>\n";
    }
    echo "</tbody>\n</table>\n";
} else {
    echo '<h3>Inconsistent Sources</h3><div class="message"><p>No inconsistent sources found.</p></div>';
}
