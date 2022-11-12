<?php
namespace Transvision;

$locales_list = Project::getAllLocales();

// Clean up table to remove duplicate and sort by locale name
$locales_list = array_unique($locales_list);
sort($locales_list);

// Build the tmx format switcher
$check['tmx_format'] = 'normal';
if (isset($_GET['tmx_format'])
    && in_array($_GET['tmx_format'], ['normal', 'omegat'])
    ) {
    $check['tmx_format'] = $_GET['tmx_format'];
}
$tmx_format_descriptions = [
    'normal' => 'Normal',
    'omegat' => 'OmegaT',
];
$tmx_format_list = Utils::getHtmlSelectOptions(
    $tmx_format_descriptions,
    $check['tmx_format'],
    true
);

if (isset($_GET['locale'])) {
    $locale = Utils::getOrSet($locales_list, $_GET['locale'], 'en-US');
    include MODELS . 'tmx_downloading.php';
    include VIEWS . 'tmx_downloading.php';
} else {
    $l10n = new \tinyl10n\ChooseLocale($locales_list);
    $locale = $l10n->getCompatibleLocale();
    include VIEWS . 'tmx_downloads.php';
}
