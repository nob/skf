<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php
/**
 * Single Post Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a post ('post' post_type).
 * @link http://codex.wordpress.org/Post_Types#Post
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options;
	
/**
 * The Variables
 *
 * Setup default variables, overriding them if the "Theme Options" have been saved.
 */
	
	$settings = array(
					'thumb_single' => 'false', 
					'single_w' => 200, 
					'single_h' => 200, 
					'thumb_single_align' => 'alignright'
					);
					
	$settings = woo_get_dynamic_values( $settings );
?>
       
    <div id="content" class="col-full">
    
    	<?php woo_main_before(); ?>
    	
		<section id="main" class="col-left">
		           
        <?php
        	if ( have_posts() ) { $count = 0;
        		while ( have_posts() ) { the_post(); $count++;
        ?>
			<article <?php post_class(); ?>>

				<?php echo woo_embed( 'width=580' ); ?>
                <?php if ( $settings['thumb_single'] == 'true' && ! woo_embed( '' ) ) { woo_image( 'width=' . $settings['single_w'] . '&height=' . $settings['single_h'] . '&class=thumbnail ' . $settings['thumb_single_align'] ); } ?>

                <div class="article-inner">

	                <header>
	                
		                <h1><?php the_title(); ?></h1>
		                
	                	<?php woo_post_meta(); ?>
	                	
	                </header>

	                <section class="entry fix">
	                	<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
					</section>
										
					<?php the_tags( '<p class="tags">'.__( 'Tags: ', 'woothemes' ), ', ', '</p>' ); ?>
	            
				</div><!-- /.article-inner -->


		        <nav id="post-entries" class="fix">
		            <div class="nav-prev fl"><?php previous_post_link( '%link', '<span class="meta-nav">&larr;</span> %title' ); ?></div>
		            <?php woo_single_post_share_bar( ); ?>
		            <div class="nav-next fr"><?php next_post_link( '%link', '%title <span class="meta-nav">&rarr;</span>' ); ?></div>
		        </nav><!-- #post-entries -->

				<?php if ( isset( $woo_options['woo_post_author'] ) && $woo_options['woo_post_author'] == 'true' ) { ?>
				<aside id="post-author" class="fix">
					<div class="profile-content">
						<h3 class="title"><span><?php _e( 'Author:', 'woothemes' ); ?></span><?php printf( esc_attr__( 'About %s', 'woothemes' ), get_the_author() ); ?></h3>
						<p><?php the_author_meta( 'description' ); ?></p>
						<div class="author-links">
							<?php $site_url = get_the_author_meta( 'user_url' ); if ( isset($site_url) && get_the_author_meta( 'user_url' ) != '' ): ?>
							<a href="<?php the_author_meta( 'user_url' ); ?>" class="website">
								<?php printf( __( 'View Website', 'woothemes' ), get_the_author() ); ?>
							</a>
							<?php endif; ?>
							<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="profile">
								<?php printf( __( 'View other posts', 'woothemes' ), get_the_author() ); ?>
							</a>
						</div><!-- #author-link -->
					</div><!-- .post-entries -->
				</aside><!-- .post-author-box -->
				<?php } ?>

				<?php woo_subscribe_connect(); ?>

            </article><!-- .post -->

            <?php
            	// Determine wether or not to display comments here, based on "Theme Options".
            	if ( isset( $woo_options['woo_comments'] ) && in_array( $woo_options['woo_comments'], array( 'post', 'both' ) ) ) {
            		comments_template();
            	}

				} // End WHILE Loop
			} else {
		?>
			<article <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
			</article><!-- .post -->             
       	<?php } ?>  
        
		</section><!-- #main -->
		
		<?php woo_main_after(); ?>

        <?php get_sidebar(); ?>

    </div><!-- #content -->

    <?php if ( woo_active_sidebar( 'single-full' ) ) { ?>

	<div id="single-widget-fullwidth" class="col-full">
		<?php woo_sidebar( 'single-full' ); ?>
	</div>

	<?php } ?>	
		
<?php get_footer(); ?>