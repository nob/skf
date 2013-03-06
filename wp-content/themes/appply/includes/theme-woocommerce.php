<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $woo_options;

// Disable WooCommerce styles
define( 'WOOCOMMERCE_USE_CSS', false );

// Load WooCommerce stylsheet
if ( ! is_admin() ) { add_action( 'wp_enqueue_scripts', 'woo_load_woocommerce_css', 20 ); }

if ( ! function_exists( 'woo_load_woocommerce_css' ) ) {
	function woo_load_woocommerce_css () {
		wp_register_style( 'woocommerce', esc_url( get_template_directory_uri() . '/css/woocommerce.css' ) );
		wp_enqueue_style( 'woocommerce' );
	} // End woo_load_woocommerce_css()
}

/*-----------------------------------------------------------------------------------*/
/* Products */
/*-----------------------------------------------------------------------------------*/

// Number of columns on product archives
add_filter( 'loop_shop_columns', 'wooframework_loop_columns' );
if ( ! function_exists( 'wooframework_loop_columns' ) ) {
	function wooframework_loop_columns() {
		global $woo_options;
		if ( ! isset( $woo_options['woocommerce_product_columns'] ) ) {
			$cols = 3;
		} else {
			$cols = $woo_options['woocommerce_product_columns'] + 2;
		}
		return $cols;
	} // End wooframework_loop_columns()
}

// Number of products per page
add_filter( 'loop_shop_per_page', 'wooframework_products_per_page' );

if ( ! function_exists( 'wooframework_products_per_page' ) ) {
	function wooframework_products_per_page() {
		global $woo_options;
		if ( isset( $woo_options['woocommerce_products_per_page'] ) ) {
			return $woo_options['woocommerce_products_per_page'];
		}
	} // End wooframework_products_per_page()
}

// Display product tabs?
add_action( 'wp_head','wooframework_tab_check' );
if ( ! function_exists( 'wooframework_tab_check' ) ) {
	function wooframework_tab_check() {
		global $woo_options;
		if ( isset( $woo_options['woocommerce_product_tabs'] ) && 'false' == $woo_options['woocommerce_product_tabs'] ) { 
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		}
	} // End wooframework_tab_check()
}

// Display related products
add_action( 'wp_head','wooframework_related_products' );
if ( ! function_exists( 'wooframework_related_products' ) ) {
	function wooframework_related_products() {
		global $woo_options;
		if ( isset( $woo_options['woocommerce_related_products'] ) &&  'false' == $woo_options['woocommerce_related_products'] ) { 
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
		}
	} // End wooframework_related_products()
}

// Change number of related products
if ( ! function_exists( 'wooframework_related_products' ) ) {
	function woocommerce_output_related_products() {
		woocommerce_related_products(3,3); // Display 3 products in rows of 3
	}
}
// Custom place holder
add_filter( 'woocommerce_placeholder_img_src', 'wooframework_wc_placeholder_img_src' );

if ( ! function_exists( 'wooframework_wc_placeholder_img_src' ) ) {
	function wooframework_wc_placeholder_img_src( $src ) {
		global $woo_options;
		if ( isset( $woo_options['woo_placeholder_url'] ) && '' != $woo_options['woo_placeholder_url'] ) {   
			$src = $woo_options['woo_placeholder_url'];
		}
		else {
			$src = get_template_directory_uri() . '/images/wc-placeholder.gif';
		}
		return esc_url( $src );
	} // End wooframework_wc_placeholder_img_src()
}

