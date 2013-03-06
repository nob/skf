<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Theme Setup
- Load layout.css in the <head>
- Load responsive <meta> tags in the <head>
- Add custom styling to HEAD
- Add custom typograhpy to HEAD
- Add layout to body_class output
- woo_feedburner_link

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Theme Setup */
/*-----------------------------------------------------------------------------------*/
/**
 * Theme Setup
 *
 * This is the general theme setup, where we add_theme_support(), create global variables
 * and setup default generic filters and actions to be used across our theme.
 *
 * @package WooFramework
 * @subpackage Logic
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */

if ( ! isset( $content_width ) ) $content_width = 640;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support for post thumbnails.
 *
 * To override woothemes_setup() in a child theme, add your own woothemes_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for automatic feed links.
 * @uses add_editor_style() To style the visual editor.
 */

add_action( 'after_setup_theme', 'woothemes_setup' );

if ( ! function_exists( 'woothemes_setup' ) ) {
	function woothemes_setup () {
		// This theme styles the visual editor with editor-style.css to match the theme style.
		if ( locate_template( 'editor-style.css' ) != '' ) { add_editor_style(); }

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );
	} // End woothemes_setup()
}

/**
 * Set the default Google Fonts used in theme.
 *
 * Used to set the default Google Fonts used in the theme, when Custom Typography is disabled.
 */

global $default_google_fonts;
$default_google_fonts = array( 'Droid Serif', 'Kreon' );


/*-----------------------------------------------------------------------------------*/
/* Load layout.css in the <head> */
/*-----------------------------------------------------------------------------------*/

if ( ! is_admin() ) { add_action( 'get_header', 'woo_load_frontend_css', 10 ); }

if ( ! function_exists( 'woo_load_frontend_css' ) ) {
	function woo_load_frontend_css () {
		wp_register_style( 'woo-layout', get_template_directory_uri() . '/css/layout.css' );
		
		wp_enqueue_style( 'woo-layout' );
	} // End woo_load_frontend_css()
}

/*-----------------------------------------------------------------------------------*/
/* Load responsive <meta> tags in the <head> */
/*-----------------------------------------------------------------------------------*/

add_action( 'wp_head', 'woo_load_responsive_meta_tags', 10 );

if ( ! function_exists( 'woo_load_responsive_meta_tags' ) ) {
	function woo_load_responsive_meta_tags () {
		$html = '';
		
		$html .= "\n" . '<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->' . "\n";
		$html .= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />' . "\n";
		$html .= '<!--  Mobile viewport scale | Disable user zooming as the layout is optimised -->' . "\n";
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n" . "\n";
		
		echo $html;
	} // End woo_load_responsive_meta_tags()
}

/*-----------------------------------------------------------------------------------*/
/* Add Custom Styling to HEAD */
/*-----------------------------------------------------------------------------------*/

add_action( 'woo_head', 'woo_custom_styling', 10 ); // Add custom styling to HEAD

