<?php
namespace Transvision;

$missing_repos = $available_repos = $results = '';
$missing_repos_count = $repos_count = 0;
$strings = ['en-US' => [], $locale => []];

$requested_repositories = '';
foreach ($repos as $repo) {
    if (isset($_GET[$repo])) {
        $requested_repositories .= $repo;
        $cache_file_english = Utils::getRepoStrings(Project::getReferenceLocale($repo), $repo);

        if ($cache_file_english) {
            $cache_file_locale = Utils::getRepoStrings($locale, $repo);

            // If a repo is missing, we don't have additional keys
            if ($cache_file_locale) {
                $strings['en-US'] = array_merge($strings['en-US'], $cache_file_english);
                $strings[$locale] = array_merge($strings[$locale], $cache_file_locale);
                $repos_count++;
                $available_repos .= '<br>' . $repos_nice_names[$repo] . ' (' . $locale . ')';
                unset($cache_file_locale);
            } else {
                $missing_repos_count++;
                $missing_repos .= '<br>' . $repos_nice_names[$repo] . ' (' . $locale . ')';
            }

            unset($cache_file_english);
        }
    }
}

// We filter empty values but we keep 0 values with strlen
$strings = [
    'en-US' => array_filter($strings['en-US'], 'strlen'),
    $locale => array_filter($strings[$locale], 'strlen'),
];

$empty_TMX = $repos_count == 0;

// Generate the TMX file
$content = $check['tmx_format'] == 'normal'
    ? TMX::create($strings, $locale, 'en-US')
    : TMX::createOmegat($strings, $locale, 'en-US');

$tmx_id = md5($requested_repositories);
$target_file_name = "mozilla_en-US_{$locale}_{$tmx_id}_{$check['tmx_format']}.tmx";
$target_file_path = WEB_ROOT . "download/{$target_file_name}";

unset($strings);

$empty_TMX = $created_TMX = false;

if ($content) {
    if (! file_put_contents($target_file_path, $content)) {
        $logger->error('Can\'t write into web/download folder');
    }
} else {
    $empty_TMX = true;
}
