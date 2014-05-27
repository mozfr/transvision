<?php
namespace Transvision;

$strings = Utils::getRepoStrings(
    Project::getReferenceLocale($repo),
    $repo
);

// Invalid entity, we don't do any calculation and get back to the view
if (! $entity) {
    return $error = 1;
} elseif (! array_key_exists($entity, $strings)) {
    return $error = 2;
}

include MODELS . 'api/entity.php';
