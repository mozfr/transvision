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
// mxr support
$mxr_url  = "http://mxr.mozilla.org/comm-${check['repo']}/search?find=";
$mxr_field_limit = 27;

foreach ($entities as $val) {

    // let's analyse the entity for the search string
    $search = explode('/', $val);
    $mxr_url  = "http://mxr.mozilla.org/comm-${check['repo']}/search?find=";

    if ($search[0] == 'apps') {
        $mxr_link = formatEntity($val);
    } else {
        // We chop search strings with mb_strimwidth()
        // because  of field length limits in mxr)
        $search = mb_strimwidth($search[0] . '.*' . $search[1], 0, $mxr_field_limit) . '&amp;string=' . mb_strimwidth($search[2], 0, 29);
        $mxr_link = '<a href="' . $mxr_url . $search . '">' . formatEntity($val) . '</a>';
    }

    $path_locale1 = pathFileInRepo($sourceLocale, $check['repo'], $val);
    $path_locale2 = pathFileInRepo($locale, $check['repo'], $val);

    $target_string = str_replace(' ', '<span class="highlight-gray"> </span>', $tmx_target[$val]); // nbsp highlight
    $table .= "    <tr>\n";
    $table .= "      <td>" . $mxr_link . "</a></td>\n";
    $table .= "      <td dir='" . $direction1. "'>". $tmx_source[$val] . "<a href=\"$path_locale1\" style=\"float:right\"><em>source</em></a></td>\n";
    $table .= "      <td dir='" . $direction2. "'>" . $target_string . "<a href=\"$path_locale2\" style=\"float:right\"><em>source</em></a></td>\n";
    $table .= "    </tr>\n\n";
}

$table .= "  </table>\n\n";

echo $table;