if ( ! function_exists( 'woo_custom_styling' ) ) {
	function woo_custom_styling() {
	
		$output = '';
		// Get options
		$settings = array(
						'body_color' => '', 
						'body_img' => '', 
						'body_repeat' => '', 
						'body_pos' => '', 
						'link_color' => '', 
						'link_hover_color' => '', 
						'button_color' => '', 
						'transparency' => '70', 
						'alt_stylesheet' => 'default.css',
						'enable_slides' => 'true'
						);
		$settings = woo_get_dynamic_values( $settings );
		
			
		// Add CSS to output
		if ( $settings['body_color'] != '' ) {
			$output .= 'body { background: ' . $settings['body_color'] . ' !important; }' . "\n";
		}
		
		if( $settings['enable_slides'] != 'true'  || ( $settings['enable_slides'] == 'true' && ! is_front_page() ) ) {
			if ( $settings['body_img'] != '' ) {
				$output .= 'body { background-image: url( ' . $settings['body_img'] . ' ) !important; }' . "\n";
			}

			if ( ( $settings['body_img'] != '' ) && ( $settings['body_repeat'] != '' ) && ( $settings['body_pos'] != '' ) ) {
				$output .= 'body { background-repeat: ' . $settings['body_repeat'] . ' !important; }' . "\n";
			}
			
			if ( ( $settings['body_img'] != '' ) && ( $settings['body_pos'] != '' ) ) {
				$output .= 'body { background-position: ' . $settings['body_pos'] . ' !important; }' . "\n";
			}
		}
		
		if ( $settings['link_color'] != '' ) {
			$output .= 'a { color: ' . $settings['link_color'] . ' !important; }' . "\n";
		}
		
		if ( $settings['link_hover_color'] != '' ) {
			$output .= 'a:hover, .post-more a:hover, .post-meta a:hover, .post p.tags a:hover { color: ' . $settings['link_hover_color'] . ' !important; }' . "\n";
		}
		
		if ( $settings['button_color'] != '' ) {
			$output .= 'a.button, a.comment-reply-link, #commentform #submit, #contact-page .submit, #connect .container .newsletter-form .submit  { background: ' . $settings['button_color'] . ' !important; border-color: ' . $settings['button_color'] . ' !important; }' . "\n";
			$output .= 'a.button:hover, a.button.hover, a.button.active, a.comment-reply-link:hover, #commentform #submit:hover, #contact-page .submit:hover { background: ' . $settings['button_color'] . ' !important; opacity: 0.9; }' . "\n";
		}
		
		// Custom Transparency Setting
		if ( $settings['transparency'] != '' ) {
			// Determine alt style to determine which colour to use for the boxes.
			$box_rgb = array( 'r' => 0, 'g' => 0, 'b' => 0 );
			
			$style = $settings['alt_stylesheet'];
			
			if ( $style == 'light.css' ) {
				$box_rgb = array( 'r' => 255, 'g' => 255, 'b' => 255 );
			}
			
			// Make sure we have a valid value.
			$transparency = $settings['transparency'];
			if ( $transparency < 0 ) { $transparency = 0; }
			if ( $transparency > 100 ) { $transparency = 100; }
			
			$box_rgb = apply_filters( 'woo_box_rgb', $box_rgb, $style, $transparency );
			
			$selector = '.flexslider .slides .slide, .widget_video, .widget.woo-audio-player, .widget .container, .rounded-border, .widget.widget_recent_news ul li, .widget.widget_press_clippings ul li, #header-wrap, #navigation .nav ul, .widget.widget_events ul li, table, #tabs .inside, #tabs ul.wooTabs li a.selected, #tabs ul.wooTabs li a:hover, .widget.widget_galleries ul li, .widget .woo-audio-player, .widget.widget_woo_twitter .back, .widget.widget_albums ul li, .widget_shopping_cart ul.cart_list li, .widget_shopping_cart ul.product_list_widget li, .searchform, .textwidget, .tagcloud, .post, .type-page, .type-band_member, .type-video, .type-product, .type-gallery, .type-album, .type-event, #contact-page, ul.products, tr.alt-table-row, ul.widget-video-list, p.buttons, body .widget_shopping_cart .total, #post-author, #comments .comment.thread-even, .widget ul, #navigation .nav ul, #respond #commentform, .type-press, .widget_woo_blogauthorinfo .content';
			
			$selector = apply_filters( 'woo_box_rgb_selector', $selector, $style, $transparency );
			
			$output .= $selector . ' { background-color: rgba(' . $box_rgb['r'] . ', ' . $box_rgb['g'] . ', ' . $box_rgb['b'] . ', ' . ( $transparency / 100 ) . ' ); }' . "\n";
		}
		
		// Output styles
		if ( isset( $output ) && $output != '' ) {
			$output = strip_tags( $output );
			$output = "<!-- Woo Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
			echo $output;
		}
			
	} // End woo_custom_styling()
}

/*-----------------------------------------------------------------------------------*/
/* Add custom typograhpy to HEAD */
/*-----------------------------------------------------------------------------------*/

add_action( 'woo_head','woo_custom_typography', 10 ); // Add custom typography to HEAD

