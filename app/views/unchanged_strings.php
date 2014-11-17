<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

$content = '<h2>' . count($unchanged_strings) . " strings identical to English</h2>\n";

if(isset($filter_block)) {
    $content .= "<div id='filters'>" .
                "  <h4>Filter by folder:</h4>\n" .
                "  <a href='#showall' id='showall' class='filter'>Show all results</a>\n" .
                $filter_block .
                "</div>\n";
}

$content .= "<table class='collapsable'>\n";
$content .= "  <thead>\n" .
            "    <tr>\n" .
            "      <th>String ID</th>\n" .
            "      <th>English</th>\n" .
            "      <th>Translation</th>\n" .
            "    </tr>\n" .
            "  </thead>\n" .
            "  <tbody>\n";
foreach ($unchanged_strings as $string_id => $string_value) {
    $component = explode('/', $string_id)[0];
    $content .= "    <tr class='{$component}'>\n" .
                "      <td>{$string_id}</td>\n" .
                "      <td>{$string_value}</td>\n" .
                "      <td>" . $strings_locale[$string_id] ."</td>\n" .
                "    </tr>\n";
}
$content .= "  </tbody>\n</table>\n";

echo $content;
