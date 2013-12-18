<?php
namespace Transvision;

$repo = isset($_GET['repo']) && in_array($_GET['repo'], $repos)
		? $_GET['repo']
		: 'release';

$strings = Utils::getRepoStrings('en-US', $repo);
$entity = isset($_GET['entity']) ? $_GET['entity'] : false;

// Invalid entity, we don't do any calculation and get back to the view
if (!$entity) {
	$error = 1;
	return;
} elseif (!array_key_exists($entity, $strings)) {
	$error = 2;
	return;
}

$translations = array('en-US' => $strings[$entity]);
$locales = Utils::getFilenamesInFolder(TMX . $repo . '/');

foreach($locales as $locale) {
	$strings = Utils::getRepoStrings($locale, $repo);
	if (array_key_exists($entity, $strings)) {
		$translations[$locale] = $strings[$entity];
	} else {
		$translations[$locale] = false;
	}
}
unset($strings);

