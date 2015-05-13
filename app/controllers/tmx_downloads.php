<?php
namespace Transvision;

$locales_list = [];

foreach (Project::getRepositories() as $repo) {
    $locales_list = array_merge($locales_list, Project::getRepositoryLocales($repo));
}

// Clean up table to remove duplicate and sort by locale name
$locales_list = array_unique($locales_list);
sort($locales_list);

// Include TMX Options
require_once INC . 'tmx_options.php';

if (isset($_GET['locale'])) {
    $locale = Utils::getOrSet($locales_list, $_GET['locale'], 'en-US');
    include MODELS . 'tmx_downloading.php';
    include VIEWS . 'tmx_downloading.php';
} else {
    $javascript_include = ['select_all.js'];
    $l10n = new \tinyl10n\ChooseLocale($locales_list);
    $locale = $l10n->getCompatibleLocale();
    include VIEWS . 'tmx_downloads.php';
}
