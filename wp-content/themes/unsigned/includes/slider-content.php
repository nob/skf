<?php
/**
 * Slider Content Template
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
					'featured_slide_group' => 0
					);
					
	$settings = woo_get_dynamic_values( $settings );
 
 $slides = $woothemes_slides->get_slides( array( 'limit' => $settings['featured_limit'], 'term' => $settings['featured_slide_group'] ) );

 $slide_content = '';
 
 if ( count( $slides ) > 0 ) {
 	foreach ( $slides as $k => $v ) {
 		$slide_content .= '<li><div id="slide-id-' . $v->ID . '" class="slide" rel="' . esc_attr( $v->thumbnail ) . '">' . apply_filters( 'the_content', $v->post_content ) . '</div><!--/.slide--></li>' . "\n";
 	}
 	
?>
	<div id="slider" class="slider">
		<div class="flexslider">
			<ul class="slides">
		<?php echo $slide_content; ?>
			</ul>
		</div><!--/.flexslider-->
	</div><!--/.slider-->
<?php
 }
?>