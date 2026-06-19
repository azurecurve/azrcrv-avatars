/*
 * Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
 */
document.addEventListener( 'DOMContentLoaded', function () {
	'use strict';

	var uploadButton = document.getElementById( 'azrcrv-a-upload-avatar' );
	var removeButton = document.getElementById( 'azrcrv-a-remove-avatar' );
	var avatarImage  = document.getElementById( 'custom-default-avatar-src' );
	var avatarInput  = document.getElementById( 'custom-default-avatar' );

	if ( ! uploadButton || ! removeButton || ! avatarImage || ! avatarInput ) {
		return;
	}

	// Uploading files
	var file_frame;

	uploadButton.addEventListener( 'click', function ( event ) {
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media(
			{
				title: uploadButton.dataset.uploader_title,
				button: {
					text: uploadButton.dataset.uploader_button_text,
				},
				multiple: false  // Set to true to allow multiple files to be selected
			}
		);

		// When an image is selected, run a callback.
		file_frame.on(
			'select',
			function () {
				// We set multiple to false so only get one image from the uploader
				var attachment = file_frame.state().get( 'selection' ).first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				avatarInput.setAttribute( 'value', attachment.url );
				avatarImage.setAttribute( 'src', attachment.url );
			}
		);

		// Finally, open the modal
		file_frame.open();
	} );

	removeButton.addEventListener( 'click', function ( event ) {
		event.preventDefault();

		// remove image and url
		avatarInput.setAttribute( 'value', '' );
		avatarImage.setAttribute( 'src', '' );
	} );
} );
