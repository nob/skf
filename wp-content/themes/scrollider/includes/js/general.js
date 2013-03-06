/*-----------------------------------------------------------------------------------*/
/* GENERAL SCRIPTS */
/*-----------------------------------------------------------------------------------*/
jQuery(document).ready(function(){

	// Table alt row styling
	jQuery( '.entry table tr:odd' ).addClass( 'alt-table-row' );
	
	// FitVids - Responsive Videos
	jQuery( ".post, .widget, .panel, .slide, .single-portfolio-gallery" ).fitVids();
	
	// Add class to parent menu items with JS until WP does this natively
	jQuery("ul.sub-menu").parents('li').addClass('parent');
	
	
	// Responsive Navigation (switch top drop down for select)
	jQuery('ul#top-nav').mobileMenu({
		switchWidth: 767,                   //width (in px to switch at)
		topOptionText: 'Select a page',     //first option text
		indentString: '&nbsp;&nbsp;&nbsp;'  //string for indenting nested items
	});
  	  	
  	// Show/hide the main navigation
  	jQuery('.nav-toggle').click(function() {
	  jQuery('#navigation').slideToggle('fast', function() {
	  	return false;
	    // Animation complete.
	  });
	});
	
	// Stop the navigation link moving to the anchor (Still need the anchor for semantic markup)
	jQuery('.nav-toggle a, a.cart-parent').click(function(e) {
        e.preventDefault();
    });
    
    // Show/hide the mini-cart
  	jQuery('a.cart-parent').click(function() {
	  jQuery('#header .cart_list').fadeToggle('fast', function() {
	  	return false;
	    // Animation complete.
	  });
	});
    
});

jQuery(window).load(function(){
	if (jQuery(window).width() > 767) {	
		jQuery('ul.products').masonry({
		  itemSelector: '.product',
		  // set columnWidth a fraction of the container width
		  columnWidth: function( containerWidth ) {
		    return containerWidth / 3;
		  }
		});
	}
});