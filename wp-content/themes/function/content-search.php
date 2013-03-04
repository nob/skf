<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The default template for displaying content
 */

	global $woo_options;
?>

	<article <?php post_class(); ?>>
	
		<header>
			<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
		</header>

	    <?php 
	    	if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] != 'content' ) { 
	    		woo_image( 'width=' . $settings['thumb_w'] . '&height=' . $settings['thumb_h'] . '&class=thumbnail ' . $settings['thumb_align'] ); 
	    	}
	    ?>
    	  
	    <div class="fix"></div>

	    <?php woo_post_meta(); ?>
	    
	    <div class="article-inner">
			<section class="entry">
				<?php the_excerpt(); ?>
			</section>
		</div><!-- /.article-inner -->

	</article><!-- /.post -->