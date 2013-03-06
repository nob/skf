jQuery(document).ready( function ( e ) {
	jQuery( '.widget_woo_slider .flexslider' ).each( function ( i, e ) {
		
		//Set navigation display
		var show_nav = true;
		if( jQuery( this ).find( '.hide_nav' ).length ) {
			show_nav = false;
		}

		//Set animation effect
		var effect = 'slide';
		if( jQuery( this ).find( '.effect_fade' ).length ) {
			effect = 'fade';
		}

		//Set auto-start and slide interval options
		var autoslide = jQuery( this ).find( '.autoslide' ).val();
		var autostart = false;
		var slidespeed = false;
		if( autoslide > 0 ) {
			autostart = true;
			slidespeed = ( autoslide * 1000 );
		}
		jQuery( this ).find( '.autoslide' ).remove();

		//Create slider
		jQuery( this ).flexslider({
			controlNav: false,
			directionNav: show_nav,
			animation: effect, 
			smoothHeight: true,
			slideshow: autostart,
			slideshowSpeed: slidespeed
		});
	});
});