jQuery(document).ready(function($){
	// run some functions
	lesson.mediaUploader();
	lesson.prependAttachment();

	// course error checking

	$('#publish').click(function(e){
		if( $('#teacher-select').val() == -1 ){
			alert('Please select a teacher for this course.');
			e.preventDefault();
		} else if( $('select#course-category').val() == -1 ){
			alert('Please select a course category.');
			e.preventDefault();
		} else if( $('select#course-difficulty').val() == -1 ){
			alert('Please select a course difficulty.');
			e.preventDefault();
		}
	});

});

var lesson = {
	mediaUploader: function(){
		var mediaUploader;

		jQuery('.wpc-media-button').live("click", function(e) {
			buttonId = this.id;
			e.preventDefault();
			// If the uploader object has already been created, reopen the dialog
			  if (mediaUploader) {
			  mediaUploader.open();
			  return;
			}
			// Extend the wp.media object
			mediaUploader = wp.media.frames.file_frame = wp.media({
			  title: 'Choose Attachment',
			  button: {
			  text: 'Choose Attachment'
			}, multiple: false });

			// When a file is selected, grab the URL and set it as the text field's value
			mediaUploader.on('select', function() {
			  attachment = mediaUploader.state().get('selection').first().toJSON();
			  jQuery('#image-url-' + buttonId).val(attachment.url);
			});
			// Open the uploader dialog
			mediaUploader.open();
		});
	},
	prependAttachment: function(){
			jQuery('#wpc-add-section').click(function(){
			var numSections = jQuery('.wpc-add-media-wrapper').length;

			var maxAllowedAttachments = 6;

			if( numSections >= maxAllowedAttachments ){
				alert( 'Maximum Number of Allowed Attachments is ' + maxAllowedAttachments );
				return false;
			}

			var thisNumSection = numSections + 1;
			jQuery('#wpc-add-media-wrapper').prepend( '<div class="wpc-add-media-wrapper"><button style="padding:3px;margin-right:3px;" id="wpc-btn-' + thisNumSection + '" class="wpc-media-button button wp-menu-image dashicons-before dashicons-admin-media"></button><input id="image-url-wpc-btn-' + thisNumSection + '" type="text" name="wpc-lesson-attachments-' + thisNumSection +  '" /></div>' );
			
			jQuery('#wpc-num-sections').val(thisNumSection);
			return false;
		});
	}
}
