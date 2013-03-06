<?php
/**
 * woothemes_add_javascript function.
 *
 * @description Load JavaScript files for this theme.
 * @access public
 * @return void
 */

if ( ! is_admin() ) { add_action( 'wp_print_scripts', 'woothemes_add_javascript' ); }

if ( ! function_exists( 'woothemes_add_javascript' ) ) {
	function woothemes_add_javascript() {
		$settings = array(
					'enable_lightbox' => 'false'
					);
					
		$settings = woo_get_dynamic_values( $settings );

		wp_enqueue_script( 'jquery' );    
		wp_enqueue_script( 'third-party', get_template_directory_uri() . '/includes/js/third-party.js', array( 'jquery' ) );
		
		wp_register_script( 'prettyPhoto', get_template_directory_uri() . '/includes/js/jquery.prettyPhoto.js', array( 'jquery' ) );
		wp_register_script( 'prettyPhoto-loader', get_template_directory_uri() . '/includes/js/jquery.prettyPhoto.wooloader.js', array( 'jquery', 'prettyPhoto' ) );
		
		if ( $settings['enable_lightbox'] == 'true' || ( is_singular() && get_post_type() == 'gallery' ) ) {
			wp_enqueue_script( 'prettyPhoto-loader' );
		}
		
		wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/includes/js/jquery.flexslider-min.js', array( 'jquery' ) );
		
		wp_enqueue_script( 'general', get_template_directory_uri() . '/includes/js/general.js', array( 'jquery' ), time() );
		
		$settings = array(
					'slider_speed' => '7', 
					'slider_animation_speed' => '0.6', 
					'slider_hover' => 'true'
					);
					
		$settings = woo_get_dynamic_values( $settings );
		
		wp_localize_script( 'general', 'woo_localized_data', $settings );
	} // End woothemes_add_javascript()
}

/**
 * woothemes_add_css function.
 *
 * @description Load additional, JavaScript-specific, CSS files for this theme.
 * @access public
 * @return void
 */

if ( ! is_admin() ) { add_action( 'wp_print_styles', 'woothemes_add_css' ); }

if ( ! function_exists( 'woothemes_add_css' ) ) {
	function woothemes_add_css() {
		$settings = array(
					'enable_lightbox' => 'false'
					);
					
		$settings = woo_get_dynamic_values( $settings );

		wp_register_style( 'prettyPhoto', get_template_directory_uri() . '/includes/css/prettyPhoto.css' );
		
		if ( $settings['enable_lightbox'] == 'true' || ( is_singular() && get_post_type() == 'gallery' ) ) {
			wp_enqueue_style( 'prettyPhoto' );
		}
	} // End woothemes_add_css()
}
?>