// If theme lightbox is enabled, disable the WooCommerce lightbox and make product images prettyPhoto galleries
add_action( 'wp_footer', 'woocommerce_prettyphoto' );
function woocommerce_prettyphoto() {
	global $woo_options;
	if ( $woo_options[ 'woo_enable_lightbox' ] == "true" ) {
		update_option( 'woocommerce_enable_lightbox', false );
		?>
			<script>
				jQuery(document).ready(function(){
					jQuery('.images a').attr('rel', 'prettyPhoto[product-gallery]');
				});
			</script>
		<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Layout */
/*-----------------------------------------------------------------------------------*/

// Adjust markup on all woocommerce pages
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'woocommerce_theme_before_content', 10 );
add_action( 'woocommerce_after_main_content', 'woocommerce_theme_after_content', 20 );

if ( ! function_exists( 'woocommerce_theme_before_content' ) ) {
	function woocommerce_theme_before_content() {
		global $woo_options;
		if ( ! isset( $woo_options['woocommerce_product_columns'] ) ) {
			$columns = 'woocommerce-columns-3';
		} else {
			$columns = 'woocommerce-columns-' . ( $woo_options['woocommerce_product_columns'] + 2 );
		}
		?>
		<!-- #content Starts -->
	    <div id="content" class="col-full <?php echo esc_attr( $columns ); ?>">
			
	        <!-- #main Starts -->
	        <?php woo_main_before(); ?>
	        <div id="main" class="col-left">
	        
	    <?php
	} // End woocommerce_theme_before_content()
}


if ( ! function_exists( 'woocommerce_theme_after_content' ) ) {
	function woocommerce_theme_after_content() {
		?>
		
			</div><!-- /#main -->
	        <?php woo_main_after(); ?>
	    
	    </div><!-- /#content -->
		<?php woo_content_after(); ?>
	    <?php
	} // End woocommerce_theme_after_content()
}

/* Loop */
add_action('woocommerce_before_shop_loop_item_title', 'appply_before_shop_loop_item_title'); 
if ( ! function_exists( 'appply_before_shop_loop_item_title' ) ) {
	function appply_before_shop_loop_item_title() {
		?>
			<div class="more-info">
		<?php
	}
}
add_action('woocommerce_after_shop_loop_item_title', 'appply_after_shop_loop_item_title');
if ( ! function_exists( 'appply_after_shop_loop_item_title' ) ) {
	function appply_after_shop_loop_item_title() {
		?>
			</div><!-- /.more-info -->
		<?php
	}
}

// Header search form
//add_action( 'woo_nav_before', 'woocommerce_search_widget', 30 );

function woocommerce_search_widget() {
	global $woo_options;
	if ( isset( $woo_options['woocommerce_header_search_form'] ) && 'true' == $woo_options['woocommerce_header_search_form'] ) {
		the_widget('WooCommerce_Widget_Product_Search', 'title=' );
	}
} // End woocommerce_search_widget()

// Remove WC breadcrumb (we're using the WooFramework breadcrumb)
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

// Remove WC sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Add the WC sidebar in the right place and remove it from shop archives if specified
add_action( 'woo_main_after', 'woocommerce_get_sidebar', 10 );

if ( ! function_exists( 'woocommerce_get_sidebar' ) ) {
	function woocommerce_get_sidebar() {
		global $woo_options;
		
		// Don't display the sidebar on the full width template and homepage
		// Display the sidebar if full width option is disabled on archives
		if ( isset( $woo_options['woocommerce_archives_fullwidth'] ) && 'false' == $woo_options['woocommerce_archives_fullwidth'] && is_archive( array( 'product' ) ) ) {
			get_sidebar('');
		}

		if ( is_singular('product') ) {
			global $post;
			$single = get_post_meta($post->ID, '_layout', true);
			if ( $single != 'layout-full' )
				get_sidebar('');
		}

	} // End woocommerce_get_sidebar()
}

// Shop archives full width?

// Only display sidebar on product archives if instructed to do so via woocommerce_archives_fullwidth
if ( ! function_exists( 'woocommerce_get_sidebar' ) ) {
	function woocommerce_get_sidebar() {
		global $woo_options;
		
		if ( ! is_woocommerce() ) {
			get_sidebar();
		} elseif ( isset( $woo_options['woocommerce_archives_fullwidth'] ) && 'false' == $woo_options['woocommerce_archives_fullwidth'] && ( is_woocommerce() || is_product() ) ) {
			get_sidebar();
		} elseif ( isset( $woo_options['woocommerce_archives_fullwidth'] ) && 'true' == $woo_options['woocommerce_archives_fullwidth'] && is_archive( array( 'product' ) ) ) {
			// no sidebar
		}
	} // End woocommerce_get_sidebar()
}

// Remove pagination (we're using the WooFramework default pagination)
remove_action( 'woocommerce_pagination', 'woocommerce_pagination', 10 );
add_action( 'woocommerce_pagination', 'woocommerceframework_pagination', 10 );

if ( ! function_exists( 'woocommerceframework_pagination' ) ) {
function woocommerceframework_pagination() {
	if ( is_search() && is_post_type_archive() ) {
		add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 );
		add_filter( 'woo_pagination_args_defaults', 'woocommerceframework_woo_pagination_defaults', 10 );
	}
	woo_pagination();
} // End woocommerceframework_pagination()
}

if ( ! function_exists( 'woocommerceframework_add_search_fragment' ) ) {
function woocommerceframework_add_search_fragment ( $settings ) {
	$settings['add_fragment'] = '&post_type=product';
	
	return $settings;	
} // End woocommerceframework_add_search_fragment()
}

if ( ! function_exists( 'woocommerceframework_woo_pagination_defaults' ) ) {
function woocommerceframework_woo_pagination_defaults ( $settings ) {
	$settings['use_search_permastruct'] = false;
	
	return $settings;	
} // End woocommerceframework_woo_pagination_defaults()
}

// Add wrapping div around pagination
add_action( 'woocommerce_pagination', 'woocommerce_pagination_wrap_open', 5 );
add_action( 'woocommerce_pagination', 'woocommerce_pagination_wrap_close', 25 );

if ( ! function_exists( 'woocommerce_pagination_wrap_open' ) ) {
	function woocommerce_pagination_wrap_open() {
		echo '<section class="pagination-wrap">';
	} // End woocommerce_pagination_wrap_open()
}

if ( ! function_exists( 'woocommerce_pagination_wrap_close' ) ) {
	function woocommerce_pagination_wrap_close() {
		echo '</section>';
	} // End woocommerce_pagination_wrap_close()
}

// Add a class to the body if full width shop archives are specified
add_filter( 'body_class','wooframework_layout_body_class', 10 );		// Add layout to body_class output
if ( ! function_exists( 'wooframework_layout_body_class' ) ) {
	function wooframework_layout_body_class( $wc_classes ) {
		global $woo_options;
		
		$layout = '';
		
		// Add woocommerce-fullwidth class if full width option is enabled
		if ( isset( $woo_options['woocommerce_archives_fullwidth'] ) && 'true' == $woo_options['woocommerce_archives_fullwidth'] && ( is_shop() || is_product_category() ) ) {
			$layout = 'layout-full';
		}
		
		// Add classes to body_class() output 
		$wc_classes[] = $layout;
		return $wc_classes;		
	} // End woocommerce_layout_body_class()
}

// Ensure cart contents update when products are added to the cart via AJAX
add_filter( 'add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );

if ( ! function_exists( 'woocommerce_header_add_to_cart_fragment' ) ) {
	function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;

		ob_start();
?>
		<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>"><span><?php echo $woocommerce->cart->cart_contents_count; ?></span> <?php _e('Cart', 'woothemes'); ?></a>
<?php

		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	} // End woocommerce_header_add_to_cart_fragment()
}

?>