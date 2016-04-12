<?php
require_once INC . 'l10n-init.php';

// Include JS lib after $javascript_include gets reset in l10n-init.php.
$javascript_include = ['/js/sorttable.js'];

include MODELS . 'showrepos.php';
include VIEWS . 'showrepos.php';
