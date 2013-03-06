<?php
/**
 * Template Name: Full Width
 *
 * This template is a full-width version of the page.php template file. It removes the sidebar area.
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options;
?>
       
    <div id="content" class="page col-full">
		<section id="main" class="fullwidth">
            
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
					                    
                    <section class="entry">
	                	<?php the_content(); ?>
	               	</section><!-- /.entry -->

					<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>

                </article><!-- /.post -->
                                                    
			<?php
					} // End WHILE Loop
				} else {
			?>
				<article <?php post_class(); ?>>
                	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
                </article><!-- /.post -->
            <?php } ?>  
        
		</section><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>