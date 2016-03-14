<?php
namespace Transvision;

?>
<form id ="searchform" name="searchform" method="get" action="">
    <fieldset id="main_search">
        <fieldset>
            <label>Locale:</label>
            <select name='locale'>
            <?=$target_locales_list?>
            </select>
        </fieldset>
        <fieldset>
            <label>Channel 1:</label>
            <select name='chan1'>
            <?=$chan_selector1?>
            </select>
        </fieldset>
        <fieldset>
            <label>Channel 2:</label>
            <select name='chan2'>
            <?=$chan_selector2?>
            </select>
        </fieldset>
        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>

<?php if (empty($common_strings)) : ?>
<h3>Comparison is empty</h3>
<p class='subtitle'>There are no string differences for this locale between the <?=$repos_nice_names[$chan1]?> and <?=$repos_nice_names[$chan2]?> channels.</p>

<?php else: ?>
<table class='collapsable'>
    <thead>
        <tr>
            <th colspan='3'>Locale: <?=$locale?></th>
        </tr>
        <tr>
            <th>Key</th>
            <th><?=$chan1?></th>
            <th><?=$chan2?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($common_strings as $key => $value) : ?>
    <tr>
        <td><span class='celltitle'>Key</span><div class='string'><?=ShowResults::formatEntity($key)?></div></td>
        <td><span class='celltitle'><?=$chan1?></span><div class='string'><?=Utils::secureText($value)?></div></td>
        <td><span class='celltitle'><?=$chan2?></span><div class='string'><?=Utils::secureText($strings[$chan2][$key])?></div></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if (empty($new_strings)): ?>
<h3 id="new_strings">No new strings have been added</h3>

<?php else : ?>
<h3 id="new_strings">New added strings in <?=$locale?> between <?=$repos_nice_names[$chan1]?> and <?=$repos_nice_names[$chan2]?></h3>
<table class="collapsable">
    <thead>
        <tr>
            <th>Key</th>
            <th>Value</th>
            <th>en-US value</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($new_strings as $key => $value): ?>
    <tr>
        <td><span class="celltitle">Key</span><div class="string"><?=showResults::formatEntity($key)?></div></td>
        <td><span class="celltitle">Value</span><div class="string"><?=Utils::secureText($value)?></div></td>
        <?php if (isset($new_en_US_strings[$key])): ?>
        <td><span class="celltitle">en-US Value</span><div class="string"><?=Utils::secureText($new_en_US_strings[$key])?></div></td>
        <?php else: ?>
        <td><span class='celltitle'>en-US value</span><div class="string">(not available)</div></td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    <tbody>
</table>
<?php endif; ?>
