<?php
require_once INC . 'l10n-init.php';

// Include JS lib after $javascript_include gets reset in l10n-init.php.
$javascript_include = ['/js/select_column.js'];

include MODELS . 'channelcomparison.php';
include VIEWS . 'channelcomparison.php';
