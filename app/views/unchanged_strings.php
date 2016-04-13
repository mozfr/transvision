<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

$search_id = 'unchanged_strings';
$content = "<h2><span class=\"results_count_{$search_id}\">"
    . Utils::pluralize(count($unchanged_strings), 'string')
    . "</span> identical to English</h2>\n";

if (isset($filter_block)) {
    $content .= "<div id='filters'>" .
                "  <h4>Filter by folder:</h4>\n" .
                "  <a href='#showall' id='showall' class='filter'>Show all results</a>\n" .
                $filter_block .
                "</div>\n";
}

$content .= "<table class='collapsable results_table sortable {$search_id}'>
               <thead>
                 <tr class='column_headers'>
                   <th>String ID</th>
                   <th>English</th>
                   <th>Translation</th>
                 </tr>
               </thead>
               <tbody>\n";
foreach ($unchanged_strings as $string_id => $string_value) {
    $component = explode('/', $string_id)[0];

    $entity_link = "/?sourcelocale={$source_locale}"
            . "&locale={$locale}"
            . "&repo={$repo}"
            . "&search_type=entities&recherche={$string_id}";

    /*
        Since this view displays strings identical to source locale, I'll use the same
        direction, not the locale's default (for example I'll use LTR for English+Arabic).
    */
    $direction = RTLSupport::getDirection($source_locale);

    $content .= "  <tr class='{$component} {$search_id}'>\n" .
                "    <td dir='ltr'><a href='{$entity_link}'>{$string_id}</a></td>\n" .
                "    <td dir='{$direction}' lang='{$source_locale}'>{$string_value}</td>\n" .
                "    <td dir='{$direction}' lang='{$locale}'>" . $strings_locale[$string_id] . "</td>\n" .
                "  </tr>\n";
}
$content .= "</tbody>\n</table>\n";

echo $content;
