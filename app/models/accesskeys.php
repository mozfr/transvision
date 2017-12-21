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

// Some keys map to a different string ID than the one identified by the
// algorithm
$known_mappings = [
    'browser/chrome/browser/browser.properties:decoder.noCodecs.accesskey'      => 'browser/chrome/browser/browser.properties:decoder.noCodecs.button',
    'extensions/irc/chrome/pref-irc.dtd:pref-irc.open.accesskey'                => 'extensions/irc/chrome/pref-irc.dtd:pref-irc.open.label',
    'browser/chrome/browser/preferences/sync.dtd:engine.tabs.accesskey'         => 'browser/chrome/browser/preferences/sync.dtd:engine.tabs.label2',
    'browser/chrome/browser/preferences/sync.dtd:engine.logins.accesskey'       => 'browser/chrome/browser/preferences/sync.dtd:engine.logins.label',
    'browser/chrome/browser/preferences/sync.dtd:engine.prefs.accesskey'        => 'browser/chrome/browser/preferences/sync.dtd:engine.prefs.label',
    'browser/chrome/browser/preferences/sync.dtd:engine.addons.accesskey'       => 'browser/chrome/browser/preferences/sync.dtd:engine.addons.label',
    'browser/chrome/browser/preferences/sync.dtd:engine.addresses.accesskey'    => 'browser/chrome/browser/preferences/sync.dtd:engine.addresses.label',
    'browser/chrome/browser/preferences/sync.dtd:engine.creditcards.accesskey'  => 'browser/chrome/browser/preferences/sync.dtd:engine.creditcards.label',
];

$ak_results = [];
foreach ($ak_string_ids as $ak_string_id) {
    if (isset($known_mappings[$ak_string_id])) {
        $entity = $known_mappings[$ak_string_id];
        // Check if the label is translated
        if (isset($target[$entity]) && ! empty($target[$entity])) {
            $current_ak = $target[$ak_string_id];
            if (($current_ak == '') || (mb_stripos($target[$entity], $current_ak) === false)) {
                $ak_results[$ak_string_id] = $entity;
            }
        }
    } else {
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
            if (isset($target[$entity]) && ! empty($target[$entity]) && ! empty($source[$ak_string_id])) {
                // Ignore known false positives
                if (in_array($entity, $ignored_ids)) {
                    continue;
                }
                /*
                    Store the string if the access key is empty or using a
                    character not available in the label.
                */
                if (($current_ak == '') || (mb_stripos($target[$entity], $current_ak) === false)) {
                    $ak_results[$ak_string_id] = $entity;
                }
            }
        }
    }
}

// Build components filter
if (in_array($repo, $desktop_repos)) {
    $components = Project::getComponents(array_flip($ak_results));
    $filter_block = ShowResults::buildComponentsFilter($components);
}

// RTL support
$direction = RTLSupport::getDirection($locale);
