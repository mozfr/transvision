<?php
namespace Transvision;

require_once INC . 'l10n-init.php';

$strings[$repo] = Utils::getRepoStrings($locale, $repo);
$strings_english[$repo] = Utils::getRepoStrings('en-US', $repo);

$channel_selector = Utils::getHtmlSelectOptions(
    array_intersect_key(
        $repos_nice_names,
        array_flip($desktop_repos)
    ),
    $repo,
    true
);

// Get the locale list
$loc_list = Project::getRepositoryLocales($repo);

// Build the target locale switcher
$target_locales_list = Utils::getHtmlSelectOptions($loc_list, $locale);

$akeys = array_filter(
    array_keys($strings[$repo]),
    function ($entity) {
        return substr($entity, -9) == 'accesskey';
    }
);

$ak_labels = ['.label', '.title', '.title2'];
$ak_results = [];

foreach ($akeys as $akey) {
    $entity = substr($akey, 0, -10);
    $akey_value = $strings[$repo][$akey];

    foreach ($ak_labels as $ak_label) {
        if (isset($strings[$repo][$entity . $ak_label])
             && !empty($strings[$repo][$entity . $ak_label])
             && isset($strings_english[$repo][$akey])
             && !empty($strings_english[$repo][$akey])
            ) {
            if ($akey_value == '') {
                $ak_results[$akey] = $entity . $ak_label;
            } elseif (mb_stripos($strings[$repo][$entity . $ak_label], $akey_value) === false) {
                $ak_results[$akey] = $entity . $ak_label;
            } else {
                break;
            }
        }
    }
}
// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

echo '<h2>' . count($ak_results) . ' potential accesskey errors</h2>';
Utils::printSimpleTable(
    $ak_results,
    $strings[$repo],
    ['Label entity', 'Label value', 'Access&nbsp;key', 'Access key entity'],
    'collapsable sortable'
);
