<?php
namespace Transvision;

require_once WEBROOT .'inc/l10n-init.php';

// Serbian hack
$allLocales[] = 'sr-Cyrl';
$allLocales[] = 'sr-Latn';

//~ error_log(print_r($repos,1));

$get_or_set = function($arr, $value, $fallback) {
    return isset($_GET[$value]) && in_array($_GET[$value], $arr)
            ? $_GET[$value]
            : $fallback;
};

$locale = $get_or_set($allLocales, 'locale', $locale);

$get_repo_strings = function($locale, $repo) {
    return array_filter(Utils::getRepoStrings($locale, $repo), 'strlen');
};

$strings = [
    'gaia'           => $get_repo_strings($locale, 'gaia'),
    'gaia_1_1'       => $get_repo_strings($locale, 'gaia_1_1'),
    'gaia_1_2'       => $get_repo_strings($locale, 'gaia_1_2'),
    'gaia-en-US'     => $get_repo_strings('en-US', 'gaia'),
    'gaia_1_1-en-US' => $get_repo_strings('en-US', 'gaia_1_1'),
    'gaia_1_2-en-US' => $get_repo_strings('en-US', 'gaia_1_2'),
];

// Get the locale list
$loc_list = Utils::getFilenamesInFolder(TMX . 'gaia' . '/');

// build the target locale switcher
$target_locales_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

$status = [
    ['Gaia l10n', count($strings['gaia']), count($strings['gaia-en-US'])],
    ['Gaia 1.1',  count($strings['gaia_1_1']), count($strings['gaia_1_1-en-US'])],
    ['Gaia 1.2',  count($strings['gaia_1_2']), count($strings['gaia_1_2-en-US'])],
];

$table = function($title, $columns, $rows, $anchor) {
    $html = '<table id="' . $anchor . '">'
       . '<tr>'
       . '<th colspan="3">' . $title . '</th>'
       . '</tr>'
       . '<tr>'
       . '<th>' . $columns[0] . '</th>'
       . '<th>' . $columns[1] . '</th>'
       . '<th>' . $columns[2] . '</th>'
       . '</tr>';


    $row = function($arr) {
        return '<tr><th>' . $arr[0] . '</th><td>' . $arr[1] . '</td><td>' . $arr[2] . '</td></tr>';
    };
    $html .= $row($rows[0]);
    $html .= $row($rows[1]);
    $html .= $row($rows[2]);
    $html .= '</table>';

    return $html;
};

print "<h2>$locale</h2>";
print $table('How many strings are translated?', ['repo', $locale, 'en-US'], $status, 'hihi');


$table5col = function ($table_title, $column_titles, $strings, $anchor) use ($locale) {

    $english_gaia_keys    = array_fill_keys(array_keys($strings['gaia-en-US']), '');
    $english_gaia1_1_keys = array_fill_keys(array_keys($strings['gaia_1_1-en-US']), '');
    $english_gaia1_2_keys = array_fill_keys(array_keys($strings['gaia_1_2-en-US']), '');

    $normalized_gaia_locale    = array_merge($english_gaia_keys, $strings['gaia']);
    $normalized_gaia1_1_locale = array_merge($english_gaia1_1_keys, $strings['gaia_1_1']);
    $normalized_gaia1_2_locale = array_merge($english_gaia1_2_keys, $strings['gaia_1_2']);

    $common_strings = array_intersect_key(
        $normalized_gaia1_2_locale,
        $normalized_gaia_locale,
        $normalized_gaia1_1_locale
    );

    $divergences = [];
    foreach ($common_strings as $k => $v) {
        $temp = [
            $normalized_gaia_locale[$k],
            $normalized_gaia1_1_locale[$k],
            $normalized_gaia1_2_locale[$k]
        ];

        // remove blanks
        $temp = array_filter($temp, 'strlen');

        // remove duplicates
        $temp = array_unique ($temp);

        // if we have a string in one repo or no string, skip the key
        if (count($temp) <= 1 ) {
            continue;
        }

        $divergences[] = $k;
    }

    $table = '<table id="' . $anchor . '">'
           . '<tr>'
           . '<th colspan="4">' . count($divergences) . ' ' . $table_title . '</th>'
           . '</tr>'
           . '<tr>'
           . '<th style="width:25%">' . $column_titles[0] . '</th>'
           . '<th style="width:25%">' . $column_titles[1] . '</th>'
           . '<th style="width:25%">' . $column_titles[2] . '</th>'
           . '<th style="width:25%">' . $column_titles[3] . '</th>'
           . '</tr>';

    foreach ($divergences as $v) {
        $table .= '<tr>'
                . '<td>' . ShowResults::formatEntity($v) . '</td>'
                . '<td>' . ShowResults::highlight($normalized_gaia_locale[$v], $locale) . '</td>'
                . '<td>' . ShowResults::highlight($normalized_gaia1_1_locale[$v], $locale) . '</td>'
                . '<td>' . ShowResults::highlight($normalized_gaia1_2_locale[$v], $locale) . '</td>'
                . '</tr>';
    }

    $table .= '</table>';

    return $table;
};

