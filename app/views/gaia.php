<?php
namespace Transvision;

// Set $repo to get the correct list of locales for Gaia from l10n-init.php
$repo = 'gaia';
require_once INC . 'l10n-init.php';

// Functions
$get_or_set = function ($arr, $value, $fallback) {
    return isset($_GET[$value]) && in_array($_GET[$value], $arr)
            ? $_GET[$value]
            : $fallback;
};

$get_repo_strings = function ($locale, $repo) {
    return array_filter(Utils::getRepoStrings($locale, $repo), 'strlen');
};

$build_select = function ($array_in, $selected_elm) use ($repos_nice_names) {
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

// Build the target locale & channels switchers
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
    'translation_status' => 'Translation Status',
    'diverging_strings'  => 'Diverging Strings',
    'changed_english'    => 'String Changes in English',
    'new_strings'        => 'New Strings',
];

?>
<form name="searchform" id="simplesearchform" method="get" action="">
    <fieldset id="main_search">
        <fieldset>
            <label>Locale</label>
            <div class="select-style">
                <select name="locale">
                <?=$target_locales_list?>
                </select>
            </div>
        </fieldset>
        <fieldset>
            <label>Repository 1</label>
            <div class="select-style">
                <select name="repo1">
                <?=$channel_selector1?>
                </select>
            </div>
        </fieldset>
        <fieldset>
            <label>Repository 2</label>
            <div class="select-style">
                <select name="repo2">
                <?=$channel_selector2?>
                </select>
            </div>
        </fieldset>
        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>
<?php

// Overview of string count for the locale
$overview = function ($section_description, $columns, $rows, $anchor) {
    // Titles
    $html = "
        <p class='section_description'>{$section_description}</p>
        <table id='{$anchor}'>
          <thead>
            <tr class='column_headers'>";
    foreach ($columns as $key => $value) {
        $html .= '<th>' . $value . '</th>';
    }
    $html .= "</tr>\n</thead>\n<tbody>\n";

    // Rows
    foreach ($rows as $key => $row) {
        $html .= '<tr>';
        foreach ($row as $key => $value) {
            $html .= '<td>' . $value . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= "</tbody>\n</table>\n";

    return $html;
};

$anchor_title = function ($name) use ($sections) {
    return "<h3 id='{$name}'><a href='#{$name}'>{$sections[$name]}</a></h3>\n";
};

print "<h2>$locale</h2>\n";
print "<p class='subtitle'>Available views:</p>\n";
print "<ul id='gaia_views_list'>\n";
foreach ($sections as $anchor_name => $section_title) {
    print "  <li><a href='#{$anchor_name}'>{$section_title}</a></li>\n";
}
// Adding a manual link for consistency (to the specific view)
print "  <li>Translation consistency for <a href='/consistency/?locale={$locale}&repo={$repo1}'>{$repos_nice_names[$repo1]}</a> or <a href='/consistency/?locale={$locale}&repo={$repo2}'>{$repos_nice_names[$repo2]}</a></li>\n";
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

        // Remove blanks strings. Using 'strlen' to avoid filtering out strings set to 0
        $temp = array_filter($temp, 'strlen');

        // Remove duplicates
        $temp = array_unique($temp);

        // If we have a string in one repo or no string, skip the key
        if (count($temp) <= 1) {
            continue;
        }

        $divergences[] = $k;
    }
    $nb_sources = count($diverging_sources) + 1;
    $width = 100 / $nb_sources;

    $section_description = count($divergences) . ' diverging translations across repositories';
    $table = "
        <p class='section_description'>{$section_description}</p>
        <table id='{$anchor}' class='collapsable sortable'>
          <thead>
            <tr class='column_headers'>
              <th style='width: {$width}%;'>Keys</th>";
    foreach ($diverging_sources as $key => $repo_name) {
        $table .= "<th style='width: $width%;'>{$repos_nice_names[$repo_name]}</th>\n";
    }

    $table .= "</tr>\n</thead>\n<tbody>\n";

    foreach ($divergences as $v) {
        $table .= '<tr>'
                . '<td><span class="celltitle">Key</span><div class="string">' . ShowResults::formatEntity($v) . '</div></td>';
        foreach ($normalized_repo as $repo_name => $repo) {
            $table .= '<td><span class="celltitle">' . $repo_name . '</span><div class="string">' . ShowResults::highlight($normalized_repo[$repo_name][$v]) . '</div></td>';
        }
        $table .= '</tr>';
    }

    $table .= "</tbody>\n</table>\n";

    return $table;
};

