<?php
namespace Transvision;

$cache_id = $repo . $entity . 'alllocales';

if (! $translations = Cache::getKey($cache_id)) {

    $translations = [];

    foreach (Files::getFilenamesInFolder(TMX . $repo . '/', ['ab-CD']) as $locale_code) {

        $strings = Utils::getRepoStrings($locale_code, $repo);

        // We always want to have an en-US locale for the Json API
        if ($repo == 'mozilla_org' && $locale_code == 'en-GB' && isset($strings[$entity])) {
            $translations ['en-US'] = $strings[$entity];
        }

        if (array_key_exists($entity, $strings)) {
            $translations[$locale_code] = trim(rtrim($strings[$entity], '{ok}'));
        }
    }

   Cache::setKey($cache_id, $translations);
}

unset($strings);

return $json = $translations;
