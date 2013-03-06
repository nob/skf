<?php
/**
 * Header Template
 *
 * Here we setup all logic and XHTML that is required for the header section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
 
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
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />

<title><?php woo_title(); ?></title>
<?php woo_meta(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo esc_url( get_bloginfo( 'stylesheet_url' ) ); ?>" media="screen" />
<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>" />
<?php
	wp_head();
	woo_head();
?>
</head>

<body <?php body_class(); ?>>
<?php woo_top(); ?>
<?php
	if ( $settings['enable_slides'] == 'true' && is_front_page() ) {
		// Load the slider background images.
		get_template_part( 'includes/slider', 'background' );
	}
?>
<div id="wrapper">

	<div id="header-wrap">

	<?php if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) { ?>

	<div id="top">
		<nav class="col-full" role="navigation">
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
		</nav>
	</div><!-- /#top -->

    <?php } ?>
	
	<header id="header" class="col-full">
		
		<?php
		    $logo = get_template_directory_uri() . '/images/logo.png';
		    if ( isset( $woo_options['woo_logo'] ) && $woo_options['woo_logo'] != '' ) { $logo = $woo_options['woo_logo']; }
		    if ( is_ssl() ) { $logo = str_replace( 'http', 'https', $logo ); }
		?>
		<?php if ( ! isset( $woo_options['woo_texttitle'] ) || $woo_options['woo_texttitle'] != 'true' ) { ?>
		    <a id="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
		    	<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
		    </a>
	    <?php } ?>
	    
	    <hgroup>
	        
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		      	
		</hgroup>

		<?php if ( isset( $woo_options['woo_ad_top'] ) && $woo_options['woo_ad_top'] == 'true' ) { ?>
        <div id="topad">
			<?php
				if ( isset( $woo_options['woo_ad_top_adsense'] ) && $woo_options['woo_ad_top_adsense'] != '' ) {
					echo stripslashes( $woo_options['woo_ad_top_adsense'] );
				} else {
					if ( isset( $woo_options['woo_ad_top_url'] ) && isset( $woo_options['woo_ad_top_image'] ) ) {
						$top_ad_image = $woo_options['woo_ad_top_image'];
						if ( is_ssl() ) { $top_ad_image = str_replace( 'http', 'https', $top_ad_image ); }
			?>
				<a href="<?php echo esc_url( $woo_options['woo_ad_top_url'] ); ?>"><img src="<?php echo esc_url( $top_ad_image ); ?>" width="468" alt="advert" /></a>
			<?php } } ?>
		</div><!-- /#topad -->
        <?php } ?>

	<nav id="navigation" class="col-right" role="navigation">
		<?php
		if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) {
			wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
		} else {
		?>
        <ul id="main-nav" class="nav fl">
			<?php if ( is_page() ) $highlight = 'page_item'; else $highlight = 'page_item current_page_item'; ?>
			<li class="<?php echo $highlight; ?>"><a href="<?php echo home_url( '/' ); ?>"><?php _e( 'Home', 'woothemes' ); ?></a></li>
			<?php wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); ?>
		</ul><!-- /#nav -->
        <?php } ?>
	</nav><!-- /#navigation -->
	
	</header><!-- /#header -->
	
	</div><!-- /#header-wrap -->
