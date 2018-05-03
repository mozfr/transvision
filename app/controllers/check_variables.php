<?php
namespace Transvision;

// Get requested repo and locale
require_once INC . 'l10n-init.php';

include MODELS . 'check_variables.php';

/*
    Used only for QA reasons: adding &json to the query will shortcut the logic,
    and display the results as JSON instead of loading view and template.
*/
if (isset($_GET['json'])) {
    $json = array_column($var_errors, 'string_id');
    include VIEWS . 'json.php';
    die();
}
include VIEWS . 'check_variables.php';
