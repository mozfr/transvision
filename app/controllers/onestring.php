<?php
namespace Transvision;

$repo = isset($_GET['repo']) && in_array($_GET['repo'], $repos)
        ? $_GET['repo']
        : 'release';

$entity = isset($_GET['entity']) ? $_GET['entity'] : '';
$callback = isset($_GET['callback']) ? '&callback=' . $_GET['callback'] : '';

// Redirect old API call to new official API
if (isset($_GET['json'])) {
    header('Status: 301 Moved Permanently', false, 301);
    header('Location:' . APP_SCHEME . "{$_SERVER['HTTP_HOST']}/api/v1/entity/{$repo}/?id={$entity}{$callback}");
    exit;
}

include MODELS . 'onestring.php';
include VIEWS . 'onestring.php';
