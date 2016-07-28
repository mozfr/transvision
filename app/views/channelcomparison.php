<?php
namespace Transvision;

// Helper anonymous variable to output a formatted table cell
$td = function ($key, $value) {
    return "<td><span class=\"celltitle\">{$key}</span>"
           . "<div class=\"string\">{$value}</div></td>";
};

?>
<form id ="searchform" name="searchform" method="get" action="">
    <fieldset id="main_search">
        <fieldset>
            <label>Locale:</label>
            <div class="select-style">
                <select name='locale'>
                <?=$target_locales_list?>
                </select>
            </div>
        </fieldset>
        <fieldset>
            <label>Channel 1:</label>
            <div class="select-style">
                <select name='chan1'>
                <?=$chan_selector1?>
                </select>
            </div>
        </fieldset>
        <fieldset>
            <label>Channel 2:</label>
            <div class="select-style">
                <select name='chan2'>
                <?=$chan_selector2?>
                </select>
            </div>
        </fieldset>
        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>

<?php if (empty($common_strings)) : ?>
<h3>Comparison is empty</h3>
<p class='subtitle'>There are no string differences for this locale between the <?=$repos_nice_names[$chan1]?> and <?=$repos_nice_names[$chan2]?> channels.</p>

<?php else: ?>
<h3>Locale: <?=$locale?></h3>
<table class='collapsable sortable' id='modified_strings_table'>
    <thead>
        <tr class='column_headers'>
            <th>Key</th>
            <th class='select_header'><a href="#">Select column</a><?=$chan1?></th>
            <th class='select_header'><a href="#">Select column</a><?=$chan2?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($common_strings as $key => $value) : ?>
    <tr>
        <?=$td('Key', ShowResults::formatEntity($key))?>
        <?=$td($chan1, Utils::secureText($value))?>
        <?=$td($chan2, Utils::secureText($strings[$chan2][$key]))?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if (empty($new_strings)): ?>
<h3 id="new_strings">No new strings have been added</h3>

<?php else : ?>
<h3 id="new_strings">New strings added in <em><?=$locale?></em> between <?=$repos_nice_names[$chan1]?> and <?=$repos_nice_names[$chan2]?></h3>
<table class='collapsable sortable' id='new_strings_table'>
    <thead>
        <tr class='column_headers'>
            <th>Entity</th>
            <th class='select_header'><a href="#">Select column</a>en-US</th>
            <th class='select_header'><a href="#">Select column</a><?=$locale?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($new_strings as $string_id => $string_values): ?>
    <tr>
        <?=$td('Entity', ShowResults::formatEntity($string_id))?>
        <?php if ($new_strings[$string_id]['reference'] != '@N/A@'): ?>
        <?=$td('en-US', Utils::secureText($string_values['reference']))?>
        <?php else: ?>
        <?=$td('en-US', '<em class="error">(not available)</em>')?>
        <?php endif; ?>
        <?=$td($locale, Utils::secureText($string_values['translation']))?>
    </tr>
    <?php endforeach; ?>
    <tbody>
</table>
<?php endif; ?>
