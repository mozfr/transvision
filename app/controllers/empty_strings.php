<?php
namespace Transvision;

// Get requested repo and locale
require_once INC . 'l10n-init.php';

include MODELS . 'empty_strings.php';

/*
    Used only for QA reasons: adding &json to the query will shortcut the logic,
    and display the results as JSON instead of loading view and template.
*/
if (isset($_GET['json'])) {
    $json = array_keys($empty_strings);
    include VIEWS . 'json.php';
    die();
}
include VIEWS . 'empty_strings.php';
