<?php
/*-----------------------------------------------------------------------------------*/
/* This theme supports WooCommerce, woo! */
/*-----------------------------------------------------------------------------------*/

add_theme_support( 'woocommerce' );

/*-----------------------------------------------------------------------------------*/
/* Any WooCommerce overrides can be found here
/*-----------------------------------------------------------------------------------*/

// Disable WooCommerce styles
define( 'WOOCOMMERCE_USE_CSS', false );

// Change columns in product loop to 4
function loop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'loop_columns' );

// Display 16 products per page
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 16;' ) );

// Remove the add to cart button from the product loop
//remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10, 2);



// Adjust markup on all WooCommerce pages
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', 'unsigned_before_content', 10 );
add_action( 'woocommerce_after_main_content', 'unsigned_after_content', 20 );

// Fix the layout etc
if ( !function_exists( 'unsigned_before_content' ) ) {
	function unsigned_before_content() {
		global  $woo_options;
?>
 <div id="content" class="page col-full">
		<section id="main" class="col-left">
		           
		<?php if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) { ?>
			<section id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</section><!--/#breadcrumbs -->
		<?php } ?>
	    <?php
	}
}

if ( !function_exists( 'unsigned_after_content' ) ) {
	function unsigned_after_content() {
?>
			<?php if ( is_search() && is_post_type_archive() ) { add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 ); } ?>
			<?php woo_pagenav(); ?>
			</section><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
	    <?php
	}
}


// Remove breadcrumb (we're using the WooFramework default breadcrumb)
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

// Remove pagination (we're using the WooFramework default pagination)
remove_action( 'woocommerce_pagination', 'woocommerce_pagination', 10 );

function woocommerceframework_pagination() {
	if ( is_search() && is_post_type_archive() ) {
		add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 );
	}
	woo_pagenav();
}

function woocommerceframework_add_search_fragment ( $settings ) {
	$settings['add_fragment'] = '?post_type=product';
	return $settings;
} // End woocommerceframework_add_search_fragment()

// Change columns in related products output to 3 and move below the product summary
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );

if ( !function_exists( 'woocommerce_output_related_products' ) ) {
	function woocommerce_output_related_products() {
		woocommerce_related_products( 3, 3 ); // 3 products, 3 columns
	}
}

// Change columns in upsells output to 3 and move below the product summary
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product', 'woocommerceframework_upsell_display', 20 );

if ( !function_exists( 'woocommerceframework_upsell_display' ) ) {
	function woocommerceframework_upsell_display() {
		woocommerce_upsell_display( 3, 3 ); // 3 products, 3 columns
	}
}

// Adjust the star rating in the sidebar
add_filter( 'woocommerce_star_rating_size_sidebar', 'woostore_star_sidebar' );

if ( !function_exists( 'woostore_star_sidebar' ) ) {
	function woostore_star_sidebar() {
		return 12;
	}
}

// Adjust the star rating in the recent reviews
add_filter( 'woocommerce_star_rating_size_recent_reviews', 'woostore_star_reviews' );

if ( !function_exists( 'woostore_star_reviews' ) ) {
	function woostore_star_reviews() {
		return 12;
	}
}

// Sticky shortcode
function woo_shortcode_sticky( $atts, $content = null ) {
	extract( shortcode_atts( array(
				'class' => '',
			), $atts ) );

	return '<div class="shortcode-sticky ' . esc_attr( $class ) . '">' . $content . '</div><!--/shortcode-sticky-->';
}

add_shortcode( 'sticky', 'woo_shortcode_sticky' );

// Sale shortcode
function woo_shortcode_sale ( $atts, $content = null ) {
	$defaults = array();
	extract( shortcode_atts( $defaults, $atts ) );
	return '<div class="shortcode-sale"><span>' . $content . '</span></div><!--/.shortcode-sale-->';
}

add_shortcode( 'sale', 'woo_shortcode_sale' );

// Add image wrap
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_product_thumbnail_wrap_open', 5, 2 );

if ( !function_exists( 'woocommerce_product_thumbnail_wrap_open' ) ) {
	function woocommerce_product_thumbnail_wrap_open() {
		echo '<div class="img-wrap">';
	}
}

// Close image wrap
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_product_thumbnail_wrap_close', 15, 2 );

if ( !function_exists( 'woocommerce_product_thumbnail_wrap_close' ) ) {
	function woocommerce_product_thumbnail_wrap_close() {
		echo '</div> <!--/.wrap-->';
	}
}
?>