if ( ! function_exists( 'woo_custom_typography' ) ) {
	function woo_custom_typography() {
	
		// Get options
		global $woo_options;
				
		// Reset	
		$output = '';
		
		// Add Text title and tagline if text title option is enabled
		if ( isset( $woo_options['woo_texttitle'] ) && $woo_options['woo_texttitle'] == 'true' ) {		
			
			if ( $woo_options['woo_font_site_title'] )
				$output .= '#wrapper #header .site-title a {'.woo_generate_font_css($woo_options['woo_font_site_title']).'}' . "\n";	
			if ( $woo_options['woo_tagline'] == "true" AND $woo_options['woo_font_tagline'] ) 
				$output .= '#wrapper #header .site-description {'.woo_generate_font_css($woo_options[ 'woo_font_tagline']).'}' . "\n";	
		}

		if ( isset( $woo_options['woo_typography'] ) && $woo_options['woo_typography'] == 'true' ) {
			
			if ( isset( $woo_options['woo_font_body'] ) && $woo_options['woo_font_body'] )
				$output .= 'body { '.woo_generate_font_css($woo_options['woo_font_body'], '1.5').' }' . "\n";	

			if ( isset( $woo_options['woo_font_nav'] ) && $woo_options['woo_font_nav'] )
				$output .= '#wrapper #navigation, #wrapper #navigation .nav a { '.woo_generate_font_css($woo_options['woo_font_nav'], '1.4').' }' . "\n";	

			if ( isset( $woo_options['woo_font_page_title'] ) && $woo_options['woo_font_page_title'] )
				$output .= '.page #wrapper header h1 { '.woo_generate_font_css($woo_options[ 'woo_font_page_title' ]).' }' . "\n";

			if ( isset( $woo_options['woo_font_post_title'] ) && $woo_options['woo_font_post_title'] )
				$output .= '.post header h1, .type-page header h1, .type-band_member header h1, .type-video header h1, .type-product header h1, .type-gallery header h1, .type-album header h1, .type-event header h1, #contact-page header h1, .type-press header h1, .post header h1, .type-page header h1, .type-band_member header h1, .type-video header h1, .type-product header h1, .type-gallery header h1, .type-album header h1, .type-event header h1, #contact-page header h1, .type-press header h1, header h1.out-box { '.woo_generate_font_css($woo_options[ 'woo_font_post_title' ]).' }' . "\n";	
		
			if ( isset( $woo_options['woo_font_post_meta'] ) && $woo_options['woo_font_post_meta'] )
				$output .= '.post-meta { '.woo_generate_font_css($woo_options[ 'woo_font_post_meta' ]).' }' . "\n";	

			if ( isset( $woo_options['woo_font_post_entry'] ) && $woo_options['woo_font_post_entry'] )
				$output .= '#wrapper .entry, #wrapper .entry p { '.woo_generate_font_css($woo_options[ 'woo_font_post_entry' ], '1.5').' } h1, h2, h3, h4, h5, h6 { font-family: '.stripslashes($woo_options[ 'woo_font_post_entry' ]['face']).', arial, sans-serif; }'  . "\n";	

			if ( isset( $woo_options['woo_font_widget_titles'] ) && $woo_options['woo_font_widget_titles'] )
				$output .= '#wrapper .widget h1, #wrapper .widget h2, #wrapper .widget h3, #wrapper .widget h4, #wrapper .widget h5, #wrapper .widget h6 { '.woo_generate_font_css($woo_options[ 'woo_font_widget_titles' ]).' }'  . "\n";
				$output .= '#wrapper .widget h1 a, #wrapper .widget h2 a, #wrapper .widget h3 a, #wrapper .widget h4 a, #wrapper .widget h5 a, #wrapper .widget h6 a { '.woo_generate_font_css($woo_options[ 'woo_font_widget_titles' ]).' }'  . "\n";
								
			// Component titles
			if ( isset( $woo_options['woo_font_component_titles'] ) && $woo_options['woo_font_component_titles'] )
				$output .= '.component h2.component-title { '.woo_generate_font_css($woo_options[ 'woo_font_component_titles' ]).' }'  . "\n";	

		// Add default typography Google Font
		} else {
		
			// Load default Google Fonts
			global $default_google_fonts;
			if ( is_array( $default_google_fonts) and count( $default_google_fonts ) > 0 ) :
			
				$count = 0;
				foreach ( $default_google_fonts as $font ) {
					$count++;
					$woo_options[ 'woo_default_google_font_'.$count ] = array( 'face' => $font );
					woo_generate_font_css( $woo_options[ 'woo_default_google_font_'.$count ] );			
				}
				if (function_exists( 'woo_google_webfonts')) woo_google_webfonts();

			endif;
		
		} 
		
		// Output styles
		if (isset($output) && $output != '') {
		
			// Enable Google Fonts stylesheet in HEAD
			if (function_exists( 'woo_google_webfonts')) woo_google_webfonts();
			
			$output = "<!-- Woo Custom Typography -->\n<style type=\"text/css\">\n" . $output . "</style>\n\n";
			echo $output;
			
		}
			
	} // End woo_custom_typography()
}

