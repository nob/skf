<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
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
 
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<meta charset="<?php bloginfo( 'charset' ); ?>" />

<title><?php woo_title(''); ?></title>
<?php woo_meta(); ?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_url' ); ?>" media="screen" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	wp_head();
	woo_head();
?>

</head>

<body <?php body_class(); ?>>
<?php woo_top(); ?>


    
    <?php woo_header_before(); ?>

    <div id="content-top">

	<?php if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) { ?>

	<div id="top">
		<nav class="col-full" role="navigation">
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
		</nav>
	</div><!-- /#top -->

    <?php } ?>

	<header id="header">

		<div class="col-full">
			<div id="logo">

				<?php
					$dynamic_logo = ( is_home() || is_front_page() ) ? '/images/logo-home.png' : '/images/logo.png';
				    $logo = esc_url( get_template_directory_uri() . $dynamic_logo );
				    if ( isset( $woo_options['woo_logo'] ) && $woo_options['woo_logo'] != '' ) { $logo = $woo_options['woo_logo']; }
				?>
				<?php if ( ! isset( $woo_options['woo_texttitle'] ) || $woo_options['woo_texttitle'] != 'true' ) { ?>
				    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
				    	<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
				    </a>
			    <?php } ?>
			    
			    <hgroup>
			        
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>

				</hgroup>

			</div><!-- /#logo -->
	        
	        <h3 class="nav-toggle"><a href="#navigation">&#9776; <span><?php _e('Navigation', 'woothemes'); ?></span></a></h3>

	        <?php woo_nav_before(); ?>

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
		
			</nav><!-- /#navigation -->
			
			<?php woo_nav_after(); ?>

		</div><!-- /.col-full -->
	
	</header><!-- /#header -->

	<?php
	// Output the Features Area
	$settings = array(
				'featured' => 'true'
				);
	$settings = woo_get_dynamic_values( $settings );
	if ( ( is_front_page() || is_home() ) && ( isset($settings['featured']) && $settings['featured'] == 'true' ) ) { get_template_part( 'includes/featured' ); } 
    else {
	?>
    <h1 style="z-index: 1;"><?php the_title();?></h1>
    <div id="custom-bg">
        <img src="http://skfine.jointanet.com/wp-content/uploads/2013/03/Peppers-2560x2733.jpg" alt="" width="2560" class="woo-image slide-image full">
    </div>
    <?php
    }
    ?>

	</div><!-- /#content-top -->

	<div id="wrapper">
		
	<?php woo_content_before(); ?>
