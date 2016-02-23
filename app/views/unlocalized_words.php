<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

$search_id = 'unlocalized_strings';

$content = "<table class='collapsable results_table sortable {$search_id}'>\n" .
            "  <tr class='column_headers'>\n" .
            "    <th>English</th>\n" .
            "    <th>Occurrences</th>\n" .
            "  </tr>\n";

foreach ($unlocalized_words as $string_id => $string_value) {
    $link = "/?recherche={$string_id}&repo={$repo}&sourcelocale={$locale}" .
            "&locale={$ref_locale}&search_type=strings&whole_word=whole_word";

    $link_title = $string_value == 1
        ? 'Search for this occurrence'
        : 'Search for these occurrences';

    $content .= "  <tr class='{$search_id}'>\n" .
                "    <td><a href='{$link}' title='{$link_title}'>{$string_id}</a></td>\n" .
                "    <td>{$string_value}</td>\n" .
                "  </tr>\n";
}
$content .= "</table>\n";

echo $content;
