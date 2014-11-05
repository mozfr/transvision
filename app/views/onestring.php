<?php
namespace Transvision;

// Error management
if (isset($error)) {
    if ($error == 1) {
        print "<p>No entity asked for, goodbye.</p>";
    } elseif ($error == 2) {
        print "<p>Entity does not exist for this repo, goodbye.</p>";
    }

    return;
}

// We have no error, display results
$page_descr = $entity;
?>

<table>
    <tr>
        <th>Locale</th>
        <th>Translation</th>
    </tr>
    <?php
    foreach ($translations as $locale => $translation) {
        $rtl_support = RTLSupport::isRTL($locale) ? 'dir="rtl"' : '';
        echo "<tr id='{$locale}''>\n" .
             "  <th><a href='#{$locale}'>{$locale}</a></th>\n" .
             "  <td lang='#{$locale}' {$rtl_support} >" . Utils::secureText($translation) . "</td>\n" .
             "</tr>\n";
    }
    ?>
</table>
