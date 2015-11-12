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

// Promote API view
include VIEWS . 'templates/api_promotion.php';
?>

<table>
    <tr>
        <th>Locale</th>
        <th>Translation</th>
        <th>&nbsp;</th>
    </tr>
    <?php
    $reference_locale = Project::getReferenceLocale($repo);
    foreach ($translations as $locale => $translation) {
        $rtl_support = RTLSupport::isRTL($locale) ? 'dir="rtl"' : '';
        $search_link = "/?sourcelocale={$reference_locale}&locale={$locale}&repo={$repo}&search_type=entities&recherche={$entity}&perfect_match=perfect_match";
        echo "<tr id='{$locale}''>\n" .
             "  <th><a href='#{$locale}'>{$locale}</a></th>\n" .
             "  <td lang='#{$locale}' {$rtl_support} >" . Utils::secureText($translation) . "</td>\n" .
             "  <td><a class='onestring_search' href='{$search_link}' title='Search for the entity in this locale'>🔍</a></td>\n" .
             "</tr>\n";
    }
    ?>
</table>
