<?php
namespace Transvision;
/*
 * This view outputs a json or jsonp representation of search results
 */

// Log script performance in PHP integrated developement server console
Utils::logScriptPerformances();

// We die here because we never want to send anything more after the Json file
die(Json::output(
    $json,
    isset($_GET['callback']) ? $_GET['callback'] : false
));

