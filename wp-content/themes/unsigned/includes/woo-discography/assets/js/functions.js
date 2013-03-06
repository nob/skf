jQuery( document ).ready( function () {
	
	/**
	 * Refresh track list.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	 jQuery( '#woo-album a.refresh' ).click( function ( e ) {
	 	jQuery( '.album-tracks' ).fadeTo( 'slow', 0.3, function () {
	 		// Get the existing image IDs.
	 		var currentIDs = [];
	 		var checkboxesObj = jQuery( '#woo-album' ).find( '.album-tracks input[type="checkbox"]' );
	 		
	 		if ( checkboxesObj ) {
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
						action : 'woo_tracks_refresh', 
						meta_box_content_nonce : woo_localized_data.meta_box_content_nonce, 
						post_id : woo_localized_data.post_id, 
						current_ids : currentIDs
					},
					function( response ) {
						jQuery( response ).insertBefore( '#woo-album .album-tracks li:first' );
						ajaxLoaderIcon.fadeTo( 'slow', 0, function () {
							jQuery( this ).css( 'visibility', 'hidden' );
						});
						
						jQuery( '.album-tracks' ).fadeTo( 'slow', 1 );
					}	
				);
			});
	 	});
	 	
	 	return false;
	 });
	
	/**
	 * Select all tracks.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	 jQuery( '#woo-album a.select-all' ).click( function ( e ) {
	 	var checkboxesObj = jQuery( '#woo-album' ).find( '.album-tracks input[type="checkbox"]' );
	 	
	 	if ( checkboxesObj.length ) {
	 		checkboxesObj.each( function ( i ) {
	 			jQuery( this ).attr( 'checked', 'checked' );
	 		});
	 	}
	 	
	 	return false;
	 });
	 
	/**
	 * Deselect all tracks.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	 jQuery( '#woo-album a.clear-all' ).click( function ( e ) {
	 	var checkboxesObj = jQuery( '#woo-album' ).find( '.album-tracks input[type="checkbox"]' );
	 	
	 	if ( checkboxesObj.length ) {
	 		checkboxesObj.each( function ( i ) {
	 			jQuery( this ).removeAttr( 'checked' );
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
				jQuery( '#title' ).attr( 'value', 'New Album' ).prev( 'label' ).css( 'visibility', 'hidden' );
			}
			delayed_autosave();
			jQuery( this ).parents( 'form' ).submit();
		}
	 	return false;
	 });
});