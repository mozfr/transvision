$(document).ready(function() {
    $('.transliterate_button').click(function(event) {
        var id = event.target.getAttribute('data-transliterated-id');
        var $transliterated = $('#transliterate_' + id);
        var $normal = $('#string_' + id);
        event.preventDefault();
        $normal.toggle();
        $transliterated.toggle();
        if ($normal.css('display') === 'none') {
            $(this).val('To Cyrillic');
            $('span[data-clipboard-target="#string_' + id + '"]').attr('data-clipboard-target', '#transliterate_' + id);
        } else {
            $(this).val('To Latin');
            $('span[data-clipboard-target="#transliterate_' + id + '"]').attr('data-clipboard-target', '#string_' + id);
        }
    });
});
