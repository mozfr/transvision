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
    $table .= "<tr>
                    <td>" . Utils::formatEntity($val, $my_search) . "</a></td>
                    <td dir='${direction1}'>
                       <div class='string'>" . $tmx_source[$val] . "</div>
                       <div class='sourcelink'><a href='${path_locale1}'><em>&lt;source&gt;</em></a></div>
                    </td>
                     <td dir='${direction2}'>
                       <div class='string'>${target_string}</div>
                       <div class='sourcelink'><a href='${path_locale2}'><em>&lt;source&gt;</em></a></div>
                    </td>
                </tr>";
}

$table .= "  </table>\n\n";

echo $table;
