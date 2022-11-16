<?php

include 'includes/init.php';

$paths = [
    ['', 400, '{"error":"No service requested"}'],
    ['v1/', 400, '{"error":"Not enough parameters for this query."}'],
    ['v1/entity/gecko_strings/?id=browser/chrome/browser/syncQuota.properties:collection.bookmarks.label', 200, '{"en-US":"Bookmarks","fr":"Marque-pages"}'],
    ['v1/entity/gecko_strings/?id=browser/chrome/browser/IdontExist', 400, '{"error":"Entity not available"}'],
    ['v1/search/strings/gecko_strings/en-US/fr/New%2Bbookmarks/', 200, '{"browser\/chrome\/browser\/places\/bookmarkProperties.properties:dialogTitleAddMulti":{"New Bookmarks":"Nouveaux marque-pages"}}'],
    ['v1/search/strings/gecko_strings/en-US/fr/tralala/', 200, '[]'],
    ['v1/search/all/gecko_strings/en-US/fr/showMac/', 200, '{"browser\/chrome\/browser\/downloads\/downloads.dtd:cmd.showMac.label":{"Find in Finder":"Ouvrir dans le Finder"}}'],
    ['v1/locales/gecko_strings/', 200, '["en-US","fr","it"]'],
    ['v1/locales/iDontExist/', 400, '{"error":"The repo queried (iDontExist) doesn\'t exist."}'],
    ['v1/repositories/', 200, '["gecko_strings","mozilla_org"]'],
    ['v1/repositories/fr/', 200, '["gecko_strings","mozilla_org"]'],
    ['v1/suggestions/all_projects/en-US/fr/ar/?max_results=2', 200, '["Bookmark","Marque-page"]'],
    ['v1/suggestions/global/en-US/fr/ar/?max_results=2', 200, '["Bookmark","Marque-page"]'],
    ['v1/suggestions/gecko_strings/en-US/fr/ar/?max_results=2', 200, '["Bookmark","Marque-page"]'],
    ['v1/suggestions/gecko_strings/en-US/fr/ar/?max_results=10', 200, '["Bookmark","Bookmarks","New Bookmarks","Bookmark This Page","Marque-page","Marque-pages","Marquer cette page","Nouveaux marque-pages"]'],
    ['v1/suggestions/gecko_strings/en-US/fr/ar/?max_results=0', 200, '["Bookmark","Bookmarks","New Bookmarks","Bookmark This Page","Marque-page","Marque-pages","Marquer cette page","Nouveaux marque-pages"]'],
    ['v1/suggestions/gecko_strings/en-US/fr/ar/', 200, '["Bookmark","Bookmarks","New Bookmarks","Bookmark This Page","Marque-page","Marque-pages","Marquer cette page","Nouveaux marque-pages"]'],
    ['v1/suggestions/gecko_strings/en-US/fr/bookmark/?max_results=2', 200, '["Bookmark","Bookmarks"]'],
    ['v1/tm/gecko_strings/en-US/fr/Bookmark/?max_results=3&min_quality=80', 200, '[{"source":"Bookmark","target":"Marquer cette page","quality":100},{"source":"Bookmark","target":"Marque-page","quality":100},{"source":"Bookmarks","target":"Marque-pages","quality":88.89}]'],
    ['v1/tm/global/fr/en-US/Ouvrir/', 200, '[{"source":"Ouvrir dans le Finder","target":"Find in Finder","quality":28.57}]'],
    ['v1/tm/global/fr/en/Ouvrir/', 200, '[{"source":"D\u00e9couvrez comment ouvrir une fen\u00eatre de navigation priv\u00e9e","target":"Learn how to open a private window","quality":8.77}]'],
    ['v1/transliterate/foo/bar/', 400, '{"error":"Wrong locale code"}'],
    ['v1/transliterate/sr-Cyrl/%D1%81%D1%80%D0%BF%D1%81%D0%BA%D0%B0/', 200, '["srpska"]'],
    ['v1/versions/', 200, '{"v1":"stable"}'],
];

$obj = new \pchevrel\Verif('Check API HTTP responses');
$obj
    ->setHost('localhost:8083')
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
