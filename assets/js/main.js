$(document).ready(function() {
    $("a.merge").on("click", function(e) {
            var id = prompt("Enter the ID of the ticket to merge this ticket into");
            if (id == null || id == '' || !jQuery.isNumeric(id)) {
                    e.preventDefault();
                    return;
            }
            $(this).attr("href", $(this).attr("href") + "&mergeId=" + id);
    });

	$('[data-toggle="offcanvas"]').click(function () {
		$('.row-offcanvas').toggleClass('active')
	});
});

Dropzone.options.dropzone = { // The camelized version of the ID of the form element

  // The configuration we've talked about above
  autoProcessQueue: false,
  uploadMultiple: true,
  parallelUploads: 100,
  maxFiles: 100,
  clickable : true,

  // The setting up of the dropzone
  init: function() {
    var myDropzone = this;

    // First change the button to actually tell Dropzone to process the queue.
    this.element.querySelector("button[type=submit]").addEventListener("click", function(e) {
      // Make sure that the form isn't actually being sent.
      e.preventDefault();
      e.stopPropagation();

    	console.log("upload");
		if (myDropzone.getQueuedFiles().length > 0) {                        
		   myDropzone.processQueue();  
		} else {                       
		   $("form#dropzone").submit();
		}
    });

    // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
    // of the sending event because uploadMultiple is set to true.
    this.on("sendingmultiple", function() {
      // Gets triggered when the form is actually being sent.
      // Hide the success button or the complete form.
    });
    this.on("successmultiple", function(files, response) {
      // Gets triggered when the files have successfully been sent.
      // Redirect user or notify of success.
    });
    this.on("errormultiple", function(files, response) {
      // Gets triggered when there was an error sending the files.
      // Maybe show form again, and notify user of error
    });
    this.on("addedfile", function() {
      // Show submit button here and/or inform user to click it.
    });

    this.on("complete", function() {
    	console.log("complete");
    });
  }

}