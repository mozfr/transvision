<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

$search_id = 'unlocalized_strings';

$content = "<table class='collapsable results_table sortable {$search_id}'>
               <thead>
                 <tr class='column_headers'>
                   <th>English</th>";

foreach ($all_locales as $locale) {
    $content .= "<th>{$locale}</th>";
}

$content .= "</tr>
               </thead>
               <tbody>\n";

foreach ($unlocalized_words as $english_term => $locales) {

    $content .= "  <tr class='{$search_id}'>\n" .
                "    <td>{$english_term}</td>\n";

    foreach ($all_locales as $locale) {
        $count = 0;
        if (in_array($locale, array_keys($locales))) {
            $count = $locales[$locale];
        }

        $link = "/?recherche={$english_term}&repo={$repo}&sourcelocale={$locale}" .
                "&locale={$ref_locale}&search_type=strings&whole_word=whole_word";

        $link_title = $count == 1
            ? 'Search for this occurrence'
            : 'Search for these occurrences';

        $content .= "    <td><a href='{$link}' title='{$link_title}'>{$count}</a></td>\n";
    }
    $content .= "  </tr>\n";
}
$content .= "</tbody>\n</table>\n";

echo $content;
