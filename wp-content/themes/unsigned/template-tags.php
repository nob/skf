<?php
/**
 * Template Name: Tags
 *
 * The tags page template displays a user-friendly tag cloud of the
 * post tags used on your website.
 *
 * @package WooFramework
 * @subpackage Template
 */

 global $woo_options; 
 get_header();
?>
       
    <div id="content" class="page col-full">
		<section id="main" class="fullwidth">
            
		<?php if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) { ?>
			<section id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</section><!--/#breadcrumbs -->
		<?php } ?>  
                    
                <header>
	                <h1 class="out-box"><?php the_title(); ?></h1>
                </header>
                                                                        
            <article <?php post_class(); ?>>
				                
	            <?php if ( have_posts() ) { the_post(); ?>
            	<section class="entry">
            		<?php the_content(); ?>
            	</section>	            	
	            <?php } ?>  
	            
                <div class="tag_cloud">
        			<?php wp_tag_cloud( 'number=0' ); ?>
    			</div><!--/.tag-cloud-->

            </article><!-- /.post -->
        
		</section><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>