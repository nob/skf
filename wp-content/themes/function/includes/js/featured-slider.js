jQuery( window ).load( function() {
    if ( woo_localized_data.slideshow == 'true' ) { woo_localized_data.slideshow = true; } else { woo_localized_data.slideshow = false; }
    if ( woo_localized_data.directionNav == 'true' ) { woo_localized_data.directionNav = true; } else { woo_localized_data.directionNav = false; }
    if ( woo_localized_data.controlNav == 'true' ) { woo_localized_data.controlNav = true; } else { woo_localized_data.controlNav = false; }

    var mainSliderArgs = {
            animationLoop: false, 
            animation: woo_localized_data.animation,
            controlsContainer: woo_localized_data.controlsContainer,
            smoothHeight: woo_localized_data.smoothHeight,
            directionNav: woo_localized_data.directionNav,
            controlNav: woo_localized_data.controlNav,
            manualControls: woo_localized_data.manualControls,
            slideshow: woo_localized_data.slideshow,
            slideshowSpeed: woo_localized_data.slideshowSpeed,
            animationDuration: woo_localized_data.animationDuration,
            touch: woo_localized_data.touch,
            pauseOnHover: woo_localized_data.pauseOnHover, 
            pauseOnAction: woo_localized_data.pauseOnAction, 
            after: function ( slider ) {
                if ( jQuery( 'body' ).hasClass( 'has-vertical-slider-pagination' ) ) { wooResizeSliderPagination(); }

                // Make sure the current pagination item is highlighted, if we have pagination.
                if ( jQuery( '#slider-pagination' ).length ) {
                    var currentPagination = jQuery( '#slider-pagination' ).find( 'li:eq(' + slider.currentSlide + ')' );
                    jQuery( '#slider-pagination' ).find( '.flex-active-slide' ).removeClass( 'flex-active-slide' );
                    currentPagination.addClass( 'flex-active-slide' );
                }
            }
    };

    var paginationSliderArgs = {
            direction: woo_localized_data.direction, 
            animationLoop: false, 
            animation: 'slide', 
            controlsContainer: '', 
            smoothHeight: false, 
            directionNav: true, 
            controlNav: false, 
            slideshow: false,  
            minItems: 5, 
            itemWidth: 240, 
            move: 1, 
            asNavFor: '#featured-slider', 
            start: function ( slider ) {
                if ( jQuery( 'body' ).hasClass( 'has-vertical-slider-pagination' ) ) { wooResizeSliderPagination(); }
            }, 
            after: function ( slider ) {
                if ( jQuery( 'body' ).hasClass( 'has-vertical-slider-pagination' ) ) { wooResizeSliderPagination(); }
            }
        };

   	jQuery( '#featured-slider' ).flexslider( mainSliderArgs );

    if ( jQuery( '#slider-pagination' ).length ) {
        jQuery( '#slider-pagination' ).flexslider( paginationSliderArgs );
    }

});

jQuery( window ).resize( function ( e ) {
    if ( jQuery( 'body' ).hasClass( 'has-vertical-slider-pagination' ) ) { wooResizeSliderPagination(); }
});

/**
 * Resize the pagination to the height of the main slider.
 * @since  1.0.0
 * @return {void}
 */
function wooResizeSliderPagination () {
    var sliderHeight = jQuery( '#featured-slider' ).height();
    jQuery( '.has-vertical-slider-pagination #slider-pagination, .has-vertical-slider-pagination #slider-pagination .flex-viewport' ).height( sliderHeight );
} // End wooResizeSliderPagination()