print $table5col(
    'diverging translations across repositories',
    ['Key',
     $repos_nice_names['gaia'],
     $repos_nice_names['gaia_1_1'],
     $repos_nice_names['gaia_1_2']
     ],
    $strings,
    'differences'
);

$common_keys = array_intersect_key($strings['gaia_1_1-en-US'],$strings['gaia_1_2-en-US']);

$table = '<table id="englishchanges">'
       . '<tr>'
       . '<th colspan="3">Strings that have changed significantly in English between Gaia 1.1 and 1.2 but for which the entity name didn\'t change</th>'
       . '</tr>'
       . '<tr>'
       . '<th>Key</th>'
       . '<th>Gaia 1.1</th>'
       . '<th>Gaia 1.2</th>'
       . '</tr>';


foreach($common_keys as $key =>$val) {
    if (trim(strtolower($strings['gaia_1_1-en-US'][$key])) != trim(strtolower($strings['gaia_1_2-en-US'][$key]))) {
            $table .= '<tr>'
            . '<td>' . ShowResults::formatEntity($key) . '</td>'
            . '<td>' . ShowResults::highlight($strings['gaia_1_1-en-US'][$key], 'en-US') . '</td>'
            . '<td>' . ShowResults::highlight($strings['gaia_1_2-en-US'][$key], 'en-US') . '</td>'
            . '</tr>';
    }
}
$table .= '</table>';

print $table;

$table3col = function($table_title, $column_titles, $strings, $anchor) use ($locale) {
    $strings = array_values($strings);
    $temp = array_diff_key($strings[5], $strings[4]);

    $count = count($temp);

    $table = '<table id="' . $anchor . '">'
           . '<tr>'
           . '<th colspan="3">' . $count . ' ' . $table_title . '</th>'
           . '</tr>'
           . '<tr>'
           . '<th>' . $column_titles[0] . '</th>'
           . '<th>' . $column_titles[1] . '</th>'
           . '<th>' . $column_titles[2] . '</th>'
           . '</tr>';


    foreach ($temp as $k => $v) {
        $translation = array_key_exists($k, $strings[2])
                        ? $strings[2][$k]
                        : '<b>String untranslated</b>';

        $table .= '<tr>'
                . '<td>' . ShowResults::formatEntity($k) . '</td>'
                . '<td>' . ShowResults::highlight($strings[5][$k], 'en-US') . '</td>'
                . '<td>' . ShowResults::highlight($translation, $locale) . '</td>'
                . '</tr>';
    }

    $table .= '</table>';

    return $table;
};


print $table3col(
    'strings added to Gaia 1.2',
    ['Key', 'en-US', $locale],
    $strings,
    'newstrings'
);
