<?php
namespace Transvision;

// Compute Download table content 
$downloadTable = function() {
    $localesList = [];

    foreach (Utils::getFilenamesInFolder(TMX) as $locale) {
        $localesList = array_merge($localesList, Utils::getFilenamesInFolder(TMX . $locale . '/'));
    }

    // Clean up table to remove duplicate and sort by locale name
    $localesList = array_unique($localesList);
    sort($localesList);

    return Utils::tmxDownloadTable($localesList);
};

print $downloadTable();
