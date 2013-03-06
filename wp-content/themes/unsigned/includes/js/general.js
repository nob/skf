/*-----------------------------------------------------------------------------------*/
/* GENERAL SCRIPTS */
/*-----------------------------------------------------------------------------------*/
jQuery(document).ready(function(){

	// Table alt row styling
	jQuery( '.entry table tr:odd' ).addClass( 'alt-table-row' );
	
	// FitVids - Responsive Videos
	jQuery( ".post, .page, .video, .widget" ).fitVids();
	
	// Add class to parent menu items with JS until WP does this natively
	jQuery("ul.sub-menu").parents().addClass('parent');
		
	// Responsive Navigation (switch top drop down for select)
	jQuery('ul#top-nav').mobileMenu({
		switchWidth: 767,                   //width (in px to switch at)
		topOptionText: 'Select a page',     //first option text
		indentString: '&nbsp;&nbsp;&nbsp;'  //string for indenting nested items
	});
	
});

jQuery(window).load(function() {
	// Fire Uniform js
	jQuery( 'select.orderby, .variations select, input[type=radio]' ).uniform();
	
	if ( jQuery( '.flexslider' ).length ) {
		
		if ( woo_localized_data.slider_speed == 'Off' ) {
			slideshowSpeed = 0;
			slideshow = false;
		} else {
			slideshowSpeed = parseInt( woo_localized_data.slider_speed ) * 1000;
			slideshow = true;
		}
		
		var animationDuration = woo_localized_data.slider_animation_speed * 1000;
		
		var pauseOnHover = false;
		
		if ( woo_localized_data.slider_hover == 'true' ) {
			pauseOnHover = true;
		}
		
		jQuery( '.flexslider' ).flexslider({
			slideshowSpeed: slideshowSpeed, 
			slideshow: slideshow, 
			animationDuration: animationDuration, 
			pauseOnHover: pauseOnHover, 
			before: function ( slider ) {
				jQuery( '.slider-background img.active' ).fadeOut( animationDuration - 100, function () {
					jQuery( this ).removeClass( 'active' ).addClass( 'inactive' );
				});
			}, 
			after: function ( slider ) {
				var slideID = jQuery( '.flexslider .slides > li:eq(' + slider.currentSlide + ') > div' ).attr( 'id' );
				
				jQuery( '.slider-background img.' + slideID ).css( 'display', 'none' ).addClass( 'active' ).removeClass( 'inactive' ).fadeIn( animationDuration - 100 );
			}
		});
	}
});