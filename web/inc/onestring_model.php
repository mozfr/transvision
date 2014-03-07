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
if (!$entity) {
    return $error = 1;
} elseif (!array_key_exists($entity, $strings)) {
    return $error = 2;
}

if ($repo != 'mozilla_org') {
    $translations = ['en-US' => $strings[$entity]];
}

foreach(Files::getFilenamesInFolder(TMX . $repo . '/', ['ab-CD']) as $locale) {
    $strings = Utils::getRepoStrings($locale, $repo);
    if (array_key_exists($entity, $strings)) {
        $translations[$locale] = $strings[$entity];
    } else {
        $translations[$locale] = false;
    }
}
unset($strings);

