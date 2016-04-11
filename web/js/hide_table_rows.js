$(document).ready(function() {
    var $chk = $('#grpChkBox input:checkbox');
    var $tbl = $('#words');
    var $tblhead = $('#words th');

    $chk.prop('checked', false);

    $chk.click(function() {
        var colToHide = $tblhead.filter('.' + $(this).attr('name'));
        var index = $(colToHide).index();
        if (colToHide.css('display') === 'none') {
            $tbl.find('tr :nth-child(' + (index + 1) + ')').css('display', 'table-cell');
        } else {
            $tbl.find('tr :nth-child(' + (index + 1) + ')').css('display', 'none');
        }
    });
});
