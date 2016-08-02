<?php
namespace Transvision;

$chan1 = 'aurora';
$chan2 = 'beta';

if (isset($_GET['chan1']) && in_array($_GET['chan1'], $desktop_repos)) {
    $chan1 = $_GET['chan1'];
}

if (isset($_GET['chan2']) && in_array($_GET['chan2'], $desktop_repos)) {
    $chan2 = $_GET['chan2'];
}

$strings = [];
$strings[$chan1] = Utils::getRepoStrings($locale, $chan1);
$strings[$chan2] = Utils::getRepoStrings($locale, $chan2);

$chan_selector1 = $chan_selector2 = '';
foreach ($desktop_repos as $repo_name) {
    $ch1 = ($repo_name == $chan1) ? ' selected' : '';
    $ch2 = ($repo_name == $chan2) ? ' selected' : '';
    $chan_selector1 .= "\t<option" . $ch1 . " value=" . $repo_name . ">" . $repos_nice_names[$repo_name] . "</option>\n";
    $chan_selector2 .= "\t<option" . $ch2 . " value=" . $repo_name . ">" . $repos_nice_names[$repo_name] . "</option>\n";
}

// Get the locale list
$loc_list = Project::getRepositoryLocales($repo);

// Build the target locale switcher
$target_locales_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}

$replacements = [
    ' '            => '<span class="highlight-gray" title="Non breakable space"> </span>', // Nbsp highlight
    ' '            => '<span class="highlight-red" title="Thin space"> </span>', // Thin space highlight
    '…'            => '<span class="highlight-gray">…</span>', // Right ellipsis highlight
    '&hellip;'     => '<span class="highlight-gray">…</span>', // Right ellipsis highlight
];

/*
    Get locale strings available in both channels with array_intersect_key.
    The translation stored is the one available in channel 1. Then remove
    strings that are identical in both channels using array_diff.
*/
$common_strings = array_intersect_key($strings[$chan1], $strings[$chan2]);
$common_strings = array_diff($common_strings, $strings[$chan2]);

/*
    Find new locale strings added between repositories, store them with the
    reference string and hightlight special characters.
*/
$added_strings = array_diff_key($strings[$chan1], $strings[$chan2]);
$new_strings = [];
$en_US_strings_chan1 = Utils::getRepoStrings('en-US', $chan1);

foreach ($added_strings as $string_id => $translation) {
    $reference_string = isset($en_US_strings_chan1[$string_id])
        ? $en_US_strings_chan1[$string_id]
        : '@@missing@@';

    $new_strings[$string_id] = [
        'reference'   => Utils::secureText($reference_string),
        'translation' => Strings::multipleStringReplace(
                            $replacements,
                            Utils::secureText($translation)
                        ),
    ];
}

// Hightlight special characters in common strings
foreach ($common_strings as $key => &$value) {
    $common_strings[$key] = Strings::multipleStringReplace(
        $replacements,
        Utils::secureText($value)
    );

    $strings[$chan2][$key] = Strings::multipleStringReplace(
        $replacements,
        Utils::secureText($strings[$chan2][$key])
    );
}
