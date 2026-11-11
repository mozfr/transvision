<?php
namespace Transvision;

use Json\Json;

// This view outputs a JSON representation of search results

// Log script performance in PHP integrated developement server console
Utils::logScriptPerformances();

print new Json()->outputContent($json);