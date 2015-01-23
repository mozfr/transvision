$(document).ready(function() {
    // Make sure the menu is not displayed.
    $('#links-top').hide();

    // Associate code to link to hide/display top menu.
    $('.menu-button').click(function(e) {
      e.preventDefault();
      $('#links-top').slideToggle(400, function(){
        if ($('#links-top').is(':visible')) {
          $('#links-top-button').attr('title', 'Hide Transvision Menu');
          $('#links-top-button').css('background-position', '0 -38px');
        } else {
          $('#links-top-button').attr('title', 'Display Transvision Menu');
          $('#links-top-button').css('background-position', '0 0');
        }
      });
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

    // Focus on the main search field.
    $('#recherche').focus();
});
