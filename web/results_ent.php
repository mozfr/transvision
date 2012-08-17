<?php

if (!valid($valid)) return;

$table  = "\n\n  <table>\n\n";
$table .= "    <tr>\n";
$table .= "      <th>Entity</th>\n";
$table .= "      <th>en-US</th>\n";
$table .= "      <th>" . $locale . "</th>\n";
$table .= "    </tr>\n\n";

foreach ($entities as $val) {
    // let's analyse the entity for the search string
    $search = explode(':', $val);
    $search = $search[0] . '.*' . $search[1] . '&amp;string=' . $search[2];
    $mxr_url  = "http://mxr.mozilla.org/comm-${check['repo']}/search?find=";
    $mxr_link = '<a href="' . $mxr_url . $search . '">' . formatEntity($val) . '</a>';

    $target_string = str_replace(' ', '<span class="highlight-gray"> </span>',  $tmx_target[$val]); // nbsp highlight
    $table .= "    <tr>\n";
    $table .= "      <td>" . $mxr_link . "</a></td>\n";
    $table .= "      <td dir='" . $direction1. "'>". $tmx_source[$val] . "</td>\n";
    $table .= "      <td dir='" . $direction2. "'>" . $target_string . "</td>\n";
    $table .= "    </tr>\n\n";
}

$table .= "  </table>\n\n";

echo $table;

