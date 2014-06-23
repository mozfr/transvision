<?php
namespace Transvision;

if (count($perfect_results) > 0) {
    print '<h4>Perfect matches</h4>';
    print "<ol dir='{$locale_dir}'>";
    foreach ($perfect_results as $val) {
        print '<li>' . Utils::secureText($val) . '</li>';
    }
    print '</ol>';
} else {
    print '<p>No perfect match found.</p>';
    return;
}

print "<b>Used in</b>
<table class='collapsable'>
  <tr>
    <th>Localized string</th>
    <th>Source string</th>
  </tr>\n";

foreach ($imperfect_results as $key => $val) {
    print "<tr>\n";
    print "  <td dir='{$locale_dir}'><span class='celltitle'>Localized string</span><div class='string'>" . Utils::secureText($val) . "</div></td>\n";
    print "  <td dir='{$source_locale_dir}'><span class='celltitle'>Source string</span><div class='string'>" . Utils::secureText($tmx_source[$key]) . "</div></td>\n";
    print "</tr>\n";
}
print "</table>\n";
