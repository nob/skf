<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Blog Posts Component
 *
 * Display X recent blog posts.
 *
 * @author Matty
 * @since 1.0.0
 * @package WooFramework
 * @subpackage Component
 */
$settings = array(
				'homepage_number_of_posts' => 5, 
				'homepage_posts_category' => '', 
				'thumb_w' => 100, 
				'thumb_h' => 100, 
				'thumb_align' => 'alignleft',
				'homepage_posts_display_sidebar' => 'true'
				);
					
$settings = woo_get_dynamic_values( $settings );

if ( get_query_var( 'paged') ) { $paged = get_query_var( 'paged' ); } elseif ( get_query_var( 'page') ) { $paged = get_query_var( 'page' ); } else { $paged = 1; }

$query_args = array(
						'post_type' => 'post', 
						'posts_per_page' => intval( $settings['homepage_number_of_posts'] ), 
						'paged' => $paged
					);

if ( 0 < intval( $settings['homepage_posts_category'] ) ) {
	$query_args['cat'] = intval( $settings['homepage_posts_category'] );
}
?>

<section class="blog-posts widget fix">
	<?php if ( "true" == $settings['homepage_posts_display_sidebar'] ) { $class = 'col-left'; } else { $class = 'fullwidth'; }  ?>
	<div id="main" class="<?php echo $class ?>">
<?php woo_loop_before(); ?>

<?php
	query_posts( $query_args );
	
	if ( have_posts() ) {
		$count = 0;
		while ( have_posts() ) { the_post(); $count++;

			/* Include the Post-Format-specific template for the content.
			 * If you want to overload this in a child theme then include a file
			 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
			 */
			get_template_part( 'content', get_post_format() );

		} // End WHILE Loop
	
	} else {
?>
    <article <?php post_class(); ?>>
        <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
    </article><!-- /.post -->
<?php } // End IF Statement ?> 

<?php woo_loop_after(); ?> 

<?php woo_pagenav(); ?>
<?php wp_reset_query(); ?>
	</div><!--/.col-left-->
<?php if ( "true" == $settings['homepage_posts_display_sidebar'] ) { get_sidebar(); } ?>
</section>
<div class="fix"></div>