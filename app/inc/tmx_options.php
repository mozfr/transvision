<?php
namespace Transvision;

$check['tmx_format'] = 'normal';

if (isset($_GET['tmx_format'])
    && in_array($_GET['tmx_format'], ['normal', 'omegat'])
    ) {
    $check['tmx_format'] = $_GET['tmx_format'];
}

// Build the tmx format switcher
$tmx_format_descriptions = [
    'normal' => 'Normal',
    'omegat' => 'OmegaT',
];

$tmx_format_list = Utils::getHtmlSelectOptions(
    $tmx_format_descriptions,
    $check['tmx_format'],
    true
);
