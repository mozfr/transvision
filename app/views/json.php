<?php
namespace Transvision;

use Json\Json;

/*
 * This view outputs a JSON or JSONP representation of search results
 */

// Log script performance in PHP integrated developement server console
Utils::logScriptPerformances();

// We die here because we never want to send anything more after the JSON file
$json_data = new Json;
die($json_data->outputContent(
    $json,
    isset($_GET['callback']) ? $_GET['callback'] : false
));
