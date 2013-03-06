jQuery( window ).load( function() {
    if ( woo_localized_data.slideshow == 'true' ) { woo_localized_data.slideshow = true; } else { woo_localized_data.slideshow = false; }
    if ( woo_localized_data.directionNav == 'true' ) { woo_localized_data.directionNav = true; } else { woo_localized_data.directionNav = false; }
    if ( woo_localized_data.controlNav == 'true' ) { woo_localized_data.controlNav = true; } else { woo_localized_data.controlNav = false; }
    if ( woo_localized_data.pauseOnHover == 'true' ) { woo_localized_data.pauseOnHover = true; } else { woo_localized_data.pauseOnHover = false; }

   	jQuery( '#featured' ).flexslider({
     		animation: woo_localized_data.animation,
     		controlsContainer: woo_localized_data.controlsContainer,
        smoothHeight: woo_localized_data.smoothHeight,
     		directionNav: woo_localized_data.directionNav,
   			controlNav: woo_localized_data.controlNav,
   			manualControls: woo_localized_data.manualControls,
     		slideshow: woo_localized_data.slideshow,
     		pauseOnHover: woo_localized_data.pauseOnHover,
     		slideshowSpeed: woo_localized_data.slideshowSpeed,
     		animationDuration: woo_localized_data.animationDuration,
        touch: woo_localized_data.touch,
        pauseOnHover: woo_localized_data.pauseOnHover,
     		start: function( slider ) {
            jQuery( '#featured .slide:eq(0)' ).addClass( 'flex-active-slide' );
            adjust_slide_height();
         		adjust_content_margin( 0 );
      	},
      	before: function( slider ) {
         		/* Check if last slide is being shown */
         		if(jQuery( '#featured-wrap #featured .slide:eq(' + ( slider.currentSlide + 1 ) + ')' ).length ) {
         			  var slide = ( slider.currentSlide + 1 );
         		} else {
         			  var slide = 0;
         		}
         		adjust_content_margin( slide );
      	},
      	after: function( slider ) {
            adjust_slide_height();
         		adjust_content_margin( slider.currentSlide );
      	}
   	});
   	jQuery( '#slides' ).addClass( 'loaded' );
});

/**
 * Adjust the content margin for the given slide.
 * @since  1.0.0
 * @param  {object} slide The slide in question.
 * @return {void}
 */
function adjust_content_margin( slide ) {
    var height = jQuery( '#featured .slide:eq(' + slide + ')' ).outerHeight();
    var window_width = jQuery( window ).width();
    if( window_width >= 768 ) {
      jQuery( '.home #content' ).stop().animate( { marginTop: height + 'px' }, woo_localized_data.animationDuration );
    }
} // End adjust_content_margin()

/**
 * Adjust the height for the current slide based on content
 * @since  1.0.0
 * @return {void}
 */
function adjust_slide_height() {
  var content_height = jQuery( '.flex-active-slide .slide-content-container' ).outerHeight();
  var current_height = jQuery( '.flex-active-slide' ).outerHeight();
  if(content_height > current_height) {
    jQuery( '.current-slide' ).css( 'height' , content_height + 'px' );
    jQuery( '#featured' ).css( 'height' , content_height + 'px' );
  }
}

// Adjust the opacity of the slide content when scrolling the screen.
jQuery( window ).scroll( function () {
      var slide_height = jQuery( '#featured .slide.flex-active-slide' ).outerHeight();
      var scroll_pos = jQuery( 'body' ).scrollTop();
      var ratio = scroll_pos / slide_height;
      var opacity = 1 - ratio;

      if( opacity <= 1 ) {
          jQuery( '#featured .slide .slide-content-container' ).css( 'opacity', opacity );
      }
});