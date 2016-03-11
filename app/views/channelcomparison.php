<?php
namespace Transvision;

?>
<form id ="searchform" name="searchform" method="get" action="">
    <fieldset id="main_search">

        <fieldset>
            <label>Locale:</label>
            <select name='locale'>
            <?=$target_locales_list?>
            </select>
        </fieldset>
        <fieldset>
            <label>Channel 1:</label>
            <select name='chan1'>
            <?=$chan_selector1?>
            </select>
        </fieldset>
        <fieldset>
            <label>Channel 2:</label>
            <select name='chan2'>
            <?=$chan_selector2?>
            </select>
        </fieldset>
        <input type="submit" value="Go" alt="Go" />

    </fieldset>
</form>

<?php

if (empty($common_strings)) {
    echo "<h3>Comparison is empty</h3>\n" .
         "<p class='subtitle'>There are no string differences for this locale between the {$repos_nice_names[$chan1]} and {$repos_nice_names[$chan2]} channels.</p>\n";
} else {
    echo "
        <h2>Locale: {$locale}</h2>
        <table class='collapsable'>
          <thead>
            <tr class='column_headers'>
              <th>Key</th>
              <th>{$chan1}</th>
              <th>{$chan2}</th>
            </tr>
          </thead>
          <tbody>\n";

    foreach ($common_strings as $key => $value) {
        echo   "  <tr>"
             . "    <td><span class='celltitle'>Key</span><div class='string'>" . ShowResults::formatEntity($key) . "</div></td>\n"
             . "    <td><span class='celltitle'>{$chan1}</span><div class='string'>" . ShowResults::highlight($value) . "</div></td>\n"
             . "    <td><span class='celltitle'>{$chan2}</span><div class='string'>" . ShowResults::highlight($strings[$chan2][$key]) . "</div></td>\n"
             . "  </tr>\n";
    }
    echo "</tbody>\n</table>\n";
}
