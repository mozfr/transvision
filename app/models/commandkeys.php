<?php
namespace Transvision;

$error_messages = [];
$reference_locale = Project::getReferenceLocale($repo);
$supported_locales = Project::getRepositoryLocales($repo, [$reference_locale]);
// If the requested locale is not available, fall back to the first
if (! in_array($locale, $supported_locales)) {
    $locale = array_shift($supported_locales);
}
// Build the target locale switcher
$target_locales_list = Utils::getHtmlSelectOptions($supported_locales, $locale);

/*
    Only use desktop repositories. If the requested repository is not
    available, fall back to the first key.
*/
$channels = array_intersect_key(
    Project::getSupportedRepositories(),
    array_flip($desktop_repos)
);
if (! isset($channels[$repo])) {
    $repo = current(array_keys($channels));
    $error_messages[] = "The selected repository is not supported. Falling back to <em>{$repo}</em>.";
}
$channel_selector = Utils::getHtmlSelectOptions($channels, $repo, true);

// Get strings
$source = Utils::getRepoStrings($reference_locale, $repo);
$target = Utils::getRepoStrings($locale, $repo);

// Get string IDs in target ending with '.key' or '.commandkey'
$commandkey_ids = array_filter(
    array_keys($target),
    function ($entity) {
        return Strings::endsWith($entity, ['.key', '.commandkey']);
    }
);

// Known false positives
$ignored_ids = [
    'browser/chrome/browser/browser.properties:addonPostInstall.okay.key',
    'devtools/client/webconsole.properties:table.key',
    'extensions/irc/chrome/chatzilla.properties:msg.url.key',
];
$ignored_files = [
    'toolkit/chrome/global/charsetMenu.properties',
];

$commandkey_results = [];
/*
    Get keyboard shortcuts different from English (ignoring case), and
    exclude known false positives.
*/
foreach ($commandkey_ids as $commandkey_id) {
    if (in_array($commandkey_id, $ignored_ids)) {
        continue;
    }

    if (Strings::startsWith($commandkey_id, $ignored_files)) {
        continue;
    }

    if (mb_strtoupper($target[$commandkey_id]) != mb_strtoupper($source[$commandkey_id])) {
        $commandkey_results[] = $commandkey_id;
    }
}

// Add component filter
// Build logic to filter components
$components = Project::getComponents(array_flip($commandkey_results));
$filter_block = '';
foreach ($components as $value) {
    $filter_block .= " <a href='#{$value}' id='{$value}' class='filter'>{$value}</a>";
}

// RTL support
$text_direction = RTLSupport::getDirection($locale);
