<?php
namespace Transvision;

// Page title
$title = '<a href="/">Transvision</a> TMX Downloads';

// Table header
echo '<table id="DownloadsTable">
    <tr>
        <th colspan="6">
            <abbr title="Translation Memory eXchange">TMX</abbr> Download Page
        </th>
    </tr>
    <tr>
        <th></th>
        <th colspan="4">Desktop Software</th>
        <th>Firefox OS</th>
    </tr>
    <tr>
        <th></th>
        <th>Central</th>
        <th>Aurora</th>
        <th>Beta</th>
        <th>Release</th>
        <th>Gaia</th>
    </tr>
    ';

// Table content
$loc_list = Utils::getFilenamesInFolder(TMX . 'central/');
echo Utils::tmxDownloadTable($loc_list);

// Close table
echo '</table>';
