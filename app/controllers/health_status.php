<?php
namespace Transvision;

$locales_list = [];
$javascript_include = ['show_hide_tabs.js'];

foreach (Project::getRepositories() as $repo) {
    $locales_list = array_merge($locales_list, Project::getRepositoryLocales($repo));
}

$ignored_locales = ['es', 'gu-IN', 'ja-JP-mac'];
$locales_list = array_unique(array_diff($locales_list, $ignored_locales));

// Sort by locale name
sort($locales_list);

if (isset($_GET['locale'])) {
    $page_locale = Utils::getOrSet($locales_list, $_GET['locale'], 'en-US');
} else {
    $l10n = new \tinyl10n\ChooseLocale($locales_list);
    $page_locale = $l10n->getCompatibleLocale();
}
include MODELS . 'health_status.php';
include VIEWS . 'health_status.php';
