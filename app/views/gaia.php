<?php
namespace Transvision;

// Set $repo to get the correct list of locales for Gaia from l10n-init.php
$repo = 'gaia';
require_once INC . 'l10n-init.php';

// Functions
$get_or_set = function($arr, $value, $fallback) {
    return isset($_GET[$value]) && in_array($_GET[$value], $arr)
            ? $_GET[$value]
            : $fallback;
};

$get_repo_strings = function($locale, $repo) {
    return array_filter(Utils::getRepoStrings($locale, $repo), 'strlen');
};

$build_select = function($array_in, $selected_elm) use ($repos_nice_names) {
    $string_out = '';
    foreach ($array_in as $elm) {
        $ch = ($elm == $selected_elm) ? ' selected' : '';
        $elm_nice_name = isset($repos_nice_names[$elm]) ? $repos_nice_names[$elm] : $elm;
        $string_out .= "<option" . $ch . " value=" . $elm . ">" . $elm_nice_name . "</option>\n";
    }
    return $string_out;
};

// Variables

// Project::getGaiaRepositories() returns ordered repos from newest to oldest
$repo1 = 'gaia';
$repo2 = Project::getLastGaiaBranch();

$locale = $get_or_set($all_locales, 'locale', $locale);
$repo1 = $get_or_set($gaia_repos, 'repo1', $repo1);
$repo2 = $get_or_set($gaia_repos, 'repo2', $repo2);

// Get the locale list
$loc_list = Project::getRepositoryLocales('gaia');

// build the target locale & channels switchers
$target_locales_list = '';
$channel_selector1 = '';
$channel_selector2 = '';

$target_locales_list = $build_select($loc_list, $locale);
$channel_selector1 = $build_select($gaia_repos, $repo1);
$channel_selector2 = $build_select($gaia_repos, $repo2);

// Check if repo1 is a newer branch than repo2
$reverted_comparison = array_search($repo1, $gaia_repos) < array_search($repo2, $gaia_repos);

// Get strings + status for both channel
$status = [];
$strings = [];

foreach ($gaia_repos as $repo) {
    if ($repo == $repo1 || $repo == $repo2) {
        $strings[$repo] = $get_repo_strings($locale, $repo);
        $strings[$repo . '-en-US'] = $get_repo_strings('en-US', $repo);
        $status[] = [$repos_nice_names[$repo], count($strings[$repo]), count($strings[$repo . '-en-US'])];
    }
}

$sections = [
    'translation_status'      => 'Translation Status',
    'diverging_strings'       => 'Diverging Strings',
    'translation_consistency' => 'Translation Consistency',
    'changed_english'         => 'String Changes in English',
    'new_strings'             => 'New Strings',
];

?>
<form name="searchform" id="simplesearchform" method="get" action="">
    <fieldset id="main_search">
        <fieldset>
            <label>Locale</label>
            <select name="locale">
            <?=$target_locales_list?>
            </select>
        </fieldset>
        <fieldset>
            <label>Repository 1</label>
            <select name="repo1">
            <?=$channel_selector1?>
            </select>
        </fieldset>
        <fieldset>
            <label>Repository 2</label>
            <select name="repo2">
            <?=$channel_selector2?>
            </select>
        </fieldset>
        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>
<?php

// Overview of string count for the locale
$overview = function($section_title, $columns, $rows, $anchor) {
    // Titles
    $html = '<table id="' . $anchor . '">'
       . '<tr>'
       . '<th colspan="3">' . $section_title . '</th>'
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
            $html .= '<td>' . $value . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';

    return $html;
};

$anchor_title = function($name) use ($sections) {
    return "<h3 id='{$name}'><a href='#{$name}'>{$sections[$name]}</a></h3>\n";
};

print "<h2>$locale</h2>\n";
print "<p class='subtitle'>Available views:</p>\n";
print "<ul id='gaia_views_list'>\n";
foreach ($sections as $anchor_name => $section_title) {
    print "  <li><a href='#{$anchor_name}'>{$section_title}</a></li>\n";
}
print "</ul>\n";

print $anchor_title('translation_status');
print $overview('How many strings are translated?', ['repo', $locale, 'en-US'], $status, 'overview');

