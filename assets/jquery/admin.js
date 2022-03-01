/*
 * Tabs
 */
jQuery(
	function($) {
		'use strict';

		$( '#tabs ul li a' ).on(
			'keyup click',
			function(e) {
				if (e.key === 'Enter' || e.type === 'click') {
					var id = $( this ).attr( 'href' );
					$( '.azrcrv-ui-state-active' ).removeClass( 'azrcrv-ui-state-active' ).attr( 'aria-selected', 'false' ).attr( 'aria-expanded', 'false' );
					$( this ).parent( 'li' ).addClass( 'azrcrv-ui-state-active' ).attr( 'aria-selected', 'true' ).attr( 'aria-expanded', 'true' );
					$( this ).closest( 'ul' ).siblings().addClass( 'azrcrv-ui-tabs-hidden' ).attr( 'aria-hidden', 'true' );
					$( id ).removeClass( 'azrcrv-ui-tabs-hidden' ).attr( 'aria-hidden', 'false' );
					e.preventDefault();
				}
			}
		);

		$( '#tabs ul li a' ).hover(
			function() { $( this ).addClass( 'azrcrv-ui-state-hover' ); },
			function() { $( this ).removeClass( 'azrcrv-ui-state-hover' ); }
		);
	}
);

/*
 * Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
 */
jQuery( document ).ready(
	function($){
		// remove standard avatar display
		// jQuery('table.form-table tr.user-profile-picture').remove();

		// Uploading files
		var file_frame;

		$( '#azrcrv-a-upload-avatar' ).on(
			'click',
			function( event ){

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					file_frame.open();
					return;
				}

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media(
					{
						title: $( this ).data( 'uploader_title' ),
						button: {
							text: $( this ).data( 'uploader_button_text' ),
						},
						multiple: false  // Set to true to allow multiple files to be selected
					}
				);

				// When an image is selected, run a callback.
				file_frame.on(
					'select',
					function() {
						// We set multiple to false so only get one image from the uploader
						attachment = file_frame.state().get( 'selection' ).first().toJSON();

						// Do something with attachment.id and/or attachment.url here
						jQuery( '#custom-default-avatar' ).attr( 'value',attachment.url );
						jQuery( '#custom-default-avatar-src' ).attr( 'src',attachment.url );
					}
				);

				// Finally, open the modal
				file_frame.open();
			}
		);
		$( '#azrcrv-a-remove-avatar' ).on(
			'click',
			function( event ){

				// remove image and url
				jQuery( '#custom-default-avatar' ).attr( 'value','' );
				jQuery( '#custom-default-avatar-src' ).attr( 'src','' );

			}
		);
	}
);
