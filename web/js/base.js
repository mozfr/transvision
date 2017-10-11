$(document).ready(function() {
    var hideMenuButton = function(transition) {
        $('#links-top').slideToggle(transition, function(){
            if ($('#links-top').is(':visible')) {
                $('#links-top-button').attr('title', 'Hide Transvision Menu');
                $('#links-top-button').css('background-position', '0 -36px');
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
        var currentLocale = $('#simplesearch_locale').val();
        var repositoryID = this.value;

        // Empty the select
        $('#simplesearch_locale')
            .find('option')
            .remove();

        // Rebuild options with locales for new repository.
        $.each(supportedLocales[this.value], function(key, locale) {
            $('#simplesearch_locale')
                .append($('<option>', {value : locale}).text(locale));
        });

        // Hide elements (e.g. filters in Consistency view) if it's not a desktop repository
        var desktopRepositories = ['gecko_strings'];
        if (desktopRepositories.indexOf(repositoryID) === -1) {
            $('.desktop_repo_only').hide();
        } else {
            $('.desktop_repo_only').show();
        }

        // Try to select the same locale previously selected.
        $('#simplesearch_locale option[value="' + currentLocale + '"]')
            .prop('selected', true);
    });

    // When using the simple search form, remove hashes from URL
    $('#simplesearchform').submit(function(e){
        if (window.location.hash !== '') {
            e.preventDefault();
            window.location.href = window.location.pathname + '?' + $(this).serialize();
        }
    });

    // Hides search options on small screens (check if the warning is displayed)
    if ($('.smallscreen_notices').is(':visible')) {
        $('#searchoptions').hide();
    }

    // Focus on the main search field.
    if (window.location.hash === '') {
        $('#recherche').focus();
    }
});
