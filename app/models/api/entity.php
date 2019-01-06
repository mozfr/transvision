<?php
namespace Transvision;

use Cache\Cache;

$cache_id = $repo . $entity . 'alllocales';

if (! $translations = Cache::getKey($cache_id)) {
    $translations = [];

    foreach (Project::getRepositoryLocales($repo) as $locale_code) {
        $strings = Utils::getRepoStrings($locale_code, $repo);

        if (isset($strings[$entity])) {
            $translations[$locale_code] = $strings[$entity];
        }

        // Releasing memory in the loop saves 15% memory on the script
        unset($strings);
    }

    Cache::setKey($cache_id, $translations);
}

return $translations;
