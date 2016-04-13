<?php
namespace Transvision;

?>
<p>You might be interested in high values to validate your translation choices and in low values to check for potential mistakes.</p>
<?php
// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

?>
<table class='collapsable results_table sortable'>
    <thead>
     <tr class='column_headers'>
       <th>English</th>
       <th>Occurrences</th>
     </tr>
    </thead>
    <tbody>

<?php foreach ($unlocalized_words as $english_term => $locales) :
    $string_count = $locales[$all_locales[0]];
    $link = "/?recherche={$english_term}&repo={$repo}&sourcelocale={$locale}" .
            "&locale={$ref_locale}&search_type=strings&whole_word=whole_word";

    $link_title = $string_count == 1
        ? 'Search for this occurrence'
        : 'Search for these occurrences';
?>
    <tr>
        <td><a href='<?=$link?>' title='<?=$link_title?>'><?=$english_term?></a></td>
        <td><?=$string_count?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php
unset($unlocalized_words);