// Returns proper font css output
if (!function_exists( 'woo_generate_font_css')) {
	function woo_generate_font_css($option, $em = '1') {

		// Test if font-face is a Google font
		global $google_fonts;
		foreach ( $google_fonts as $google_font ) {

			// Add single quotation marks to font name and default arial sans-serif ending
			if ( $option[ 'face' ] == $google_font[ 'name' ] )
				$option[ 'face' ] = "'" . $option[ 'face' ] . "', arial, sans-serif";

		} // END foreach

		if ( !@$option["style"] && !@$option["size"] && !@$option["unit"] && !@$option["color"] )
			return 'font-family: '.stripslashes($option["face"]).';';
		else
			return 'font:'.$option["style"].' '.$option["size"].$option["unit"].'/'.$em.'em '.stripslashes($option["face"]).';color:'.$option["color"].';';
	}
}

// Output stylesheet and custom.css after custom styling
remove_action( 'wp_head', 'woothemes_wp_head' );
add_action( 'woo_head', 'woothemes_wp_head' );


/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/

add_filter( 'body_class','woo_layout_body_class', 10 );		// Add layout to body_class output

if ( ! function_exists( 'woo_layout_body_class' ) ) {
	function woo_layout_body_class( $classes ) {
		
		global $woo_options;
		
		$layout = 'two-col-left';
		
		if ( isset( $woo_options['woo_site_layout'] ) && ( $woo_options['woo_site_layout'] != '' ) ) {
			$layout = $woo_options['woo_site_layout'];
		}

		// Set main layout on post or page
		if ( is_singular() ) {
			global $post;
			$single = get_post_meta($post->ID, '_layout', true);
			if ( $single != "" AND $single != "layout-default" ) 
				$layout = $single;
		}
		
		// Add layout to $woo_options array for use in theme
		$woo_options['woo_layout'] = $layout;
		
		// Add classes to body_class() output 
		$classes[] = $layout;
		return $classes;						
					
	} // End woo_layout_body_class()
}


/*-----------------------------------------------------------------------------------*/
/* woo_feedburner_link() */
/*-----------------------------------------------------------------------------------*/
/**
 * woo_feedburner_link()
 *
 * Replace the default RSS feed link with the Feedburner URL, if one
 * has been provided by the user.
 *
 * @package WooFramework
 * @subpackage Filters
 */

add_filter( 'feed_link', 'woo_feedburner_link', 10 );

function woo_feedburner_link ( $output, $feed = null ) {

	global $woo_options;

	$default = get_default_feed();

	if ( ! $feed ) $feed = $default;

	if ( isset($woo_options[ 'woo_feed_url']) && $woo_options[ 'woo_feed_url' ] && ( $feed == $default ) && ( ! stristr( $output, 'comments' ) ) ) $output = esc_url( $woo_options[ 'woo_feed_url' ] );

	return $output;

} // End woo_feedburner_link()


/*-----------------------------------------------------------------------------------*/
/* END */
/*-----------------------------------------------------------------------------------*/
?>