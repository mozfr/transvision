<?php

if (!$valid) {
    die("File can't be called directly");
}

// Force error reporting
error_reporting(E_ALL);

// These values depend on the server
define('APPROOT', '/home/pascalc/transvision');
define('HG',  APPROOT .'/data/hg/');
define('TMX', APPROOT .'/TMX/');

// locale detection
require_once 'classes/ChooseLocale.class.php';
$site = file(APPROOT . '/trunk.txt', FILE_IGNORE_NEW_LINES);
$l10nDetect = new tinyL10n\ChooseLocale($site);
$l10nDetect->setDefaultLocale('fr');
$l10nDetect->mapLonglocales = true;
$detectedLocale = $l10nDetect->getCompatibleLocale();
