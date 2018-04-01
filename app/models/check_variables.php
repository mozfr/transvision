<?php
namespace Transvision;

use Cache\Cache;

$reference_locale = Project::getReferenceLocale($repo);
$supported_locales = Project::getRepositoryLocales($repo, [$reference_locale]);
// If the requested locale is not available, fall back to the first
if (! in_array($locale, $supported_locales)) {
    $locale = array_shift($supported_locales);
}

// Set up channel selector, ignore mozilla.org
$channels = Project::getSupportedRepositories();
unset($channels['mozilla_org']);
$channel_selector = Utils::getHtmlSelectOptions($channels, $repo, true);

// Build the target locale switcher
$target_locales_list = Utils::getHtmlSelectOptions($supported_locales, $locale);

$cache_id = $repo . $locale . 'variables';
$var_errors = Cache::getKey($cache_id);
if ($var_errors === false) {
    $source = Utils::getRepoStrings($reference_locale, $repo);
    $target = Utils::getRepoStrings($locale, $repo);
    $var_errors = [];

    $source = array_map(['Transvision\AnalyseStrings', 'cleanUpEntities'], $source);
    $target = array_map(['Transvision\AnalyseStrings', 'cleanUpEntities'], $target);

    // We need to ignore some strings because of false positives
    $ignored_strings = [
        'mail/chrome/messenger/aboutRights.dtd:rights.webservices-term4',
        'suite/chrome/branding/aboutRights.dtd:rights.webservices-term4',
        'toolkit/chrome/global/aboutRights.dtd:rights.webservices-term5',
    ];

    $string_ids = AnalyseStrings::differences($source, $target, $repo, $ignored_strings);
    foreach ($string_ids as $string_id) {
        $var_errors[] = [
            'string_id'     => $string_id,
            'source_string' => $source[$string_id],
            'target_string' => $target[$string_id],
        ];
    }
    Cache::setKey($cache_id, $var_errors);
    unset($source);
    unset($target);
}
$error_count = count($var_errors);

// Build components filter
if (in_array($repo, $desktop_repos)) {
    $components = Project::getComponents(array_flip(array_column($var_errors, 'string_id')));
    $filter_block = ShowResults::buildComponentsFilter($components);
}

// RTL support
$direction1 = RTLSupport::getDirection($source_locale);
$direction2 = RTLSupport::getDirection($locale);
