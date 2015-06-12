var check_default = function(id) {
    /* Check if the associated 'default' checkbox needs to be selected.
     * If the select is called 'repository', the default checkbox
     * is called 'default_repository'.
     */
    var checkbox = $('#default_' + id);
    var current_value = $('#' + id).val();
    if (current_value === checkbox.val()) {
        checkbox.prop('checked', true);
    } else {
        checkbox.prop('checked', false);
    }
};

$(document).ready(function() {
    // Change the label below the search field to reflect the value of "Search in".
    $('#search_type').on('change', function(){
        var option_label = $('#search_type option[value="' + this.value + '"]').text();
        $('#searchcontextvalue').text(option_label);
    });

    // Associate code to repository switch in main search form.
    $('#repository').on('change', function(){
        var repository_id = this.value;
        // Check if default checkbox needs to be selected.
        check_default(this.id);

        // Update all locale selectors.
        $.each($('.mainsearch_locale_selector'), function(key, locale) {
            // Store locale currently selected
            var current_locale = this.value;

            // Empty the select.
            $(this).find('option').remove();

            // Rebuild options with locales for new repository.
            var locale_selector = $(this);
            $.each(supported_locales[repository_id], function(key, locale) {
                locale_selector.append($('<option>', {value : locale}).text(locale));
            });

            // Try to select the same locale previously selected.
            $('#' + this.id + ' option[value="' + current_locale + '"]')
                .prop('selected', true);
        });
    });

    /* When a locale selector changes, checks if the default checkbox needs
     * to be selected.
     */
    $('.mainsearch_locale_selector').on('change', function(){
        check_default(this.id);
    });

    // Associate code to default checkbox changes to store a cookie.
    $('.mainsearch_default_checkbox').on('change', function(){
        var today = new Date();
        var expire = new Date();
        var days = 3650; // Default expire: +10 years
        var cookie_name = this.id;
        var cookie_value = '';

        if (!$(this).prop('checked')) {
            // If checkbox is not selected, remove cookie setting it as expired.
            days = -1;
        } else {
            // The new locale must be read from the associated select
            var select_name = this.id.replace('default_', '');
            cookie_value = $('#' + select_name).val();
        }

        /* Set Cookie to store default value. Use checkbox ID as cookie
         * name (e.g. default_repository) and value as content.
         */
        expire.setTime(today.getTime() + 3600000 * 24 * days);
        document.cookie = cookie_name + '=' + cookie_value +
                          ';expires=' + expire.toGMTString();
        $(this).val(cookie_value);
    });

    // Switch source and target locales in select boxes
    $('#locale_switch').on('click', function() {
      source_locale = $('#source_locale').val()
      target_locale = $('#target_locale').val()
      $('#source_locale').val(target_locale).change();
      $('#target_locale').val(source_locale).change();
    });
});
