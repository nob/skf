<?php
/**
 * The default template for displaying band members
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
	    	woo_image( 'width=' . $settings['thumb_w'] . '&height=' . $settings['thumb_h'] . '&class=rounded ' . $settings['thumb_align'] );
	    ?>
	    
		<header>
			<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<?php woo_post_meta(); ?>
		</header>

		<section class="entry">
		<?php if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] == 'content' ) { the_content( __( 'Continue Reading &rarr;', 'woothemes' ) ); } else { the_excerpt(); } ?>
		</section>
<?php
	// Only display this footer section of the entry is a "post" or "page"
	if ( in_array( get_post_type(), array( 'post', 'page' ) ) ) {
?>
		<footer class="post-more">      
		<?php if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] == 'excerpt' ) { ?>
			<span class="comments"><?php comments_popup_link( __( 'Leave a comment', 'woothemes' ), __( '1 Comment', 'woothemes' ), __( '% Comments', 'woothemes' ) ); ?></span>
			<span class="post-more-sep">&bull;</span>
			<span class="read-more"><a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'Continue Reading &rarr;', 'woothemes' ); ?>"><?php _e( 'Continue Reading &rarr;', 'woothemes' ); ?></a></span>
		<?php } ?>
		</footer>   
<?php
	}
?>
	<div class="fix"></div>
	
	</article><!-- /.post -->