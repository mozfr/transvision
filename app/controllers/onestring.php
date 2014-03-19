<?php
namespace Transvision;

require_once MODELS . 'onestring.php';

if (JSON_API) {
    $callback = isset($_GET['callback']) ? $_GET['callback'] : false;
    die(Json::output($translations, $callback));
}

include VIEWS . 'onestring.php';
