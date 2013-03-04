jQuery(document).ready(function(){

	testimonialsSelector = '.widget_woothemes_testimonials .testimonials';

	if  ( !jQuery(testimonialsSelector).parents().is('#main') ) {
		jQuery(testimonialsSelector).flexslider({
			animation: "fade",
			selector: ".testimonials-list > .quote",
			controlNav: false,
			directionNav: true,
		});
	}

});