<?php
namespace Transvision;

$cache_id = $repo . $entity . 'alllocales';

if (! $translations = Cache::getKey($cache_id)) {

    $translations = [];

    foreach (Project::getRepositoryLocales($repo) as $locale_code) {

        $strings = Utils::getRepoStrings($locale_code, $repo);

        // We always want to have an en-US locale for the Json API
        if ($repo == 'mozilla_org' && $locale_code == 'en-GB' && isset($strings[$entity])) {
            $translations ['en-US'] = $strings[$entity];
        }

        if (isset($strings[$entity])) {
            $translations[$locale_code] = trim(rtrim($strings[$entity], '{ok}'));
        }

        // Releasing memory in the loop saves 15% memory on the script
        unset($strings);
    }

   Cache::setKey($cache_id, $translations);
}

return $json = $translations;
