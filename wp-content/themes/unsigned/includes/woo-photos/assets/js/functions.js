jQuery( document ).ready( function () {
	
	woo_photos_hide_checkboxes();
	
	/**
	 * Check/Uncheck the checkboxes when clicking the thumbnail.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	jQuery( '#woo-gallery' ).find( '.gallery-thumbnail' ).live( 'click', function ( e ) {
		var prevCheckBox = jQuery( this ).prev( 'input' );
		if ( prevCheckBox.attr( 'checked' ) ) {
			prevCheckBox.removeAttr( 'checked' );
			jQuery( this ).removeClass( 'selected' ).addClass( 'unselected' );
		} else {
			prevCheckBox.attr( 'checked', 'checked' );
			jQuery( this ).removeClass( 'unselected' ).addClass( 'selected' );
		}
	});
	
	/**
	 * Refresh thumbnail list.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	 jQuery( '#woo-gallery a.refresh' ).click( function ( e ) {
	 	jQuery( '.thumbnails' ).fadeTo( 'slow', 0.3, function () {
	 		// Get the existing image IDs.
	 		var currentIDs = [];
	 		var checkboxesObj = jQuery( '#woo-gallery' ).find( '.thumbnails input[type="checkbox"]' );
	 		
	 		if ( checkboxesObj.length ) {
	 			checkboxesObj.each( function ( i ) {
	 				currentIDs.push( jQuery( checkboxesObj[i] ).val() );
	 			});
	 		}

	 		var ajaxLoaderIcon = jQuery( this ).parent().find( '.ajax-loading' );
	 		ajaxLoaderIcon.css( 'visibility', 'visible' ).fadeTo( 'slow', 1, function () {
		 		// Perform the AJAX call.	
				jQuery.post(
					ajaxurl, 
					{ 
						action : 'woo_photos_refresh', 
						meta_box_content_nonce : woo_localized_data.meta_box_content_nonce, 
						post_id : woo_localized_data.post_id, 
						current_ids : currentIDs
					},
					function( response ) {
						jQuery( response ).insertBefore( '#woo-gallery .thumbnails input:first' );
						woo_photos_hide_checkboxes();
						ajaxLoaderIcon.fadeTo( 'slow', 0, function () {
							jQuery( this ).css( 'visibility', 'hidden' );
						});
						
						jQuery( '.thumbnails' ).fadeTo( 'slow', 1 );
					}	
				);
			});
	 	});
	 	
	 	return false;
	 });
	
	/**
	 * Select all thumbnails.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	 jQuery( '#woo-gallery a.select-all' ).click( function ( e ) {
	 	var checkboxesObj = jQuery( '#woo-gallery' ).find( '.thumbnails input[type="checkbox"]' );
	 	
	 	if ( checkboxesObj.length ) {
	 		checkboxesObj.each( function ( i ) {
	 			jQuery( this ).attr( 'checked', 'checked' );
	 			jQuery( this ).next( 'img' ).addClass( 'selected' );
	 		});
	 	}
	 	
	 	return false;
	 });
	 
	 /**
	 * Deselect all thumbnails.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	 jQuery( '#woo-gallery a.clear-all' ).click( function ( e ) {
	 	var checkboxesObj = jQuery( '#woo-gallery' ).find( '.thumbnails input[type="checkbox"]' );
	 	
	 	if ( checkboxesObj.length ) {
	 		checkboxesObj.each( function ( i ) {
	 			jQuery( this ).removeAttr( 'checked' );
	 			jQuery( this ).next( 'img' ).removeClass( 'selected' );
	 		});
	 	}
	 	
	 	return false;
	 });
	 
	/**
	 * Trigger the autosave.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	 jQuery( '.woo-start-managing' ).click( function ( e ) {
		if ( '1' == jQuery( '#auto_draft' ).val() ) {
			if ( jQuery( '#title' ).val() == '' ) {
				jQuery( '#title' ).attr( 'value', 'New Gallery' ).prev( 'label' ).css( 'visibility', 'hidden' );
			}
			delayed_autosave();
			jQuery( this ).parents( 'form' ).submit();
		}
	 	return false;
	 });
});

/**
 * Hide the checkboxes.
 *
 * @since 1.0.0
 * @access public
 */
function woo_photos_hide_checkboxes() {
	jQuery( '#woo-gallery input[type="checkbox"]' ).addClass( 'hide' );
} // End woo_photos_hide_checkboxes()