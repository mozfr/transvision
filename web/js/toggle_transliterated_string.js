$(document).ready(function() {
    $('.transliterate_button').click(function(event) {
        var $transliterated = $('#transliterate_' + event.target.getAttribute('data-transliterated-id'));
        var $normal = $('#string_' + event.target.getAttribute('data-transliterated-id'));
        event.preventDefault();
        var id = event.target.getAttribute('data-transliterated-id');
        $normal.toggle();
        $transliterated.toggle();
        if ($normal.css('display') === 'none') {
            $(this).val('To Cyrillic');
            $('span[data-clipboard-target="#string_'+id+'"]').attr('data-clipboard-target', '#transliterate_' + id);
        } else {
            $(this).val('To Latin');
            $('span[data-clipboard-target="#transliterate_' + id +'"]').attr('data-clipboard-target', '#string_' + id);
        }
    });
});
