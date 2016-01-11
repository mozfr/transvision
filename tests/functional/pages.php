<?php
define('INSTALL_ROOT',  realpath(__DIR__ . '/../../') . '/');

// We always work with UTF8 encoding
mb_internal_encoding('UTF-8');

// Make sure we have a timezone set
date_default_timezone_set('Europe/Paris');

require __DIR__ . '/../../vendor/autoload.php';

// Set an environment variable so that the instance will use content from test files
putenv("AUTOMATED_TESTS=true");

// Launch PHP dev server in the background
chdir(INSTALL_ROOT);
exec('./start.sh -remote > /dev/null 2>&1 & echo $!', $output);

// We will need the pid to kill it, beware, this is the pid of the bash process started with start.sh
$pid = $output[0];

// Pause to let time for the dev server to launch in the background
sleep(3);

$paths = [
    ['channelcomparizon/', 200, 'Compare strings from channel to channel', 'Key'],
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
    ->setHost('localhost:8082')
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
