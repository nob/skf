<?php
/**
 * Index Template
 *
 * Here we setup all logic and XHTML that is required for the index template, used as both the homepage
 * and as a fallback template, if a more appropriate template file doesn't exist for a specific context.
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options;

/**
 * The Variables
 *
 * Setup default variables, overriding them if the "Theme Options" have been saved.
 */
	
	$settings = array(
					'enable_slides' => 'true'
					);
					
	$settings = woo_get_dynamic_values( $settings );
?>

    <div id="content" class="col-full">
		<section id="main" class="col-left">      
        
<?php
	if ( $settings['enable_slides'] == 'true' ) {
		// Load the slider.
		get_template_part( 'includes/slider', 'content' );
	}
?>
            
		<?php if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) { ?>
			<section id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</section><!--/#breadcrumbs -->
		<?php } ?>
		<?php
			if ( ! dynamic_sidebar( 'homepage' ) ) {
				// Display default widgets.
				
				// VIDEOS
				$args = array( 'title' => __( 'Videos', 'woothemes' ), 'width' => '640', 'height' => '450' );
				the_widget( 'Woo_Widget_Videos', $args );
				
				// TOUR DATES
				// Get the first available tour listing.
				$args = 'title=' . __( 'Tour Dates', 'woothemes' );
				$tour = get_terms( 'event_tour', array( 'number' => 1 ) );
				if ( ! is_wp_error( $tour ) ) {
					foreach ( $tour as $k => $v ) {
						$args .= '&tour=' . $v->term_id;
					}
				}

				the_widget( 'Woo_Widget_TourDates', $args );
				
				// WOO - PHOTOS
				$args = array( 'title' => __( 'Gallery', 'woothemes' ) );
				the_widget( 'Woo_Widget_Photos', $args );
				
				// WOO - TABS
				$args = array();
				the_widget( 'Woo_Widget_WooTabs', $args );
				add_action( 'wp_footer', 'woo_widget_tabs_js' );
			}
		?>   
		</section><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>