<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

$search_id = 'unlocalized_strings';

$content = "<table class='collapsable results_table sortable {$search_id}'>
               <thead>
                 <tr class='column_headers'>
                   <th>English</th>
                   <th>Occurrences</th>
                 </tr>
               </thead>
               <tbody>\n";

foreach ($unlocalized_words as $english_term => $string_count) {
    $link = "/?recherche={$english_term}&repo={$repo}&sourcelocale={$locale}" .
            "&locale={$ref_locale}&search_type=strings&whole_word=whole_word";

    $link_title = $string_count == 1
        ? 'Search for this occurrence'
        : 'Search for these occurrences';

    $content .= "  <tr class='{$search_id}'>\n" .
                "    <td><a href='{$link}' title='{$link_title}'>{$english_term}</a></td>\n" .
                "    <td>{$string_count}</td>\n" .
                "  </tr>\n";
}
$content .= "</tbody>\n</table>\n";

echo $content;
