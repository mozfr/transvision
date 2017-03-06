<?php
namespace Transvision;

?>
<p>You might be interested in high values to validate your translation choices and in low values to check for potential mistakes.</p>
<?php
// Include the common simple search form
include __DIR__ . '/simplesearchform.php';
?>

<p><input id="button-toggle" type="submit" value="Show locales filter"></p>
<div id="toggle-checkboxes" style="display:none">
<p>Click on each checkbox below to show/hide the corresponding column.</p>
<fieldset id="grpChkBox">
    <?php foreach ($all_locales as $locale) : ?>
    <input type="checkbox" name="<?=$locale?>" id="<?=$locale?>" /><label for="<?=$locale?>"><?=$locale?></label>
    <?php endforeach ?>
</fieldset>
</div>
<table class="collapsable results_table sortable" id="words">
    <thead>
        <tr class="column_headers">
            <th>Word</th>
            <?php foreach ($all_locales as $locale) : ?>
            <th class="<?=$locale?> hide"><?=$locale?></th>
            <?php endforeach ?>
        </tr>
   </thead>
   <tbody>
<?php foreach ($unlocalized_words as $english_term => $locales) : ?>
    <tr><td><?=$english_term?></td><?php
        foreach ($all_locales as $locale) {
            $count = 0;
            if (in_array($locale, array_keys($locales))) {
                $count = $locales[$locale];
            }

            $link = "/?recherche={$english_term}&repo={$repo}&sourcelocale={$locale}" .
                    "&locale={$ref_locale}&search_type=strings&entire_words=entire_words";

            if ($count > 0) {
                print "<td><a href='{$link}'>{$count}</a></td>";
            } else {
                print '<td></td>';
            }
        }
    ?></tr>
<?php endforeach ?>
    </tbody>
</table>
<?php unset($unlocalized_words);?>
