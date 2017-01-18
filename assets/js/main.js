$(document).ready(function() {
        $("a.merge").on("click", function(e) {
                var id = prompt("Enter the ID of the ticket to merge this ticket into");
                if (id == null || id == '' || !jQuery.isNumeric(id)) {
                        e.preventDefault();
                        return;
                }
                $(this).attr("href", $(this).attr("href") + "&mergeId=" + id);
        });
});

