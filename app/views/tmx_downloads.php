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

<script>
$(document).ready(function() {
    $('#select').click(function(event) {
        if(this.textContent == "Select all") {
            this.textContent = "Deselect all";
            $('.checkbox').each(function() {
                this.checked = true;
            });
        }else {
            this.textContent = "Select all";
            $('.checkbox').each(function() {
                this.checked = false;
            });
        }
    });
});

</script>

<form name="tmxform" id="simplesearchform" method="get" action="">
    <fieldset id="main_search">
        <fieldset id="TMX_locales">
            <label>Locale</label>
            <select name="locale">
            <?=$target_locales_list?>
            </select>
        </fieldset>
        <fieldset id="TMX_format">
            <label>Format</label>
            <select
                name="tmx_format"
                id="tmx_format"
                title="Format">
            <?=$tmx_format_list?>
            </select>
        </fieldset>
        <fieldset id="TMX_checkboxes">
            <label>Select which strings to include in the TMX file</label>
            <?=$checkboxes?>
            <button id="select" onclick="return false;">Select all</button>
        </fieldset>
        <input type="submit" id="test" class="button" value="Generate the TMX »" alt="Generate the TMX" onclick="this.value='Generating the TMX…'" />
    </fieldset>
</form>
