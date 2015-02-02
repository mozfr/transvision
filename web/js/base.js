$(document).ready(function() {
    var hideMenuButton = function(transition) {
        $('#links-top').slideToggle(transition, function(){
            if ($('#links-top').is(':visible')) {
                $('#links-top-button').attr('title', 'Hide Transvision Menu');
                $('#links-top-button').css('background-position', '0 -38px');
            } else {
                $('#links-top-button').attr('title', 'Display Transvision Menu');
                $('#links-top-button').css('background-position', '0 0');
            }
        });
    };

    // Associate code to link to hide/display top menu.
    $('#links-top-button').click(function(e) {
      e.preventDefault();
      hideMenuButton(400);
    });

    // Associate code to toggle search options on small screens.
    $('.toggle-searchoptions-link').click(function(e) {
        e.preventDefault();
        $('#searchoptions').slideToggle(400, function(){
            if ($('#searchoptions').is(':visible')) {
                $('.toggle-searchoptions-link').text('⇑ Hide search options ⇑');
            } else {
                $('.toggle-searchoptions-link').text('⇓ Display search options ⇓');
            }
        });
    });

    // Associate code to repository switch in simple search form.
    $('#simplesearch_repository').on('change', function(){
        // Store locale currently selected
        var current_locale = $('#simplesearch_locale').val();

        // Empty the select
        $('#simplesearch_locale')
            .find('option')
            .remove();

        // Rebuild options with locales for new repository.
        $.each(supported_locales[this.value], function(key, locale) {
            $('#simplesearch_locale')
                .append($('<option>', {value : locale})
                .text(locale));
        });

        // Try to select the same locale previously selected.
        $('#simplesearch_locale option[value="' + current_locale + '"]')
            .prop('selected', true);
    });

    // Javascript is enabled, hide the menu without transitions
    hideMenuButton(0);
    $('#noscript-warning').hide();

    // Hides search options on small screens (check if the warning is displayed)
    if ($('.smallscreen_notices').is(':visible')) {
        $('#searchoptions').hide();
    }

    // Focus on the main search field.
    $('#recherche').focus();
});
