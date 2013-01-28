<?php

// rtl support
$rtl = array('ar', 'fa', 'he');
$direction1 = (in_array($sourceLocale, $rtl)) ? 'rtl' : 'ltr';
$direction2 = (in_array($locale, $rtl)) ? 'rtl' : 'ltr';


$table  = "\n\n  <table>\n\n";
$table .= "    <tr>\n";
$table .= "      <th>Entity</th>\n";
$table .= "      <th>" . $sourceLocale . "</th>\n";
$table .= "      <th>" . $locale . "</th>\n";
$table .= "    </tr>\n\n";

foreach ($entities as $val) {

    $path_locale1 = pathFileInRepo($sourceLocale, $check['repo'], $val);
    $path_locale2 = pathFileInRepo($locale, $check['repo'], $val);

    $target_string = str_replace(' ', '<span class="highlight-gray"> </span>', $tmx_target[$val]); // nbsp highlight
    $table .= "    <tr>\n";
    $table .= "      <td>" . formatEntity($val) . "</a></td>\n";
    $table .= "      <td dir='" . $direction1. "'>". $tmx_source[$val] . "<a href=\"$path_locale1\" style=\"float:right\"><em>source</em></a></td>\n";
    $table .= "      <td dir='" . $direction2. "'>" . $target_string . "<a href=\"$path_locale2\" style=\"float:right\"><em>source</em></a></td>\n";
    $table .= "    </tr>\n\n";
}

$table .= "  </table>\n\n";

echo $table;