// Diverging strings betweet two repositories
$diverging = function ($diverging_sources, $strings, $anchor) use ($locale, $repos_nice_names) {

    foreach ($diverging_sources as $key => $repo_name) {
        $normalized_repo[$repo_name] = array_fill_keys(array_keys($strings[$repo_name . '-en-US']), '');
        $normalized_repo[$repo_name] = array_merge($normalized_repo[$repo_name], $strings[$repo_name]);
    }

    // Intersect
    $common_strings = array_intersect_key($normalized_repo[$diverging_sources[0]], $normalized_repo[$diverging_sources[1]]);

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
    $nb_sources = count($diverging_sources) + 1;
    $width = 100 / $nb_sources;

    $table = '<table id="' . $anchor . '" class="collapsable">'
           . '<tr>'
           . '<th colspan="' . $nb_sources . '">' . count($divergences) . ' diverging translations across repositories</th>'
           . '</tr>'
           . '<tr>';
    $table .= "<th style=\"width:$width%\">Keys</th>";
    foreach ($diverging_sources as $key => $repo_name) {
        $table .= "<th style=\"width:$width%\">" . $repos_nice_names[$repo_name] . '</th>';
    }

    $table .= '</tr>';

    foreach ($divergences as $v) {
        $table .= '<tr>'
                . '<td><span class="celltitle">Key</span><div class="string">' . ShowResults::formatEntity($v) . '</div></td>';
        foreach ($normalized_repo as $repo_name => $repo) {
            $table .= '<td><span class="celltitle">' . $repo_name . '</span><div class="string">' . ShowResults::highlight($normalized_repo[$repo_name][$v], $locale) . '</div></td>';
        }
        $table .= '</tr>';
    }

    $table .= '</table>';

    return $table;
};

print $anchor_title('diverging_strings');
print $diverging(
    [
      $repo1,
      $repo2
     ],
    $strings,
    'diverging'
);

// Inconsistent translations in the first repo (repo1)
$find_duplicates = function ($string_array) {
    $duplicates = [];
    // Ignore case
    $string_array = array_map(
        function ($str) {
            // Ignore date modifiers like %b, %B
            $matches = preg_grep('/%[a-z]{1}/i', [$str]);
            if (! count($matches)) {
                $str = mb_strtolower($str, 'UTF-8');
            }

            return $str;
        },
        $string_array
    );
    asort($string_array);
    reset($string_array);

    $previous_key = '';
    $previous_value = '';
    foreach ($string_array as $key => $value) {
        if (strcasecmp($previous_value, $value) === 0) {
            $duplicates[$previous_key] = $previous_value;
            $duplicates[$key] = $value;
        }
        $previous_value = $value;
        $previous_key = $key;
    }
    return $duplicates;
};

$duplicated_strings_english = $find_duplicates($strings[$repo1 . '-en-US']);
$duplicated_strings_translation = $find_duplicates($strings[$repo1]);

// Get the strings duplicated in English but not in the localization
$missing_duplicates = array_diff_key($duplicated_strings_english, $duplicated_strings_translation);

$inconsistent_translation = [];
foreach ($duplicated_strings_english as $key => $value) {
    /*
    I'm interested only in strings with a value that should be duplicated.
    1) Ignore plural forms containing [] in the key, too many false positives
       since English doesn't have plural for adjectives.
    2) Ignore single character strings.
    3) Ignore strings in accessibility.properties. It contains both normal and
       abbreviated strings, which are often identical in English, so there are
       too many false positives.
    */
    if (in_array($value, $missing_duplicates) &&
        strpos($key, '[') === false &&
        strpos($key, ']') === false &&
        strlen($value) > 1 &&
        strpos($key, 'accessibility.properties') === false) {
        $inconsistent_translation[$key]['en-US'] = $value;
        if (array_key_exists($key, $strings[$repo1])) {
            $inconsistent_translation[$key]['l10n'] = $strings[$repo1][$key];
        } else {
            $inconsistent_translation[$key]['l10n'] = '';
        }
    }
}

if (count($inconsistent_translation) > 0) {
    $inconsistent_results = "\n<table><tr><th>Label</th><th>English</th><th>Translation</th></tr>";
    foreach ($inconsistent_translation as $key => $value) {
        $inconsistent_results .= "<tr>
            <td>" . ShowResults::formatEntity($key) . "</td>
            <td>" . Utils::secureText($value['en-US']) . "</td>
            <td>" . Utils::secureText($value['l10n']) . "</td>
        </tr>\n";
    }
    $inconsistent_results .= "</table>\n";
} else {
    $inconsistent_results = "<p>No inconsistent translations found.</p>";
}


