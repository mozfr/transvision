<?php
namespace Transvision;

// Check that the class exists before trying to use it
// Requires php-intl
if (! class_exists('Transliterator')) {
    $request->error = 'Class Transliterator not available';
    $json = $request->invalidAPICall(501);

    return;
}

switch ($request->parameters[2]) {
    case 'sr-Cyril':
        $transliterated_locale = 'Serbian-Latin/BGN';
        break;

    default:
    $request->error = 'Wrong language';

    return $request->invalidAPICall(501);
}

$transliterator = \Transliterator::create($transliterated_locale);
$transliterated_string = $transliterator->transliterate($request->parameters[3]);

return $json = [$transliterator->transliterate(Utils::secureText($request->parameters[3]))];