print $anchor_title('diverging_strings');
print $diverging(
    [
      $repo1,
      $repo2,
     ],
    $strings,
    'diverging'
);

// Changes in en-US
$englishchanges = [$repo1, $repo2];

$common_keys = array_intersect_key($strings[$englishchanges[0] . '-en-US'], $strings[$englishchanges[1] . '-en-US']);

$repo_one = $repos_nice_names[$englishchanges[0]];
$repo_two = $repos_nice_names[$englishchanges[1]];

$section_description = "Strings that have changed significantly in English between {$repo_one} and {$repo_two} but for which the entity name didn’t change";
$table = "
    <p class='section_description'>{$section_description}</p>
    <table id='englishchanges' class='collapsable sortable'>
      <thead>
        <tr class='column_headers'>
          <th>Key</th>
          <th>{$repo_one}</th>
          <th>{$repo_two}</th>
        </tr>
      </thead>
      <tbody>\n";

$changed_strings = 0;
foreach ($common_keys as $key => $val) {
    $get_localized_string = function ($id) use ($key, $strings) {
        // Avoid warnings if the string is not localized

        return isset($strings[$id][$key]) ?
            $strings[$id][$key] :
            '<em class="error">missing string</em>';
    };
    if (trim(strtolower($strings[$englishchanges[0] . '-en-US'][$key])) != trim(strtolower($strings[$englishchanges[1] . '-en-US'][$key]))) {
        $table .=
              '<tr>'
            . '<td><span class="celltitle">Key</span><div class="string">' . ShowResults::formatEntity($key) . '</div></td>'
            . '<td><span class="celltitle">Gaia' . $repo_one . '</span><div class="string">'
            . ShowResults::highlight(Utils::secureText($strings[$englishchanges[0] . '-en-US'][$key]))
            . '<br><small>' . $get_localized_string($englishchanges[0]) . '</small></div></td>'
            . '<td><span class="celltitle">Gaia' . $repo_two . '</span><div class="string">'
            . ShowResults::highlight(Utils::secureText($strings[$englishchanges[1] . '-en-US'][$key]))
            . '<br><small>' . $get_localized_string($englishchanges[1]) . '</small></div></td>'
            . '</tr>';
        $changed_strings++;
    }
}

if ($changed_strings == 0) {
    $table .= "<tr><td colspan='3' class='no_highligth'>There are no changed strings.</td></tr>\n";
}
$table .= "</tbody>\n</table>\n";

print $anchor_title('changed_english');
print $table;

// String diff between two repositories
$strings_added = function ($reverted_comparison, $strings, $repo_one, $repo_two, $anchor, $cssclass) use ($locale, $repos_nice_names) {
    $temp = array_diff_key($strings[$repo_one . '-en-US'], $strings[$repo_two . '-en-US']);
    $count = count($temp);
    if ($reverted_comparison) {
        $comparison_type = '<span class="added_string">' . $count . ' new strings</span>';
    } else {
        $comparison_type = '<span class="deleted_string">' . $count . ' deleted strings</span>';
    }

    $section_description = "{$comparison_type} between {$repos_nice_names[$repo_one]} and {$repos_nice_names[$repo_two]}";
    $table = "
        <p class='section_description'>{$section_description}</p>
        <table id='{$anchor}' class='{$cssclass} sortable'>
          <thead>
            <tr class='column_headers'>
              <th>Key</th>
              <th>New strings</th>
            </tr>
          </thead>
          <tbody>\n";

    foreach ($temp as $k => $v) {
        $translation = array_key_exists($k, $strings[$repo_one])
                        ? $strings[$repo_one][$k]
                        : '<b>String untranslated</b>';

        $table .= '<tr>'
                . '<td><span class="celltitle">Key</span><div class="string">' . ShowResults::formatEntity($k) . '</td>'
                . '<td><span class="celltitle">' . $locale . '</span><div class="string">'
                . ShowResults::highlight(Utils::secureText($strings[$repo_one . '-en-US'][$k]))
                . '<br><small>' . ShowResults::highlight(Utils::secureText($translation))
                . '</small></div></td>'
                . '</tr>';
    }

    $table .= "</tbody>\n</table>\n";

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
