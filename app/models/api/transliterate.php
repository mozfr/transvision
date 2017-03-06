<?php
namespace Transvision;

// Check that the class exists before using it. Requires the php-intl extension
if (! class_exists('Transliterator')) {
    $request->error = 'Class Transliterator not available';

    return $request->invalidAPICall(501);
}

switch ($request->parameters[2]) {
    case 'sr-Cyrl':
        $transliterated_locale = 'Serbian-Latin/BGN';
        break;
    default:
    $request->error = 'Wrong locale code';

    return $request->invalidAPICall();
}

$transliterator = \Transliterator::create($transliterated_locale);

return [$transliterator->transliterate(urldecode($request->parameters[3]))];
