<?php
namespace Transvision;

// Ignore mozilla.org
$repos = array_diff($repos, ['mozilla_org']);
$channel_selector = '';
foreach ($repos as $repo_name) {
    $selected_channel = ($repo_name == $repo) ? ' selected' : '';
    $channel_selector .= "\t<option {$selected_channel} value=\"{$repo_name}\">{$repos_nice_names[$repo_name]}</option>\n";
}

// Filter out empty strings and known exceptions from reference strings
$reference_locale = Project::getReferenceLocale($repo);
$is_desktop_repo = in_array($repo, $desktop_repos);
$filter_strings = function ($value, $id) use ($is_desktop_repo) {
    // Ignore empty strings
    if (! strlen($value)) {
        return false;
    }

    // Exclude some known exceptions from desktop repositories
    if ($is_desktop_repo) {
        $exclusions = [
            '/branding/', '/dataman/', '/fennec-tile-testapp/',
            '/mozmill/', 'region.properties',
        ];
        foreach ($exclusions as $exclusion) {
            if (strpos($id, $exclusion) !== false) {
                return false;
            }
        }
    }

    return true;
};
$reference_strings = array_filter(Utils::getRepoStrings($reference_locale, $repo), $filter_strings, ARRAY_FILTER_USE_BOTH);

// Get supported locales, ignore the reference locale
$supported_locales = Project::getRepositoryLocales($repo);
$supported_locales = array_diff($supported_locales, [$reference_locale]);

// Reference locale count
$string_count = [];
$reference_count = count($reference_strings);

foreach ($supported_locales as $locale) {
    $locale_strings = array_filter(Utils::getRepoStrings($locale, $repo), 'strlen');
    $string_count[$locale] = [
        'total'     => count($locale_strings),
        'missing'   => count(array_diff_key($reference_strings, $locale_strings)),
        'identical' => count(array_intersect_assoc($reference_strings, $locale_strings)),
    ];
    unset($locale_strings);
}
unset($reference_strings);

$table_data = [];
foreach ($string_count as $locale => $stats) {
    $completion = $reference_count - $stats['missing'];

    // Making sure we never divide by zero while computing percentage
    $completion = $reference_count == 0
        ? 0
        : number_format($completion / $reference_count * 100);

    if ($completion >= 99) {
        $confidence = 'Highest';
    } elseif ($completion >= 95) {
        $confidence = 'High';
    } elseif ($completion >= 90) {
        $confidence = 'High';
    } elseif ($completion >= 60) {
        $confidence = 'In progress';
    } elseif ($completion >= 50) {
        $confidence = 'Low';
    } elseif ($completion >= 30) {
        $confidence = 'Very Low';
    } elseif ($completion >= 10) {
        $confidence = 'Barely started';
    } elseif ($completion >= 1) {
        $confidence = 'Just started';
    } else {
        $confidence = 'No localization';
    }

    $table_data[$locale] = [
        'total'      => $stats['total'],
        'missing'    => $stats['missing'],
        'translated' => ($stats['total'] - $stats['identical']),
        'identical'  => $stats['identical'],
        'completion' => $completion,
        'confidence' => $confidence,
    ];
}
