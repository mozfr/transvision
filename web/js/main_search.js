var checkDefault = function(id) {
    /*
        Check if the associated 'default' checkbox needs to be selected.
        If the select is called 'repository', the default checkbox
        is called 'default_repository'.
    */
    var checkbox = $('#default_' + id);
    var currentValue = $('#' + id).val();
    if (currentValue === checkbox.val()) {
        checkbox.prop('checked', true);
    } else {
        checkbox.prop('checked', false);
    }
};

$(document).ready(function() {
    // Show suggestions only for strings and strings+entities
    var checkSuggestions = function() {
        if (typeof $('#recherche').autocomplete() === 'undefined') {
            $('#recherche').autocomplete({
                serviceUrl: function(query){
                    return '/api/v1/suggestions/'
                        + $('#repository').val() + '/'
                        + $('#source_locale').val() + '/'
                        + $('#target_locale').val() + '/'
                        + encodeURIComponent(query) + '/';
                },
                params: {
                    max_results: 10 // eslint-disable-line
                },
                minChars: 2,
                triggerSelectOnValidInput: false,
                transformResult: function(response) {
                    var data = JSON.parse(response);
                    return {
                        suggestions: $.map(data, function(dataItem) {
                            return {
                                value: dataItem,
                                data: dataItem
                            };
                        })
                    };
                },
                onSelect: function() {
                    $('#searchform').submit();
                }
            });
            // Disable autocomplete on focus
            $('#recherche').off('focus.autocomplete');
        }
        if ($('#search_type').val() !== 'entities') {
            $('#recherche').autocomplete().enable();
        } else {
            $('#recherche').autocomplete().disable();
        }
    };
    // Call it once when the page is ready
    checkSuggestions();

    /*
        Change the label below the search field to reflect the value of "Search in".
        Also checks if the default checkbox needs to be selected.
    */
    $('#search_type').on('change', function(){
        var optionLabel = $('#search_type option[value="' + this.value + '"]').text();
        $('#searchcontextvalue').text(optionLabel);
        checkDefault(this.id);
        // Check if suggestions should be disabled or enabled
        checkSuggestions();
    });

    /*
        Some search options are mutually exclusive.
        "Entire string" disables both "Entire Words" and "Each Word", and
        viceversa.
    */
    var updateSearchOptions = function() {
        $('#entire_string').prop('disabled', $('#each_word').prop('checked') || $('#entire_words').prop('checked'));
        $('#entire_words').prop('disabled', $('#entire_string').prop('checked'));
        $('#each_word').prop('disabled', $('#entire_string').prop('checked'));
    };
    // Call it once when the page is ready, since options are set also via getRepository
    updateSearchOptions();
    $('.search_options').on('change', updateSearchOptions);

    // Associate code to repository switch in main search form.
    $('#repository').on('change', function(){
        var repositoryID = this.value;
        // Check if default checkbox needs to be selected.
        checkDefault(this.id);

        // Update all locale selectors.
        $.each($('.mainsearch_locale_selector'), function() {
            // Store locale currently selected
            var currentLocale = this.value;

            // Empty the select.
            $(this).find('option').remove();

            // Rebuild options with locales for new repository.
            var localeSelector = $(this);
            $.each(supportedLocales[repositoryID], function(key, locale) {
                localeSelector.append($('<option>', {value : locale}).text(locale));
            });

            // Try to select the same locale previously selected.
            $('#' + this.id + ' option[value="' + currentLocale + '"]')
                .prop('selected', true);
        });
    });

    /*
        When a locale selector changes, checks if the default checkbox needs
        to be selected.
    */
    $('.mainsearch_locale_selector').on('change', function(){
        checkDefault(this.id);
    });

    // Associate code to default checkbox changes to store a cookie.
    $('.mainsearch_default_checkbox').on('change', function(){
        var today = new Date();
        var expire = new Date();
        var days = 3650; // Default expire: +10 years
        var cookieName = this.id;
        var cookieValue = '';

        if (!$(this).prop('checked')) {
            // If checkbox is not selected, remove cookie setting it as expired.
            days = -1;
        } else {
            // The new locale must be read from the associated select
            var selectName = this.id.replace('default_', '');
            cookieValue = $('#' + selectName).val();
        }

        /*
            Set Cookie to store default value. Use checkbox ID as cookie
            name (e.g. default_repository) and value as content.
        */
        expire.setTime(today.getTime() + 3600000 * 24 * days);
        document.cookie = cookieName + '=' + cookieValue +
                          ';expires=' + expire.toGMTString();
        $(this).val(cookieValue);
    });

    // Switch source and target locales in select boxes
    $('#locale_switch').on('click', function() {
        var sourceLocale = $('#source_locale').val();
        var targetLocale = $('#target_locale').val();
        $('#source_locale').val(targetLocale).change();
        $('#target_locale').val(sourceLocale).change();
    });

    // Hide the clear button if the search field is empty
    if (!$('#recherche').val()) {
        $('#clear_search').hide();
    } else {
        $('#clear_search').show();
    }

    // Show the clear button if search field changes
    $('#recherche').on('input', function() {
        if (!this.value) {
            $('#clear_search').hide();
        } else {
            $('#clear_search').show();
        }
    });

    // Clear the search field when clicking on the X button
    $('#clear_search').on('click', function() {
        $('#recherche').val('').focus();
        $('#recherche').autocomplete().clear();
        $(this).hide();
    });

    // Associate code to search tooltip
    $('#tooltip_search').click(function(e) {
        e.preventDefault();
        if (!$('#tooltip_search_text').is(':visible')) {
            $('#tooltip_search_text').fadeIn(200);
        } else {
            $('#tooltip_search_text').fadeOut(20);
        }
    });

    $('#tooltip_search_close').click(function(e) {
        e.preventDefault();
        $('#tooltip_search_text').fadeOut(20);
    });

    // Initialize clipboard code (copy translation to clipboard)
    var clipboard = new Clipboard('.clipboard');

    clipboard.on('success', function(e) {
        e.clearSelection();
        $('<div class="tooltip clipboard_tooltip">Translation copied</div>').insertAfter(e.trigger);
        $('.clipboard_tooltip').fadeIn(300).delay(1000).fadeOut(100, function() {
            $(this).remove();
        });
    });

    /*
        Jump to filters if there's not an hash already defined, e.g. to
        jump to a specific row.
    */
    if (window.location.hash === '' && $('#filters').length) {
        $('body,html').animate({
            scrollTop: $('#filters').offset().top
        }, 500);
    }
});
