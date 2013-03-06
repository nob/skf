<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page' );
}
?>
<?php
if ( ! is_admin() ) { add_action( 'wp_enqueue_scripts', 'woothemes_add_javascript' ); }

if ( ! function_exists( 'woothemes_add_javascript' ) ) {
	function woothemes_add_javascript() {
		global $woo_options;

		wp_register_script( 'prettyPhoto', get_template_directory_uri() . '/includes/js/jquery.prettyPhoto.js', array( 'jquery' ) );
		wp_register_script( 'enable-lightbox', get_template_directory_uri() . '/includes/js/enable-lightbox.js', array( 'jquery', 'prettyPhoto' ) );
		wp_register_script( 'portfolio', get_template_directory_uri() . '/includes/js/portfolio.js', array( 'jquery', 'prettyPhoto' ) );
		wp_register_script( 'third-party', get_template_directory_uri() . '/includes/js/third-party.js', array( 'jquery' ) );
		wp_enqueue_script( 'general', get_template_directory_uri() . '/includes/js/general.js', array( 'jquery', 'third-party' ) );
		wp_register_script( 'google-maps', 'http://maps.google.com/maps/api/js?sensor=false' );
		wp_register_script( 'google-maps-markers', get_template_directory_uri() . '/includes/js/markers.js' );
		wp_register_script( 'flexslider', get_template_directory_uri() . '/includes/js/jquery.flexslider-min.js', array( 'jquery' ) );
		wp_register_script( 'featured-slider', get_template_directory_uri() . '/includes/js/featured-slider.js', array( 'jquery' , 'flexslider' ), '1.2.5' );
		wp_register_script( 'masonry', get_template_directory_uri() . '/includes/js/jquery.masonry.min.js', array( 'jquery' ) );



		// Load Google Script on Contact Form Page Template
		if ( is_page_template( 'template-contact.php' ) ) {
			wp_enqueue_script( 'google-maps' );
			wp_enqueue_script( 'google-maps-markers' );
		} // End If Statement

		// Load Portfolio JS for homepage, page template, single page, post type archive, and taxonomy archive
		if ( is_page_template( 'template-portfolio.php' ) || ( is_singular() && get_post_type() == 'portfolio' ) || is_tax( 'portfolio-gallery' ) || is_post_type_archive( 'portfolio' ) ) {
			wp_enqueue_script( 'portfolio' );
		}

		// Load masonry
		wp_enqueue_script( 'masonry' );

		// Load prettyphoto on WooCommerce product pages if using theme lightbox
		if ( is_woocommerce_activated() ) {
			if ( $woo_options[ 'woo_enable_lightbox' ] == "true" && is_product() ) {
				wp_enqueue_script( 'prettyPhoto' );
				wp_enqueue_script( 'enable-lightbox' );
			}
		}

		do_action( 'woothemes_add_javascript' );
	} // End woothemes_add_javascript()
}

if ( ! is_admin() ) { add_action( 'wp_print_styles', 'woothemes_add_css' ); }

if ( ! function_exists( 'woothemes_add_css' ) ) {
	function woothemes_add_css () {
		global $woo_options;
		wp_register_style( 'prettyPhoto', get_template_directory_uri() . '/includes/css/prettyPhoto.css' );

		if ( is_page_template('template-portfolio.php') || is_front_page() || ( is_singular() && get_post_type() == 'portfolio' ) || is_tax( 'portfolio-gallery' ) || is_post_type_archive( 'portfolio' ) ) {
			wp_enqueue_style( 'prettyPhoto' );
		}

		if ( is_woocommerce_activated() ) {
			if ( $woo_options[ 'woo_enable_lightbox' ] == "true" && is_product() ) {
				wp_enqueue_style( 'prettyPhoto' );
			}
		}

		do_action( 'woothemes_add_css' );
	} // End woothemes_add_css()
}

// Add HTML5 shim in <IE9
add_action( 'wp_head', 'html5_shim' );

if ( ! function_exists( 'html5_shim' ) ) {
	function html5_shim () {
		?>
		<!--[if lt IE 9]>
			<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<?php
	} // End html5_shim()
}


add_action( 'woothemes_add_javascript' , 'woo_load_featured_slider_js' );
function woo_load_featured_slider_js() {
	if ( is_home() || is_front_page() ) {

		//Slider settings
		$settings = array(
					'featured_speed' => '7',
					'featured_hover' => 'false',
					'featured_animation_speed' => '0.6',
					'featured_pagination' => 'false',
					'featured_nextprev' => 'true'
					);

		$settings = woo_get_dynamic_values( $settings );

		if ( $settings['featured_speed'] == '0' ) { $slideshow = 'false'; } else { $slideshow = 'true'; }
		if ( $settings['featured_hover'] == 'true' ) { $pauseOnHover = 'true'; } else { $pauseOnHover = 'false'; }

		$slideshowSpeed = (int) $settings['featured_speed'] * 1000; // milliseconds
		$animationDuration = (int) $settings['featured_animation_speed'] * 1000; // milliseconds
		$nextprev = $settings['featured_nextprev'];
		if ( $settings['featured_pagination'] == 'true' ) {
			$pagination = 'true';
			$manualControls = '.manual ol li';
		} else {
			$pagination = 'false';
			$manualControls = '';
		}
		$data = array(
			'animation' => 'fade',
			'controlsContainer' => '.controls-container',
			'smoothHeight' => 'true',
			'useCSS' => 'true',
			'directionNav' => $nextprev,
			'controlNav' => $pagination,
			'manualControls' => $manualControls,
			'slideshow' => $slideshow,
			'pauseOnHover' => $pauseOnHover,
			'slideshowSpeed' => $slideshowSpeed,
			'animationDuration' => $animationDuration,
		);

		wp_localize_script('featured-slider', 'woo_localized_data', $data);

		wp_enqueue_script( 'featured-slider' );
	}
}
?>