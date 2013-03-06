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
	    	woo_image( 'width=' . $settings['thumb_w'] . '&height=' . $settings['thumb_h'] . '&class=thumbnail ' . $settings['thumb_align'] );  
	    ?>
	    
		<header>
			<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<?php woo_post_meta(); ?>
		</header>

		<section class="entry">
			<?php the_excerpt(); ?>
		</section>

	</article><!-- /.post -->