<?php
namespace Transvision;

// rtl support
$rtl = array('ar', 'fa', 'he');
$direction1 = (in_array($sourceLocale, $rtl)) ? 'rtl' : 'ltr';
$direction2 = (in_array($locale, $rtl)) ? 'rtl' : 'ltr';


$table  = "<table>
             <tr>
               <th>Entity</th>\n
               <th>" . $sourceLocale . "</th>
               <th>" . $locale . "</th>
             </tr>";

foreach ($entities as $val) {

    $path_locale1 = Utils::pathFileInRepo($sourceLocale, $check['repo'], $val);
    $path_locale2 = Utils::pathFileInRepo($locale, $check['repo'], $val);

    if (isset($tmx_target[$val])) {
        $target_string = str_replace(' ', '<span class="highlight-gray"> </span>', $tmx_target[$val]); // nbsp highlight
    } else {
        $target_string = '';
    }
    $table .= "    <tr>\n";
    $table .= "      <td>" . Utils::formatEntity($val, $recherche) . "</a></td>\n";
    $table .= "      <td dir='" . $direction1. "'>". $tmx_source[$val] . "<a href=\"$path_locale1\" style=\"float:right\"><em>source</em></a></td>\n";
    $table .= "      <td dir='" . $direction2. "'>" . $target_string . "<a href=\"$path_locale2\" style=\"float:right\"><em>source</em></a></td>\n";
    $table .= "    </tr>\n\n";
}

$table .= "  </table>\n\n";

echo $table;
