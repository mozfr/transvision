$(document).ready(function() {
    // Make sure the menu is not displayed
    $('#links-top').hide();

    // Associate code to link to hide/display top menu
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

    // Associate code to toggle search options on small screens
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

    // Focus on the search field
    $('#recherche').focus();
});
