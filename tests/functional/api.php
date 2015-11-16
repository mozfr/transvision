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
    ['', 400, '{"error":"No service requested"}'],
    ['v1/', 400, '{"error":"Not enough parameters for this query."}'],
    ['v1/entity/central/?id=browser/chrome/browser/syncQuota.properties:collection.bookmarks.label', 200, '{"en-US":"Bookmarks","fr":"Marque-pages"}'],
    ['v1/entity/central/?id=browser/chrome/browser/IdontExist', 400, '{"error":"Entity not available"}'],
    ['v1/search/strings/central/en-US/fr/New%2Bbookmarks/', 200, '{"browser\/chrome\/browser\/places\/bookmarkProperties.properties:dialogTitleAddMulti":{"New Bookmarks":"Nouveaux marque-pages"}}'],
    ['v1/search/strings/central/en-US/fr/tralala/', 200, '[]'],
    ['v1/locales/central/', 200, '["ar","ast","cs","de","en-GB","en-US","eo","es-AR","es-CL","es-ES","es-MX","fa","fr","fy-NL","gl","he","hu","id","it","ja","ja-JP-mac","kk","ko","lt","lv","nb-NO","nl","nn-NO","pl","pt-BR","pt-PT","ru","sk","sl","sv-SE","th","tr","uk","vi","zh-CN","zh-TW"]'],
    ['v1/locales/iDontExist/', 400, '{"error":"The repo queried (iDontExist) doesn\'t exist."}'],
    ['v1/repositories/', 200, '["release","beta","aurora","central","firefox_ios","gaia_2_0","gaia_2_1","gaia_2_2","gaia_2_5","gaia","mozilla_org"]'],
    ['v1/repositories/', 200, '["release","beta","aurora","central","firefox_ios","gaia_2_0","gaia_2_1","gaia_2_2","gaia_2_5","gaia","mozilla_org"]'],
    ['v1/repositories/fr/', 200, '["aurora","beta","central","firefox_ios","gaia","gaia_2_0","gaia_2_1","gaia_2_2","gaia_2_5","mozilla_org","release"]'],
    ['v1/tm/central/en-US/fr/Bookmark/?max_results=3&min_quality=80', 200, '[{"source":"Bookmark","target":"Marquer cette page","quality":100},{"source":"Bookmark","target":"Marque-page","quality":100},{"source":"Bookmarks","target":"Marque-pages","quality":88.888888888889}]'],
    ['v1/tm/global/fr/en-US/Ouvrir/', 200, '[{"source":"Ouvrir dans le Finder","target":"Find in Finder","quality":28.571428571429},{"source":"D\u00e9couvrez comment ouvrir une fen\u00eatre de navigation priv\u00e9e","target":"Learn how to open a private window","quality":8.7719298245614}]'],
    ['v1/versions/', 200, '{"v1":"stable"}'],
];

$obj = new \pchevrel\Verif('Check API HTTP responses');
$obj
    ->setHost('localhost:8082')
    ->setPathPrefix('api/');

$check = function ($object, $paths) {
    foreach ($paths as $values) {
        list($path, $http_code, $content) = $values;
        $object
            ->setPath($path)
            ->fetchContent()
            ->hasResponseCode($http_code)
            ->isJson()
            ->isEqualTo($content);
    }
};

$check($obj, $paths);

$obj->report();

// Kill PHP dev server by killing all children processes of the bash process we opened in the background
exec('pkill -P ' . $pid);
die($obj->returnStatus());
