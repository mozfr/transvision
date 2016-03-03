<?php
namespace Transvision;

?>
<form name="searchform" id="simplesearchform" method="get" action="">
    <fieldset id="main_search">

        <?php if (isset($target_locales_list)) : ?>
        <fieldset>
            <label>Locale</label>
            <select name="locale" title="Locale" id="simplesearch_locale">
            <?=$target_locales_list?>
            </select>
        </fieldset>
        <?php endif; ?>

        <?php if (isset($channel_selector)) : ?>
        <fieldset>
            <label>Repository</label>
            <select name="repo" title="Repository" id="simplesearch_repository">
            <?=$channel_selector?>
            </select>
        </fieldset>
        <?php endif; ?>

        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>

<?php
if ($strings_number == 0) {
    echo '<div class="message"><p>No inconsistent translations found.</p></div>';
} else {
    ?>
    <table>
        <tr>
            <th>English String</th>
            <th>Available Translations</th>
        </tr>
<?php
    foreach ($inconsistent_translations as $data) {
        $search_link = "/?sourcelocale={$reference_locale}"
        . "&locale={$locale}"
        . "&repo={$repo}"
        . '&search_type=strings&recherche=' . urlencode($data['source']);
        echo "<tr>\n";
        echo '<td>';
        echo '<a href="' . $search_link . '" title="Search for this string">' . Utils::secureText($data['source']) . "</a></td>\n";
        echo '<td>';
        foreach ($data['target'] as $target) {
            echo '<div class="inconsistent_translation">' . Utils::secureText($target) . "</div>\n";
        }
        echo "</td>\n</tr>\n";
    }
    echo "</table>\n";
}
