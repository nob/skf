jQuery( document ).ready( function () {
	/**
	 * Refresh transient data.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	 jQuery( '.button.songkick-refresh' ).click( function ( e ) {
	 	var ajaxLoaderIcon = jQuery( this ).parent().find( '.ajax-loading' );
	 	ajaxLoaderIcon.css( 'visibility', 'visible' ).fadeTo( 'slow', 1, function () {
	 		// Perform the AJAX call.	
			jQuery.post(
				ajaxurl, 
				{ 
					action : 'woo_songkick_refresh', 
					songkick_refresh_nonce : woo_localized_data.songkick_refresh_nonce
				},
				function( response ) {	
					ajaxLoaderIcon.fadeTo( 'slow', 0, function () {
						jQuery( this ).css( 'visibility', 'hidden' );
					});
				}	
			);
	 	});
	 	
	 	return false;
	 });
});