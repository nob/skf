<?php
/**
 * Single Video Template
 *
 * This template is the default template for "video" posts. It is used to display content when someone is viewing a
 * singular view of a post ('video' post_type).
 * @link http://codex.wordpress.org/Post_Types
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options, $woothemes_videos;
?>
       
    <div id="content" class="col-full">
		<section id="main" class="col-left">
		           
		<?php if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) { ?>
			<section id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</section><!--/#breadcrumbs -->
		<?php } ?>
        <?php
        	if ( have_posts() ) { $count = 0;
        		while ( have_posts() ) { the_post(); $count++;
        ?>
        
                <header>
	                <h1 class="out-box"><?php the_title(); ?></h1>
                </header>
        
			<article <?php post_class(); ?>>

				<?php woo_post_meta(); ?>

				<?php echo woo_embed( 'width=580' ); ?>
                
                <section class="entry">
                	<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
				</section>         
            </article><!-- .post -->
            <?php
				} // End WHILE Loop
			} else {
		?>
			<article <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
			</article><!-- .post -->             
       	<?php } ?>  
        
		</section><!-- #main -->

        <?php get_sidebar(); ?>

    </div><!-- #content -->
		
<?php get_footer(); ?>