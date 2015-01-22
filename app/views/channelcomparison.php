<?php
namespace Transvision;

require_once INC .'l10n-init.php';

$chan1 = 'aurora';
$chan2 = 'beta';

if (isset($_GET['chan1']) && in_array($_GET['chan1'], $desktop_repos)) {
    $chan1 = $_GET['chan1'];
}

if (isset($_GET['chan2']) && in_array($_GET['chan2'], $desktop_repos)) {
    $chan2 = $_GET['chan2'];
}

$strings = array();
$strings[$chan1] = Utils::getRepoStrings($locale, $chan1);
$strings[$chan2] = Utils::getRepoStrings($locale, $chan2);

$chan_selector1 = $chan_selector2 = '';

foreach ($desktop_repos as $repo) {
    $ch1 = ($repo == $chan1) ? ' selected' : '';
    $ch2 = ($repo == $chan2) ? ' selected' : '';
    $chan_selector1 .= "\t<option" . $ch1 . " value=" . $repo . ">" .$repos_nice_names[$repo] . "</option>\n";
    $chan_selector2 .= "\t<option" . $ch2 . " value=" . $repo . ">" .$repos_nice_names[$repo] . "</option>\n";
}

// Get the locale list
$loc_list = Project::getRepositoryLocales($repo);

// build the target locale switcher
$target_locales_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}

$temp = array_intersect_key($strings[$chan1], $strings[$chan2]);
$temp = array_diff($temp, $strings[$chan2]);
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

echo "\n<table class='collapsable'>" .
     "  <tr>\n" .
     "    <th colspan='3'>Locale: {$locale}</th>\n" .
     "  </tr>\n" .
     "  <tr>\n" .
     "    <th>Key</th>\n" .
     "    <th>{$chan1}</th>\n" .
     "    <th>{$chan2}</th>\n" .
     "  </tr>\n";

foreach ($temp as $k => $v) {
    echo   "  <tr>"
         . "    <td><span class='celltitle'>Key</span><div class='string'>" . ShowResults::formatEntity($k) . "</div></td>\n"
         . "    <td><span class='celltitle'>{$chan1}</span><div class='string'>" . ShowResults::highlight($v, $locale) . "</div></td>\n"
         . "    <td><span class='celltitle'>{$chan2}</span><div class='string'>" . ShowResults::highlight($strings[$chan2][$k], $locale) . "</div></td>\n"
         . "  </tr>\n";
}
echo "</table>\n";
