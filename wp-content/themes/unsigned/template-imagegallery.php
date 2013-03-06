<?php
/**
 * Template Name: Image Gallery
 *
 * The image gallery page template displays a styled
 * image grid of a maximum of 60 posts with images attached.
 *
 * @package WooFramework
 * @subpackage Template
 */
 
 get_header();
?>
       
    <div id="content" class="page col-full">
		<section id="main" class="col-left fix">
                                                                            
		<?php if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) { ?>
			<section id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</section><!--/#breadcrumbs -->
		<?php } ?>  
		
                <header>
	                <h1 class="out-box"><?php the_title(); ?></h1>
                </header>

            <article <?php post_class('image-gallery-item'); ?>>
				                
				<section class="entry">

		            <?php
		            	if ( have_posts() ) { the_post();
		            		the_content();
		            	}
		            ?>
               		<?php query_posts( 'showposts=60' ); ?>
                	<?php
                		if ( have_posts() ) {
                			while ( have_posts() ) { the_post();
                			$wp_query->is_home = false;
                				woo_image( 'single=true&class=thumbnail alignleft rounded' );
                			}
                		}
                	?>	
                </section>
			
			<div class="fix"></div>
			
            </article><!-- /.post -->                
                                                            
		</section><!-- /#main -->
		
        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>