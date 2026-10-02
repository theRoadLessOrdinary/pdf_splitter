;
$(document).ready(function () {
	$('body').addClass('dropzone');
	let myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
		url: "?cmd=upload", // Set the url
		init: function () {
			this.on("queuecomplete", function (file) {
				// do nothing 
			})
				, this.on("success", function (file, response) {
					$("#iframe").css({ "width": "100%", "height": "800px", "position": "relative", "left": 0 });
					setTimeout(function () {
						$(file.previewElement).fadeOut();
						let grid = $("#grid").val();
						let url = `?grid=${grid}&cmd=split&src=${encodeURIComponent(response.name)}`;
						console.log("41710606 url", url);
						$("#iframe").attr('src', url);
					}, 1000);
				});
		}
		, previewsContainer: "#dropzonePreviews" // Define the container to display the previews
		, clickable: ".clickable" // Define the element that should be used as click trigger to select files.
		, maxFilesize: 5
		, acceptedFiles: '.pdf'
		, dictDefaultMessage: ''
	});
	$(".notclickable").on("click", function(evnt){
		evnt.stopPropagation();
	})
});
