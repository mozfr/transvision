<?php

include 'includes/init.php';

$paths = [
    ['channelcomparison/', 200, 'Compare strings from channel to channel', 'Key'],
    ['credits/', 200, 'Transvision 1.0 was created', 'Transvision is hosted on the MozFR server'],
    ['downloads/', 200, 'Select which strings', 'Generate the TMX'],
    ['gaia/', 200, 'Translation Status', 'How many strings are translated'],
    ['news/', 200, 'Version 4.0', 'End user visible changes'],
    ['productization/', 200, 'Show productization', 'firefox'],
    ['showrepos/?locale=en-GB', 200, 'Health Status for en-GB', 'General metrics'],
    ['stats/', 200, 'Repository status overview', 'Status estimate'],
    ['string/?entity=browser/chrome/browser/places/places.properties:bookmarkResultLabel&repo=central', 200, 'supported_locales', 'Marque-page'],
    ['unchanged/', 200, 'Display a list of strings identical', 'Locale'],
    ['variables/', 200, 'Show potential errors related to', 'no errors found'],
];

$obj = new \pchevrel\Verif('Check public pages HTTP responses and content');
$obj
    ->setHost('localhost:8083')
    ->setPathPrefix('');

$check = function ($object, $paths) {
    foreach ($paths as $values) {
        list($path, $http_code, $content, $content2) = $values;
        $object
            ->setPath($path)
            ->fetchContent()
            ->hasResponseCode($http_code)
            ->contains($content)
            ->contains($content2);
    }
};

$check($obj, $paths);

$obj->report();

// Kill PHP dev server by killing all children processes of the bash process we opened in the background
exec('pkill -P ' . $pid);
die($obj->returnStatus());
