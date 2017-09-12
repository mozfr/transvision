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

// Get strings with 'accesskey' in the string ID
$ak_string_ids = array_filter(
    array_keys($target),
    function ($entity) {
        return strpos($entity, '.accesskey') !== false;
    }
);

// Possible labels associated to an access key
$ak_labels = ['.label', '.title', '.message', ''];

// Known false positives
$ignored_ids = [
    'suite/chrome/mailnews/messenger.dtd:searchButton.title',
];

$ak_results = [];
foreach ($ak_string_ids as $ak_string_id) {
    foreach ($ak_labels as $ak_label) {
        /*
            Replace 'accesskey' with one of the known IDs used for labels.
            E.g.:
            * foo.accesskey -> foo.label
            * foo.accesskey -> foo.title
            * foo.accesskey -> foo.message
            * foo.accesskey -> foo (common in devtools)
        */
        $entity = str_replace('.accesskey', $ak_label, $ak_string_id);
        $current_ak = $target[$ak_string_id];

        /*
            Ignore:
            * Strings not available or empty in target locale.
            * Empty access keys in source locale.
        */
        if (isset($target[$entity]) && ! empty($target[$entity]) && ! empty($source[$ak_string_id]) ) {
            // Ignore known false positives
            if (in_array($entity, $ignored_ids)) {
                continue;
            }
            /*
                Store the string if the access key is empty or using a
                character not available in the label.
            */
            if ($current_ak == '') {
                $ak_results[$ak_string_id] = $entity;
            } elseif (mb_stripos($target[$entity], $current_ak) === false) {
                $ak_results[$ak_string_id] = $entity;
            }
        }
    }
}

// Add component filter
if (in_array($repo, $desktop_repos)) {
    // Build logic to filter components
    $components = Project::getComponents(array_flip($ak_results));
    $filter_block = '';
    foreach ($components as $value) {
        $filter_block .= " <a href='#{$value}' id='{$value}' class='filter'>{$value}</a>";
    }
}

// RTL support
$direction = RTLSupport::getDirection($locale);
