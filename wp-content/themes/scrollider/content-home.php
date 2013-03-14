<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php
/**
 * The default template for displaying content
 */

	global $woo_options;
 
/**
 * The Variables
 *
 * Setup default variables, overriding them if the "Theme Options" have been saved.
 */

 	$settings = array(
					'thumb_w' => 100, 
					'thumb_h' => 100, 
					'thumb_align' => 'alignleft'
					);
					
	$settings = woo_get_dynamic_values( $settings );
 
?>

	<article <?php post_class(); ?>>
	
	    <?php 
	    	if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] != 'content' ) { 
	    		woo_image( 'width=500&noheight=true&class=thumbnail&single=true ' . $settings['thumb_align'] ); 
	    	} 
	    ?>

	    <div class="article-inner">
	    
			<header>
				<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<?php //woo_post_meta(); ?>
			</header>

			<section class="entry">
				<?php
					$slide_excerpt = get_the_excerpt();
					if ($slide_excerpt != '') {
						echo '<p>'.woo_text_trim ( $slide_excerpt, 20 ).'</p>';
					}
				?>
			</section>

			<footer class="post-more">      
			<?php if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] == 'excerpt' ) { ?>				
				<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="edit">', ' <span class="post-more-sep">&bull;</span></span>' ); ?>
				<span class="read-more"><a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( '続きを読む &rarr;', 'woothemes' ); ?>"><?php _e( '続きを読む &rarr;', 'woothemes' ); ?></a></span>
			<?php } ?>
			</footer>

		</div><!-- /.article-inner -->

	</article><!-- /.post -->
