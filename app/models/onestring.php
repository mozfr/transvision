<?php
namespace Transvision;

$repo = isset($_GET['repo']) && in_array($_GET['repo'], $repos)
        ? $_GET['repo']
        : 'release';

if ($repo == 'mozilla_org') {
    $strings = Utils::getRepoStrings('en-GB', $repo);
} else {
    $strings = Utils::getRepoStrings('en-US', $repo);
}

$entity = isset($_GET['entity']) ? $_GET['entity'] : false;

// Invalid entity, we don't do any calculation and get back to the view
if (! $entity) {
    return $error = 1;
} elseif (!array_key_exists($entity, $strings)) {
    return $error = 2;
}

$cache_id = $repo . $entity . 'alllocales';

if (! $translations = Cache::getKey($cache_id)) {

    if ($repo == 'mozilla_org') {
        // we always want to have an en-US locale for the Json API
        $translations = ['en-US' => $strings[$entity]];
    }

    foreach (Files::getFilenamesInFolder(TMX . $repo . '/', ['ab-CD']) as $locale_code) {

        $strings = Utils::getRepoStrings($locale_code, $repo);

        if (array_key_exists($entity, $strings)) {
            $translations[$locale_code] = $strings[$entity];
        } else {
            $translations[$locale_code] = false;
        }
    }

    Cache::setKey($cache_id, $translations);
}


unset($strings);
