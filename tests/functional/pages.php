<?php

include 'includes/init.php';

$paths = [
    ['accesskeys/', 200, 'Access Keys', 'Congratulations, no errors found.'],
    ['commandkeys/', 200, 'Keyboard Shortcuts', 'Repository'],
    ['consistency/?locale=fr&repo=gecko_strings', 200, 'English String', 'Available Translations'],
    ['consistency/?locale=fr&repo=gecko_strings', 200, 'Translation Consistency', 'Analyze translation consistency across repositories.'],
    ['credits/', 200, 'Transvision 1.0 was created', 'Transvision is a community project under the MozFR umbrella'],
    ['empty-strings/', 200, 'Empty Strings', 'Locale'],
    ['downloads/', 200, 'Select which strings', 'Generate the TMX'],
    ['news/', 200, 'Version 4.0', 'End user visible changes'],
    ['showrepos/?locale=en-GB', 200, 'Check the health status of locales.', 'Health status'],
    ['stats/', 200, 'Repository status overview', 'Status estimate'],
    ['string/?entity=browser/chrome/browser/places/places.properties:bookmarkResultLabel&repo=gecko_strings', 200, 'supportedLocales', 'Marque-page'],
    ['unchanged/', 200, 'Display a list of strings identical', 'Locale'],
    ['unlocalized/', 200, 'Display the list of the most common untranslated words', 'Occurrences'],
    ['unlocalized-all/', 200, 'Click on each checkbox below', 'Word'],
    ['variables/', 200, 'Show potential errors related to', 'no errors found'],
    ['foo/', 400, '404: Page Not Found', 'You can use the menu at the top'],
    ['123/', 400, '404: Page Not Found', 'You can use the menu at the top'],
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
