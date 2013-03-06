<?php
/**
 * Template Name: Band Biography
 *
 * The tours page template displays the page content with a user-friendly
 * list of band members listed on your website.
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options, $post, $woothemes_bandmembers;
?>
       
    <div id="content" class="page col-full">
		<section id="main" class="col-left">
		           
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

					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
               	</section><!-- /.entry -->
				 
				<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
                
            </article><!-- /.post -->
            
            <section class="entry band-members">
					<?php
		            	// Begin band members display.
		            	$band_members = $woothemes_bandmembers->get_band_members( array( 'limit' => -1 ) );
		            	
		            	if ( count( $band_members ) > 0 ) {
		            		$saved_post = $post;
		            		
		            		foreach ( $band_members as $k => $post ) {
		            			setup_postdata( $post );
		            			
		            			get_template_part( 'content', 'band_member' );
		            		}
							
							$post = $saved_post;
		            	} else {
		            		_e( 'No band members currently listed.', 'woothemes' );
		            	}
		            ?>
				</section>
            <?php
            	// Determine wether or not to display comments here, based on "Theme Options".
            	if ( isset( $woo_options['woo_comments'] ) && in_array( $woo_options['woo_comments'], array( 'page', 'both' ) ) ) {
            		comments_template();
            	}

				} // End WHILE Loop
			} else {
		?>
			<article <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
            </article><!-- /.post -->
        <?php } // End IF Statement ?>  
        
		</section><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>