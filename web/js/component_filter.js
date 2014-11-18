$(document).ready(function() {
    $('.filter').click(function(e) {
        e.preventDefault();
        $('#filters a').removeClass('selected');
        if (e.target.id == 'showall') {
            $('tr').show();
            $('#showall').addClass('selected');
        } else {
            $('tr').hide();
            $('.' + e.target.id).show();
            // Always show column headers
            $('.column_headers').show();
            $('#' + e.target.id).addClass('selected');
        }
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
