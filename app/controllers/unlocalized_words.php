<?php
namespace Transvision;

// Get requested repo and locale.
require_once INC . 'l10n-init.php';

switch ($page) {
    case 'unlocalized_all':
        // Filter out English variants and ja-JP-mac
        $all_locales = array_filter($all_locales, function($l) {
            return ($l != 'en' && ! Strings::startsWith($l, 'en-'));
        });
        $all_locales = array_diff($all_locales, ['ja-JP-mac']);
        include MODELS . 'unlocalized_words_all.php';
        include VIEWS . 'unlocalized_words_all.php';
        break;
    case 'unlocalized_json':
        $all_locales = [$locale];
        include MODELS . 'unlocalized_words_all.php';
        $json = $unlocalized_words;
        include VIEWS . 'json.php';
        break;
    default:
        $all_locales = [$locale];
        include MODELS . 'unlocalized_words.php';
        include MODELS . 'unlocalized_words_all.php';
        include VIEWS . 'unlocalized_words.php';
        break;
}
