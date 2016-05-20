$(document).ready(function() {
    var $locales = $('#toggle-checkboxes');
    var $butonToggle = $('#button-toggle');

    $butonToggle.click(function() {
        $locales.toggle();
        if ($locales.css('display') === 'none') {
            $butonToggle.val('Show the locales');
        } else {
            $butonToggle.val('Hide the locales');
        }
    });
});
