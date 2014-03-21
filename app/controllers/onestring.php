<?php
namespace Transvision;

require_once MODELS . 'onestring.php';

if (JSON_API) {
    $callback = isset($_GET['callback']) ? $_GET['callback'] : false;
    print Json::output($translations, $callback);
} else {
    include VIEWS . 'onestring.php';
}
