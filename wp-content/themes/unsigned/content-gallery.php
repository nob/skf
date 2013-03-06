<?php
/**
 * The default template for displaying content
 */

	global $woo_options, $woothemes_photos;
 
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
	    		woo_image( 'width=' . $settings['thumb_w'] . '&height=' . $settings['thumb_h'] . '&class=thumbnail ' . $settings['thumb_align'] ); 
	    	} 
	    ?>
	    
		<header>
			<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<?php woo_post_meta(); ?>
		</header>

		<section class="entry">
		<?php the_excerpt(); ?>
		</section>
		<section class="entry gallery-photos">
		<?php
			// Display photographs for this gallery.
			$query_args = array( 'limit' => 5, 'id' => get_the_ID() );
			$photos = $woothemes_photos->get_photos( $query_args );
			
			$html = '';
			
			if ( count( $photos ) > 0 ) {
				foreach ( $photos as $k => $v ) {
					$html .= '<a href="' . $v->guid . '" rel="lightbox[' . get_the_ID() . ']">' . woo_image( 'return=true&link=img&width=' . '50' . '&height=' . '50' . '&class=alignleft woo-photo-thumb&src=' . $v->guid ) . '</a>' . "\n";
				}
			} else {
				$html = '<p>' . __( 'No photos are currently listed.', 'woothemes' ) . '</p>' . "\n";
			}
			
			echo $html; 
		?>
		</section>

		<footer class="post-more">
			<span class="read-more"><a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'View Gallery &rarr;', 'woothemes' ); ?>"><?php _e( 'View Gallery &rarr;', 'woothemes' ); ?></a></span>
		</footer>   

	</article><!-- /.post -->