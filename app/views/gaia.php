<?php
namespace Transvision;

require_once INC .'l10n-init.php';

// Serbian hack
$all_locales[] = 'sr-Cyrl';
$all_locales[] = 'sr-Latn';

$get_or_set = function($arr, $value, $fallback) {
    return isset($_GET[$value]) && in_array($_GET[$value], $arr)
            ? $_GET[$value]
            : $fallback;
};

$locale = $get_or_set($all_locales, 'locale', $locale);

$get_repo_strings = function($locale, $repo) {
    return array_filter(Utils::getRepoStrings($locale, $repo), 'strlen');
};

// Set up which repo we want for the view
$repos = [
    'master' => 'gaia',
    'beta' => 'gaia',
    'release' => 'gaia_1_3',
    'old' => 'gaia_1_2'
];

$strings = [
    'gaia'           => $get_repo_strings($locale, 'gaia'),
    'gaia_1_2'       => $get_repo_strings($locale, 'gaia_1_2'),
    'gaia_1_3'       => $get_repo_strings($locale, 'gaia_1_3'),
    'gaia-en-US'     => $get_repo_strings('en-US', 'gaia'),
    'gaia_1_2-en-US' => $get_repo_strings('en-US', 'gaia_1_2'),
    'gaia_1_3-en-US' => $get_repo_strings('en-US', 'gaia_1_3'),
];

// Get the locale list
$loc_list = Files::getFilenamesInFolder(TMX . 'gaia' . '/');

// build the target locale switcher
$target_locales_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

$status = [];
foreach ($repos as $key => $value) {
    $status[] = [$repos_nice_names[$value], count($strings[$value]), count($strings[$value . '-en-US'])];
}

