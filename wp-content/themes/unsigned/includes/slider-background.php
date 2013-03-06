<?php
/**
 * Slider Background Images Template
 *
 * Here we setup all logic and XHTML that is required for the slider.
 *
 * @package WooFramework
 * @subpackage Template
 * @uses $woothemes_slides
 */
 
 global $woo_options, $woothemes_slides;
 
/**
 * The Variables
 *
 * Setup default variables, overriding them if the "Theme Options" have been saved.
 */
	
	$settings = array(
					'featured_limit' => '6', 
					'slider_scroll' => 'fixed', 
					'featured_slide_group' => 0
					);
					
	$settings = woo_get_dynamic_values( $settings );
 
 $slides = $woothemes_slides->get_slides( array( 'limit' => $settings['featured_limit'], 'term' => $settings['featured_slide_group'] ) );
 
 $slide_images = '';

 if ( count( $slides ) > 0 ) {
 	$count = 0;
 	$class = 'active';
 	foreach ( $slides as $k => $v ) {
 		$count++;
	 	if ( $count > 1 ) { $class = 'inactive'; }
	 	
	 	$slide_images .= '<img src="' . $v->thumbnail . '" class="slide-id-' . $v->ID . ' woo-image slide-image ' . $class . '" />' . "\n";
	}
 	
?>
	<div id="slider-background" class="slider-background <?php echo $settings['slider_scroll']; ?>">
		<div class="slides-container">
		<?php echo $slide_images; ?>
		</div><!--/.slides-container-->
	</div><!--/.slider-background-->
<?php
 }
?>