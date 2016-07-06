<?php
namespace Transvision;

echo "<h2>Perfect matches</h2>\n" .
     "<div id='glossary_matches'>\n";

if (count($perfect_results) > 0) {
    echo "  <ol dir='{$locale_dir}'>\n";
    foreach ($perfect_results as $val) {
        echo '    <li>' . Utils::secureText($val) . "</li>\n";
    }
    echo "  </ol>\n" .
         "</div>\n";

    echo "<h2>Results</h2>
    <table class='collapsable'>
      <thead>
        <tr class='column_headers'>
          <th>Localized string</th>
          <th>Source string</th>
        </tr>
      </thead>
      <tbody>\n";

    foreach ($imperfect_results as $string_id => $string_value) {
        $entity_link = "/?sourcelocale={$source_locale}"
        . "&locale={$locale}"
        . "&repo={$repo}"
        . "&search_type=entities&recherche={$string_id}"
        . "&perfect_match=perfect_match";

        echo "<tr>\n";
        echo "  <td dir='{$locale_dir}'><span class='celltitle'>Localized string</span><div class='string'><a href='{$entity_link}'>" . Utils::secureText($string_value) . "</a></div></td>\n";
        echo "  <td dir='{$source_locale_dir}'><span class='celltitle'>Source string</span><div class='string'>" . Utils::secureText($tmx_source[$string_id]) . "</div></td>\n";
        echo "</tr>\n";
    }
    echo "</tbody>\n</table>\n";
} else {
    echo "  <p>No perfect match found.</p>\n</div>\n";
}