// Overview of string count for the locale
$overview = function($title, $columns, $rows, $anchor) {
    // Titles
    $html = '<table id="' . $anchor . '">'
       . '<tr>'
       . '<th colspan="3">' . $title . '</th>'
       . '</tr>'
       . '<tr>';
    foreach ($columns as $key => $value) {
        $html .= '<th>' . $value . '</th>';
    }
    $html .= '</tr>';

    // Rows
    foreach ($rows as $key => $row) {
        $html .= '<tr>';
        foreach ($row as $key => $value) {
            $html .= '<th>' . $value . '</th>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';

    return $html;
};

print "<h2>$locale</h2>";
print $overview('How many strings are translated?', ['repo', $locale, 'en-US'], $status, 'overview');

// Diverging strings betweet two repositories
$diverging = function ($diverging_sources, $strings, $anchor) use ($locale) {

    foreach ($diverging_sources as $key => $repo_name) {
        $normalized_repo[$repo_name] = array_fill_keys(array_keys($strings[$repo_name . '-en-US']), '');
        $normalized_repo[$repo_name] = array_merge($normalized_repo[$repo_name], $strings[$repo_name]);
    }

    // Intersect
    //$common_strings = array_intersect_key(array_slice($normalized_repo));<- does not work
    $common_strings = array_intersect_key($normalized_repo['gaia'], $normalized_repo['gaia_1_3'], $normalized_repo['gaia_1_2']);//FIXME

    $divergences = [];
    foreach ($common_strings as $k => $v) {
        $temp = [];
        foreach ($normalized_repo as $repo_name => $repo) {
            $temp[] = $repo[$k];
        }

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

    $width = 100 / (count($diverging_sources) + 1);

    $table = '<table id="' . $anchor . '" class="collapsable">'
           . '<tr>'
           . '<th colspan="' . count($diverging_sources) . '">' . count($divergences) . ' diverging translations across repositories</th>'
           . '</tr>'
           . '<tr>';
    $table .= "<th style=\"width:$width%\">Keys</th>";
    foreach ($diverging_sources as $key => $repo_name) {
        $table .= "<th style=\"width:$width%\">" . $repos_nice_names[$repo_name] . '</th>';
    }

    $table .= '</tr>';

    foreach ($divergences as $v) {
        $table .= '<tr>'
                . '<td><span class="celltitle">' . $column_titles[0] . '</span><div class="string">' . ShowResults::formatEntity($v) . '</div></td>';
        foreach ($normalized_repo as $repo_name => $repo) {
            $table .= '<td><span class="celltitle">' . $repo_name . '</span><div class="string">' . ShowResults::highlight($normalized_repo[$repo_name][$v], $locale) . '</div></td>';
        }
        $table .= '</tr>';
    }

    $table .= '</table>';

    return $table;
};

print $diverging(
    [
      $repos['beta'],
      $repos['release'],
      $repos['old'],
     ],
    $strings,
    'diverging'
);

// Changes in en-US
$englishchanges = [$repos['beta'], $repos['release']];

$common_keys = array_intersect_key($strings[$englishchanges[0] . '-en-US'], $strings[$englishchanges[1] . '-en-US']);

$repo1 = $repos_nice_names[$englishchanges[0]];
$repo2 = $repos_nice_names[$englishchanges[1]];
$table = '<table id="englishchanges" class="collapsable">'
       . '<tr>'
       . '<th colspan="3">Strings that have changed significantly in English between ' . $repo1 . ' and ' . $repo2 . ' but for which the entity name didn\'t change</th>'
       . '</tr>'
       . '<tr>'
       . '<th>Key</th>'
       . '<th>' . $repo1 . '</th>'
       . '<th>' . $repo2 . '</th>'
       . '</tr>';

foreach($common_keys as $key =>$val) {
    if (trim(strtolower($strings[$englishchanges[0] . '-en-US'][$key])) != trim(strtolower($strings[$englishchanges[1] . '-en-US'][$key]))) {
            $table .=
              '<tr>'
            . '<td><span class="celltitle">Key</span><div class="string">' . ShowResults::formatEntity($key) . '</div></td>'
            . '<td><span class="celltitle">Gaia' . $repo1 . '</span><div class="string">' . ShowResults::highlight($strings[$englishchanges[0] . '-en-US'][$key], 'en-US') . '<br><small>' . $strings[$englishchanges[0]][$key] . '</small></div></td>'
            . '<td><span class="celltitle">Gaia' . $repo2 . '</span><div class="string">' . ShowResults::highlight($strings[$englishchanges[1] . '-en-US'][$key], 'en-US') . '<br><small>' . $strings[$englishchanges[1]][$key] . '</small></div></td>'
            . '</tr>';
    }
}
$table .= '</table>';

print $table;

// String diff between two repositories
$strings_added = function($table_title, $column_titles, $strings, $repo1, $repo2, $anchor, $cssclass) use ($locale) {
    $temp = array_diff_key($strings[$repo1 . '-en-US'], $strings[$repo2 . '-en-US']);

    $count = count($temp);

    $table = '<table id="' . $anchor . '" class="' . $cssclass . '">'
           . '<tr>'
           . '<th colspan="3">' . $count . ' ' . $table_title . '</th>'
           . '</tr>'
           . '<tr>'
           . '<th>' . $column_titles[0] . '</th>'
           . '<th>' . $column_titles[1] . '</th>'
           . '<th>' . $column_titles[2] . '</th>'
           . '</tr>';

    foreach ($temp as $k => $v) {
        $translation = array_key_exists($k, $strings[$repo1])
                        ? $strings[$repo1][$k]
                        : '<b>String untranslated</b>';

        $table .= '<tr>'
                . '<td><span class="celltitle">' . $column_titles[0] . '</span><div class="string">' . ShowResults::formatEntity($k) . '</td>'
                . '<td><span class="celltitle">' . $column_titles[1] . '</span><div class="string">' . ShowResults::highlight($strings[$repo1][$k], 'en-US') . '</td>'
                . '<td><span class="celltitle">' . $column_titles[2] . '</span><div class="string">' . ShowResults::highlight($translation, $locale) . '</td>'
                . '</tr>';
    }

    $table .= '</table>';

    return $table;
};

print $strings_added(
    'strings added to ',
    ['Key', 'en-US', $locale],
    $strings,
    $repos['beta'],
    $repos['release'],
    'newstrings',
    'collapsable'
);
