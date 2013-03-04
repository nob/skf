<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! is_admin() ) { add_action( 'wp_enqueue_scripts', 'woothemes_add_javascript' ); }

if ( ! function_exists( 'woothemes_add_javascript' ) ) {
	function woothemes_add_javascript() {
		global $woo_options;

		wp_register_script( 'prettyPhoto', get_template_directory_uri() . '/includes/js/jquery.prettyPhoto.js', array( 'jquery' ) );
		wp_register_script( 'enable-lightbox', get_template_directory_uri() . '/includes/js/enable-lightbox.js', array( 'jquery', 'prettyPhoto' ) );
		wp_register_script( 'google-maps', 'http://maps.google.com/maps/api/js?sensor=false' );
		wp_register_script( 'google-maps-markers', get_template_directory_uri() . '/includes/js/markers.js' );
		wp_register_script( 'flexslider', get_template_directory_uri() . '/includes/js/jquery.flexslider-min.js', array( 'jquery' ) );
		wp_register_script( 'featured-slider', get_template_directory_uri() . '/includes/js/featured-slider.js', array( 'jquery' , 'flexslider' ) );
		wp_register_script( 'testimonials', get_template_directory_uri() . '/includes/js/testimonials.js', array( 'jquery' , 'flexslider' ) );
		wp_enqueue_script( 'third party', get_template_directory_uri() . '/includes/js/third-party.js', array( 'jquery' ) );
		wp_enqueue_script( 'general', get_template_directory_uri() . '/includes/js/general.js', array( 'jquery' ) );
		wp_register_script( 'portfolio', get_template_directory_uri() . '/includes/js/portfolio.js', array( 'jquery', 'prettyPhoto' ) );

		// Load Google Script on Contact Form Page Template
		if ( is_page_template( 'template-contact.php' ) ) {
			wp_enqueue_script( 'google-maps' );
			wp_enqueue_script( 'google-maps-markers' );
		} // End If Statement

		// Load Portfolio JS for homepage, page template, single page, post type archive, and taxonomy archive
		if ( is_page_template( 'template-portfolio.php' ) || ( is_singular() && get_post_type() == 'portfolio' ) || is_tax( 'portfolio-gallery' ) || is_post_type_archive( 'portfolio' ) ) {			
			wp_enqueue_script( 'portfolio' );
		}		
		
		do_action( 'woothemes_add_javascript' );
	} // End woothemes_add_javascript()
}

if ( ! is_admin() ) { add_action( 'wp_print_styles', 'woothemes_add_css' ); }

if ( ! function_exists( 'woothemes_add_css' ) ) {
	function woothemes_add_css () {
		wp_register_style( 'prettyPhoto', get_template_directory_uri().'/includes/css/prettyPhoto.css' );

		if ( is_page_template('template-portfolio.php') || is_front_page() || ( is_singular() && get_post_type() == 'portfolio' ) || is_tax( 'portfolio-gallery' ) || is_post_type_archive( 'portfolio' ) ) {
			wp_enqueue_style( 'prettyPhoto' );
		}		

		wp_register_style( 'non-responsive', get_template_directory_uri() . '/css/non-responsive.css' );

		do_action( 'woothemes_add_css' );
	} // End woothemes_add_css()
}

// Add an HTML5 Shim

add_action( 'wp_head', 'html5_shim' ); 

if ( ! function_exists( 'html5_shim' ) ) {
	function html5_shim() {
		?>
<!--[if lt IE 9]>
<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
		<?php
	} // End html5_shim()
}

add_action( 'woothemes_add_javascript' , 'woo_load_featured_slider_js' );

function woo_load_featured_slider_js() {
	if ( is_home() ) {

		//Slider settings
		$settings = array(
					'featured_speed' => '7',
					'featured_hover' => 'false',
					'featured_action' => 'true', 
					'featured_touchswipe' => 'false',
					'featured_animation_speed' => '0.6',
					'featured_pagination' => 'false',
					'featured_nextprev' => 'true', 
					'featured_animation' => 'fade', 
					'featured_pagination_vertical' => 'false'
					);
					
		$settings = woo_get_dynamic_values( $settings );

		if ( $settings['featured_speed'] == '0' ) { $slideshow = 'false'; } else { $slideshow = 'true'; }
		if ( $settings['featured_touchswipe'] == 'true' ) { $touchSwipe = 'true'; } else { $touchSwipe = 'false'; }
		if ( $settings['featured_hover'] == 'true' ) { $pauseOnHover = 'true'; } else { $pauseOnHover = 'false'; }
		if ( $settings['featured_action'] == 'true' ) { $pauseOnAction = 'true'; } else { $pauseOnAction = 'false'; }
		if ( ! in_array( $settings['featured_animation'], array( 'fade', 'slide' ) ) ) { $settings['featured_animation'] = 'fade'; }
		$slideshowSpeed = (int) $settings['featured_speed'] * 1000; // milliseconds
		$animationDuration = (int) $settings['featured_animation_speed'] * 1000; // milliseconds
		$nextprev = $settings['featured_nextprev'];
		
		$direction = 'horizontal';
		if ( 'true' == $settings['featured_pagination_vertical'] ) {
			$direction = 'vertical';
		}
		
		if ( $settings['featured_pagination'] == 'true' ) {
			$pagination = 'false';
			$manualControls = '';
		} else {
			$pagination = 'false';
			$manualControls = '';
		}
		$data = array(
			'direction' => $direction, 
			'animation' => $settings['featured_animation'],
			'controlsContainer' => '.flexslider-container',
			'smoothHeight' => 'true',
			'directionNav' => $nextprev,
			'controlNav' => $pagination,
			'manualControls' => $manualControls,
			'slideshow' => $slideshow,
			'slideshowSpeed' => $slideshowSpeed,
			'animationDuration' => $animationDuration,
			'touch' => $touchSwipe,
			'pauseOnHover' => $pauseOnHover, 
			'pauseOnAction' => $pauseOnAction
		);

		wp_localize_script( 'featured-slider', 'woo_localized_data', $data);

		wp_enqueue_script( 'featured-slider' );
	} // End woo_load_featured_slider_js()
}

add_action( 'woothemes_add_javascript' , 'woo_load_testimonials_slider' );

function woo_load_testimonials_slider() {
// 	if ( is_active_widget( false, false, 'widget_woothemes_testimonials', true ) ) {
 		wp_enqueue_script( 'testimonials' );
// 	}
}

?>