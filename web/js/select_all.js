$(document).ready(function() {
    $('#select').click(function(event) {
        if(this.textContent == "Select all") {
            this.textContent = "Deselect all";
            $('.checkbox').each(function() {
                this.checked = true;
            });
        } else {
            this.textContent = "Select all";
            $('.checkbox').each(function() {
                this.checked = false;
            });
        }
    });
});
