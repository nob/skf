jQuery(document).ready( function ( e ) {
	var window_width = jQuery( window ).width();
	if( window_width > 768 ) {
		jQuery( '.widget_woo_newsfromblog .flexslider, .widget_woo_relatedposts .flexslider' ).each( function ( i, e ) {
			
			//Set navigation display
			var show_nav = true;
			if( jQuery( this ).find( '.hide_nav' ).length ) {
				show_nav = false;
			}

			//Set auto-start and slide interval options
			var autoslide = jQuery( this ).find( '.autoslide' ).val();
			var autostart = false;
			var slidespeed = false;
			var loop = false;
			if( autoslide > 0 ) {
				autostart = true;
				loop = true;
				slidespeed = ( autoslide * 1000 );
			}
			jQuery( this ).find( '.autoslide' ).remove();

			//Create slider
			jQuery( this ).flexslider({
				directionNav: show_nav,
				controlsContainer: '.flexslider-nav-container',
				controlNav: false,
				slideshow: autostart,
				slideshowSpeed: slidespeed,
				animation: 'slide',
				animationLoop: loop,
				itemWidth: 920,
				minItems: 1,
				maxItems: 1,
				move: 1
			});
		});
	} else {
		jQuery( '.flexslider .slides > li' ).show();
	}
});