<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

?>
<p class="page_description">Note that it’s not possible to distinguish between untranslated
    strings and strings left blank on purpose.<br> For this reason the number of <em>Missing</em>
    string reported on this page might not be completely accurate.</p>
<p class="page_description">Click on the column headers to order the table.</p>

<table id="showrepos_table" class="sortable">
  <thead>
    <tr class="column_headers">
        <th>Locale</th>
        <th>Total</th>
        <th>Missing</th>
        <th>Translated</th>
        <th>Identical</th>
        <th>Completion</th>
        <th class="sorttable_nosort">Status estimate</th>
    </tr>
  </thead>
  <tbody>
<?php
    foreach ($table_data as $locale => $stats) {
        $translated = $stats['total'] - $stats['identical'];
        echo "
            <tr id=\"{$locale}\">
              <th>{$locale}</th>
              <td>{$stats['total']}</td>
              <td>{$stats['missing']}</td>
              <td>{$translated}</td>
              <td>{$stats['identical']}</td>
              <td sorttable_customkey=\"{$stats['completion']}\">{$stats['completion']} %</td>
              <td>{$stats['confidence']}</td>
            </tr>\n";
    }
?>
  </tbody>
</table>
