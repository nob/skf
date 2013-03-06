<?php get_header(); ?>
<?php
	$obj = get_queried_object();
	$taxonomy = '';
	if ( isset( $obj->taxonomy ) ) {
		$taxonomy = $obj->taxonomy;
	}
?>
    <div id="content" class="col-full">
		<section id="main" class="col-left">
            
		<?php if ( $woo_options['woo_breadcrumbs_show'] == 'true' ) { ?>
			<section id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</section><!--/#breadcrumbs -->
		<?php } ?>  

		<?php if (have_posts()) : $count = 0; ?>
		<header class="archive_header"><h1><?php echo $obj->name; ?></h1></header>
        <?php
        	// Display the description for this archive, if it's available.
        	woo_archive_description();
        ?>
        
	        <div class="fix"></div>
        
			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); $count++; ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to overload this in a child theme then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_type() );
				?>

			<?php endwhile; ?>
            
        <?php else: ?>
        
            <article <?php post_class(); ?>>
                <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
            </article><!-- /.post -->
        
        <?php endif; ?>  
    
			<?php woo_pagenav(); ?>
                
		</section><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>