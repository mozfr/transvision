
<form name="searchform" id="simplesearchform" method="get" action="">
    <fieldset id="main_search">

        <?php if (isset($target_locales_list)) : ?>
        <fieldset>
            <label>Locale</label>
            <select name="locale" title="Locale">
            <?=$target_locales_list?>
            </select>
        </fieldset>
        <?php endif; ?>

        <?php if (isset($channel_selector)) : ?>
        <fieldset>
            <label>Repository</label>
            <select name="repo" title="Repository">
            <?=$channel_selector?>
            </select>
        </fieldset>
        <?php endif; ?>

        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>
