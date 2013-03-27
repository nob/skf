<?php
// File Security Check
if ( ! function_exists( 'wp' ) && ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?><?php
/**
 * Index Template
 *
 * Here we setup all logic and XHTML that is required for the index template, used as both the homepage
 * and as a fallback template, if a more appropriate template file doesn't exist for a specific context.
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();

	/**
 	* The Variables
 	*
 	* Setup default variables, overriding them if the "Theme Options" have been saved.
 	*/
	
	$settings = array(
					'features_area' => 'true',
					'blog_area' => 'true',
					//'widget_area_message' => 'This is a heading for the widgetized regions below'
					'widget_area_message' => ''
					);
					
	$settings = woo_get_dynamic_values( $settings );
	if ( get_query_var( 'page' ) > 1) { $paged = get_query_var( 'page' ); } elseif ( get_query_var( 'paged' ) > 1) { $paged = get_query_var( 'paged' ); } else { $paged = 1; } 
	
?>

    <div id="content">


    	<div class="col-full">

		<?php
			if ( ( is_active_sidebar( 'homepage-1' ) ||
				   is_active_sidebar( 'homepage-2' ) ||
				   is_active_sidebar( 'homepage-3' ) ) 
				) {
		?>
			<header class="section-title">
<!--
				<p><span><?php echo $settings['widget_area_message']; ?></span></p>
-->
				<p><span></span></p>
			</header>

			<div id="home-widgets" class="<?php echo esc_attr( 'columns-' . woo_get_homepage_column_count() ); ?>">

				<?php $i = 0; while ( $i < 3 ) { $i++; ?>
					<?php if ( is_active_sidebar( 'homepage-' . $i ) ) { ?>

					<div class="block home-widget-<?php echo $i; ?>">
						<?php dynamic_sidebar( 'homepage-' . $i ); ?>
					</div>

					<?php } ?>
				<?php } // End WHILE Loop ?>

				<div class="fix"></div>

			</div><!-- /#home-widgets -->

		<?php } ?>		

		</div><!-- /.col-full -->

    </div><!-- /#content -->

	<?php if ( is_active_sidebar( 'homepage-full' ) ) { $home_content = true; ?>
	<div id="home-widget-fullwidth" class="col-full">
		<?php dynamic_sidebar( 'homepage-full' ); ?>
	</div>
		<?php
			// Output the Features Area	
			if ( ( $paged == 1 ) && $settings['features_area'] == 'true' ) { get_template_part( 'includes/home-panel-features' ); } 
		?>
	<?php } ?>
	<?php if ( ! is_active_sidebar( 'homepage-full' ) ) { get_template_part( 'includes/home-panel-default' ); } ?>
<?php get_footer(); ?>
