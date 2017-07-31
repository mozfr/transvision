<?php
namespace Transvision;

$reference_locale = Project::getReferenceLocale($repo);
$supported_locales = Project::getRepositoryLocales($repo, [$reference_locale]);
// If the requested locale is not available, fall back to the first
if (! in_array($locale, $supported_locales)) {
    $locale = array_shift($supported_locales);
}

$source = Utils::getRepoStrings($reference_locale, $repo);
$target = Utils::getRepoStrings($locale, $repo);

// Set up channel selector, ignore mozilla.org
$channels = Project::getSupportedRepositories();
unset($channels['mozilla_org']);
$channel_selector = Utils::getHtmlSelectOptions($channels, $repo, true);

// Build the target locale switcher
$target_locales_list = Utils::getHtmlSelectOptions($supported_locales, $locale);

$source = array_map(['Transvision\AnalyseStrings', 'cleanUpEntities'], $source);
$target = array_map(['Transvision\AnalyseStrings', 'cleanUpEntities'], $target);

// We need to ignore some strings because of false positives
$ignored_strings = [
    'mail/chrome/messenger/aboutRights.dtd:rights.webservices-term4',
    'suite/chrome/branding/aboutRights.dtd:rights.webservices-term4',
    'toolkit/chrome/global/aboutRights.dtd:rights.webservices-term5',
];

$var_errors = AnalyseStrings::differences($source, $target, $repo, $ignored_strings);
$error_count = count($var_errors);

// Add component filter
if (in_array($repo, $desktop_repos)) {
    // Build logic to filter components
    $components = Project::getComponents(array_flip($var_errors));
    $filter_block = '';
    foreach ($components as $value) {
        $filter_block .= " <a href='#{$value}' id='{$value}' class='filter'>{$value}</a>";
    }
}

// RTL support
$direction1 = RTLSupport::getDirection($source_locale);
$direction2 = RTLSupport::getDirection($locale);
