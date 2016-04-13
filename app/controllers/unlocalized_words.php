<?php
namespace Transvision;

// Get requested repo and locale.
require_once INC . 'l10n-init.php';

include MODELS . 'unlocalized_words.php';

switch ($page) {
    case 'unlocalized_json':
        $json = $unlocalized_words;
        include VIEWS . 'json.php';
        break;
    default:
        include VIEWS . 'unlocalized_words.php';
        break;
}
