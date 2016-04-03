$(document).ready(function() {
    $('.filter').click(function(e) {
        e.preventDefault();
        $('#filters a').removeClass('selected');

        var availableSearches = [];
        $('.results_table').each(function() {
            var searchID = $(this).attr('class').split(/\s+/).pop();
            availableSearches.push(searchID);
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
        availableSearches.forEach(function(searchID) {
            var className;
            className = e.target.id === 'showall'
                ? 'tr.' + searchID
                : 'tr.' + searchID + '.' + e.target.id;

            var resultsCount = $(className).length;
            var resultsMessage =
                resultsCount === 1
                ? '1 result'
                : resultsCount + ' results';

            $('.results_count_' + searchID).text(resultsMessage);
        });
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