print $anchor_title('translation_consistency');
print "<p class='subtitle'>Analysis of translation consistency in {$repos_nice_names[$repo1]}.</p>";
print $inconsistent_results;

// Changes in en-US
$englishchanges = [$repo1, $repo2];

$common_keys = array_intersect_key($strings[$englishchanges[0] . '-en-US'], $strings[$englishchanges[1] . '-en-US']);

$repo_one = $repos_nice_names[$englishchanges[0]];
$repo_two = $repos_nice_names[$englishchanges[1]];
$table = '<table id="englishchanges" class="collapsable">'
       . '<tr>'
       . '<th colspan="3">Strings that have changed significantly in English between ' . $repo_one . ' and ' . $repo_two . ' but for which the entity name didn\'t change</th>'
       . '</tr>'
       . '<tr>'
       . '<th>Key</th>'
       . '<th>' . $repo_one . '</th>'
       . '<th>' . $repo_two . '</th>'
       . '</tr>';

foreach($common_keys as $key =>$val) {
    if (trim(strtolower($strings[$englishchanges[0] . '-en-US'][$key])) != trim(strtolower($strings[$englishchanges[1] . '-en-US'][$key]))) {
            $table .=
              '<tr>'
            . '<td><span class="celltitle">Key</span><div class="string">' . ShowResults::formatEntity($key) . '</div></td>'
            . '<td><span class="celltitle">Gaia' . $repo_one . '</span><div class="string">'
            . ShowResults::highlight(Utils::secureText($strings[$englishchanges[0] . '-en-US'][$key]), 'en-US')
            . '<br><small>' . Utils::secureText($strings[$englishchanges[0]][$key]) . '</small></div></td>'
            . '<td><span class="celltitle">Gaia' . $repo_two . '</span><div class="string">'
            . ShowResults::highlight(Utils::secureText($strings[$englishchanges[1] . '-en-US'][$key]), 'en-US')
            . '<br><small>' . Utils::secureText($strings[$englishchanges[1]][$key]) . '</small></div></td>'
            . '</tr>';
    }
}
$table .= '</table>';

print $anchor_title('changed_english');
print $table;

// String diff between two repositories
$strings_added = function($reverted_comparison, $strings, $repo_one, $repo_two, $anchor, $cssclass) use ($locale, $repos_nice_names) {
    $temp = array_diff_key($strings[$repo_one . '-en-US'], $strings[$repo_two . '-en-US']);
    $count = count($temp);
    if ($reverted_comparison) {
        $comparison_type = '<span class="added_string">' . $count . ' new strings</span>';
    } else {
        $comparison_type = '<span class="deleted_string">' . $count . ' deleted strings</span>';
    }

    $table = '<table id="' . $anchor . '" class="' . $cssclass . '">'
           . '<tr>'
           . '<th colspan="3">' . $comparison_type . ' between ' . $repos_nice_names[$repo_one] . ' and ' . $repos_nice_names[$repo_two] . '</th>'
           . '</tr>'
           . '<tr>'
           . '<th>Key</th>'
           . '<th>New strings</th>'
           . '</tr>';

    foreach ($temp as $k => $v) {
        $translation = array_key_exists($k, $strings[$repo_one])
                        ? $strings[$repo_one][$k]
                        : '<b>String untranslated</b>';

        $table .= '<tr>'
                . '<td><span class="celltitle">Key</span><div class="string">' . ShowResults::formatEntity($k) . '</td>'
                . '<td><span class="celltitle">' . $locale . '</span><div class="string">'
                . ShowResults::highlight(Utils::secureText($strings[$repo_one. '-en-US'][$k]), 'en-US')
                . '<br><small>' . ShowResults::highlight(Utils::secureText($translation), $locale)
                . '</small></div></td>'
                . '</tr>';
    }

    $table .= '</table>';

    return $table;
};

print $anchor_title('new_strings');
print $strings_added(
    $reverted_comparison,
    $strings,
    $repo1,
    $repo2,
    'newstrings',
    'collapsable'
);
