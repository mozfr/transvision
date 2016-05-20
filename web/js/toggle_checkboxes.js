$(document).ready(function() {
    $('#button-toggle').click(function(e) {
        var $locales = $('#toggle-checkboxes');

        e.preventDefault();
        $locales.toggle();
        if ($locales.css('display') === 'none') {
            $(this).val('Show locales filter');
        } else {
            $(this).val('Hide locales filter');
        }
    });
});
