jQuery(document).ready(function(){
	// Add rel="lightbox" to image links if the lightbox is enabled.
	if ( jQuery( 'body' ).hasClass( 'has-lightbox' ) && ! jQuery( 'body' ).hasClass( 'portfolio-component' ) ) {
		jQuery( 'a[href$=".jpg"]:not(:has(img.woo-photo-thumb)), a[href$=".jpeg"]:not(:has(img.woo-photo-thumb)), a[href$=".gif"]:not(:has(img.woo-photo-thumb)), a[href$=".png"]:not(:has(img.woo-photo-thumb))' ).each( function () {
			var imageTitle = '';
			if ( jQuery( this ).next().hasClass( 'wp-caption-text' ) ) {
				imageTitle = jQuery( this ).next().text();
			}
			
			jQuery( this ).attr( 'rel', 'lightbox' ).attr( 'title', imageTitle );
		});
	}
	
	// Load prettyPhoto on all anchor tags with the rel="lightbox" attribute.
	jQuery( 'a[rel^="lightbox"]' ).prettyPhoto({ social_tools: false });
});