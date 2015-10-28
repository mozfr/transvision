$(document).ready(function() {
    $('.filter').click(function(e) {
        e.preventDefault();
        $('#filters a').removeClass('selected');

        var available_searches = [];
        $('.results_table').each(function() {
            var search_id = $(this).attr('class').split(/\s+/).pop();
            available_searches.push(search_id);
        });

        if (e.target.id === 'showall') {
            $('tr').show();
            $('#showall').addClass('selected');
        } else {
            $('tr').hide();
            $('.' + e.target.id).show();
            // Always show column headers
            $('.column_headers').show();
            $('#' + e.target.id).addClass('selected');
        }

        // Update results count
        available_searches.forEach(function(search_id) {
            var class_name;
            class_name = e.target.id === 'showall'
                ? '.' + search_id
                : '.' + search_id + '.' + e.target.id;

            var results_count = $(class_name).length;
            var results_message =
                results_count == 1
                ? '1 result'
                : results_count + ' results';

            $('.results_count_' + search_id).text(results_message);
        })
    });

    // We want URL anchors to also work as filters
    var anchor = location.hash.substring(1);
    if (anchor !== '') {
        $('#' + anchor).click();
    } else {
        $('#filters a').removeClass('selected');
        $('#showall').addClass('selected');
    }
});
