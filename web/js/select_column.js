function selectColumn(index, tableID) {
    var columnSelector = '#' + tableID + ' tbody > tr > td:nth-child(' + (index + 1) + ')';
    var cells = $(columnSelector);

    // Clear existing selections
    if (window.getSelection) { // all browsers, except IE before version 9
        window.getSelection().removeAllRanges();
    }

    if (document.createRange) {
        cells.each(function(i, cell) {
            var rangeObj = document.createRange();
            rangeObj.selectNodeContents(cell);
            window.getSelection().addRange(rangeObj);
        });

    } else { // Internet Explorer before version 9
        cells.each(function(i, cell) {
            var rangeObj = document.body.createTextRange();
            rangeObj.moveToElementText(cell);
            rangeObj.select();
        });
    }
}

$(document).ready(function() {
    $('.select_header span').click(function() {
        var columnNumber = $(this).parent().index();
        var tableID = $(this).closest('table').attr('id');
        selectColumn(columnNumber, tableID);
    });
});
