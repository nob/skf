<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Header Template
 *
 * Here we setup all logic and XHTML that is required for the header section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
 
 global $woo_options, $woocommerce;
 
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php woo_title( '' ); ?></title>
<?php woo_meta(); ?>
<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>" />
<?php
wp_head();
woo_head();
?>
</head>
<body <?php body_class(); ?>>
<?php woo_top(); ?>

<div id="wrapper">
    
    <?php woo_header_before(); ?>

	<header id="header">

		<div class="col-full">

			<?php woo_header_inside(); ?>

			<div class="header-left">
			    <hgroup>
					<span class="nav-toggle"><a href="#navigation"><span><?php _e( 'Navigation', 'woothemes' ); ?></span></a></span>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
				</hgroup>
			</div><!-- /.header-left -->
	        
	        <?php woo_nav_before(); ?>

	        <div class="header-right">
				<nav id="navigation" class="col-full" role="navigation">
					
					<?php
					if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) {
						wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
					} else {
					?>
			        <ul id="main-nav" class="nav fl">
						<?php if ( is_page() ) $highlight = 'page_item'; else $highlight = 'page_item current_page_item'; ?>
						<li class="<?php echo $highlight; ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Home', 'woothemes' ); ?></a></li>
						<?php wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); ?>
					</ul><!-- /#nav -->
			        <?php } ?>
			        <?php if ( is_woocommerce_activated() && isset( $woo_options['woocommerce_header_cart_link'] ) && 'true' == $woo_options['woocommerce_header_cart_link'] ) { ?>
			        	<ul class="nav cart fr">
			        		<li><a class="cart-contents" href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'woothemes' ); ?>"><span><?php echo $woocommerce->cart->cart_contents_count;?></span> <?php _e( 'Cart', 'woothemes' ); ?></a></li>
			       		</ul>
			        <?php } ?>
			
				</nav><!-- /#navigation -->
			</div><!-- /.header-right -->
			
			<?php woo_nav_after(); ?>
	
		</div><!-- /.col-full -->

	</header><!-- /#header -->

	<?php woo_content_before(); ?>