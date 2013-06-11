<?php
namespace Transvision;

// Page title
$title = '<a href="/">Transvision</a> TMX Downloads'; 

// Table header
echo '<table id="DownloadsTable">
	<tr>
		<th colspan="6">
			TMX Download Page
		</th>
	</tr>';

// Table content
$loc_list = Utils::getFilenamesInFolder(TMX . 'central/');
echo Utils::tmxDownloadTable($loc_list);

// Close table
echo '</table>';