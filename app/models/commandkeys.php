<?php
namespace Transvision;

use Cache\Cache;

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

$cache_id = $repo . $locale . 'commandkeys';
$commandkey_results = Cache::getKey($cache_id);
if ($commandkey_results === false) {
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

    // Known false positives to ignore
    $ignored_ids = [
        'browser/chrome/browser/browser.properties:addonPostInstall.okay.key',
        'devtools/client/webconsole.properties:table.key',
    ];
    $ignored_files = [
        'extensions/irc/chrome/chatzilla.properties',
        'toolkit/chrome/global/charsetMenu.properties',
    ];

    $commandkey_results = [];
    /*
        Get keyboard shortcuts different from English (ignoring case), and
        exclude known false positives.
    */
    foreach ($commandkey_ids as $commandkey_id) {
        // Ignore specific strings
        if (in_array($commandkey_id, $ignored_ids)) {
            continue;
        }

        // Ignore entire files
        if (Strings::startsWith($commandkey_id, $ignored_files)) {
            continue;
        }

        $target_ak = $target[$commandkey_id];
        $source_ak = $source[$commandkey_id];

        // Special clean up for Fluent
        $file_name = explode(':', $commandkey_id)[0];
        if (Strings::endsWith($file_name, ['.ftl'])) {
            // Remove all spaces for PLATFORM() or escaped values
            if (mb_strpos($target_ak, '{') !== false) {
                $target_ak = trim(preg_replace('/\s+/', '', $target_ak));
            }
            if (mb_strpos($source_ak, '{') !== false) {
                $source_ak = trim(preg_replace('/\s+/', '', $source_ak));
            }
        }

        if (mb_strtoupper($target_ak) != mb_strtoupper($source_ak)) {
            $commandkey_results[] = [
                'id'              => $commandkey_id,
                'source_shortcut' => $source_ak,
                'target_shortcut' => $target_ak,
            ];
        }
    }
    Cache::setKey($cache_id, $commandkey_results);
    unset($source);
    unset($target);
}

// Build components filter
$components = Project::getComponents(array_flip(array_column($commandkey_results, 'id')));
$filter_block = ShowResults::buildComponentsFilter($components);

// RTL support
$text_direction = RTLSupport::getDirection($locale);
