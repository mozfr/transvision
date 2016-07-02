<?php
namespace Transvision;

$target_locales_list = $checkboxes = '';

foreach ($locales_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}

foreach ($repos_nice_names as $repo => $name) {
    $checkboxes .= '<p>
                <input type="checkbox"
                       name="' . $repo . '"
                       id="' . $repo . '"
                       class="checkbox"
                       value="' . $repo . '"';
    if (isset($_GET[$repo])) {
        $checkboxes .= ' checked="checked"';
    }
    $checkboxes .= '/>
                <label for="' . $repo . '">' . $name . '</label>
            </p>';
}
?>

<form name="tmxform" id="simplesearchform" method="get" action="">
    <fieldset id="main_search">
        <fieldset id="TMX_locales">
            <label>Locale</label>
            <div class="select-style">
                <select name="locale">
                <?=$target_locales_list?>
                </select>
            </div>
        </fieldset>
        <fieldset id="TMX_format">
            <label>Format</label>
            <div class="select-style">
                <select
                    name="tmx_format"
                    id="tmx_format"
                    title="Format">
                <?=$tmx_format_list?>
                </select>
            </div>
        </fieldset>
        <fieldset id="TMX_checkboxes">
            <label>Select which strings to include in the TMX file</label>
            <?=$checkboxes?>
            <button id="select" onclick="return false;">Select all</button>
        </fieldset>
        <input type="submit" id="test" class="button" value="Generate the TMX »" alt="Generate the TMX" onclick="this.value='Generating the TMX…'" />
    </fieldset>
</form>
