
<form name="searchform" id="simplesearchform" method="get" action="">
    <fieldset id="main_search">

        <?php if (isset($target_locales_list)) : ?>
        <fieldset>
            <label>Locale</label>
            <div class="select-style">
                <select name="locale" title="Locale" id="simplesearch_locale">
                <?=$target_locales_list?>
                </select>
            </div>
        </fieldset>
        <?php endif; ?>

        <?php if (isset($channel_selector)) : ?>
        <fieldset>
            <label>Repository</label>
            <div class="select-style">
                <select name="repo" title="Repository" id="simplesearch_repository">
                <?=$channel_selector?>
                </select>
            </div>
        </fieldset>
        <?php endif; ?>

        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>
