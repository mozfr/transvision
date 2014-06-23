<?php
namespace Transvision;

// Get strings for selected repos
$strings = ['en-US' => [], $locale => []];
$missing_repos = $available_repos = $results = '';
$missing_repos_count = $repos_count = 0;

foreach ($repos as $repo) {
    if (isset($_GET[$repo])) {
        $cache_file_en = Utils::getRepoStrings(Project::getReferenceLocale($repo), $repo);
        $cache_file_locale = Utils::getRepoStrings($locale, $repo);
        if ($cache_file_en) {
            if ($cache_file_locale) {
                // If mozilla_org, remove {ok} tags
                if ($repo == 'mozilla_org') {
                    foreach ($cache_file_locale as $key => $value) {
                        $cache_file_locale[$key] = trim(rtrim($cache_file_locale[$key], '{ok}'));
                    }
                }
                $strings['en-US'] = array_merge($cache_file_en, $strings['en-US']);
                $strings[$locale] = array_merge($cache_file_locale, $strings[$locale]);
                $repos_count++;
                $available_repos .= '<br>' . $repos_nice_names[$repo] . ' (' . $locale . ')';
            } else {
                $missing_repos .= '<br>' . $repos_nice_names[$repo] . ' (' . $locale . ')';
                $missing_repos_count++;
            }
        }
    }
}
$empty_TMX = $repos_count == 0;

// Generate the TMX file
$target_file = 'mozilla_en-US_' . $locale . '.tmx';
$target_file_path = WEB_ROOT . 'download/' . $target_file;

$content = TMX::create($strings, $locale, 'en-US');
unset($strings);
$empty_TMX = $created_TMX = false;

if ($content) {
    if (! file_put_contents($target_file_path, $content)) {
        $logger->error('Can\'t write into web/download folder');
    }
} else {
    $empty_TMX = true;
}
