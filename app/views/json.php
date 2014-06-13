<?php
/*
 * This view outputs a json or jsonp representation of search results
 */

namespace Transvision;

// We die here because we never want to send anything more after the Json file
die(Json::output(
    $json,
    isset($_GET['callback']) ? $_GET['callback'] : false
));

