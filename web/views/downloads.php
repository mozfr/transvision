<?php
namespace Transvision;

// Compute Download table content
$download_table = function() {
    $locales_list = array();

    foreach (Utils::getFilenamesInFolder(TMX) as $locale) {
        $locales_list = array_merge($locales_list, Utils::getFilenamesInFolder(TMX . $locale . '/'));
    }

    // Clean up table to remove duplicate and sort by locale name
    $locales_list = array_unique($locales_list);
    sort($locales_list);

    return Utils::tmxDownloadTable($locales_list);
};

print $download_table();
