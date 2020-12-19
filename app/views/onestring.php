<?php
namespace Transvision;

// Error management
if (isset($error)) {
    if ($error == 1) {
        print '<p>No entity asked for, goodbye.</p>';
    } elseif ($error == 2) {
        print '<p>Entity does not exist for this repo, goodbye.</p>';
    }

    return;
}

// We have no error, display results
$page_descr = $entity;
?>

<table>
    <tr class="column_headers">
        <th>Locale</th>
        <th>Translation</th>
        <th>&nbsp;</th>
    </tr>
    <?php
    $reference_locale = Project::getReferenceLocale($repo);
    foreach ($translations as $locale => $translation) {
        $rtl_support = RTLSupport::isRTL($locale) ? 'dir="rtl"' : '';
        $search_link = "/?sourcelocale={$reference_locale}&locale={$locale}&repo={$repo}&search_type=entities&recherche={$entity}&entire_string=entire_string";
        echo "<tr id='{$locale}'>\n" .
             "  <th><a href='#{$locale}'>{$locale}</a></th>\n";

        if ($translation === '') {
            echo "  <td><em class='error'>Warning: Empty string</em></td><td></td>\n";
        } else {
            echo "  <td lang='#{$locale}' {$rtl_support} >" . Strings::highlightSpecial(Utils::secureText($translation)) . "</td>\n" .
                 "  <td><a class='onestring_search' href='{$search_link}' title='Search for the entity in this locale'>🔍</a></td>\n";
        }

        echo "</tr>\n";
    }
    ?>
</table>

<?php
// Promote API view
include VIEWS . 'templates/api_promotion.php';
