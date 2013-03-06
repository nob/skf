<?php
/**
 * Template Name: Tours
 *
 * The tours page template displays a user-friendly list of the
 * tours listed on your website.
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options, $woothemes_events;
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
				<section class="entry tours">
					<?php
		            	// Begin tours display.
		            	$tours = $woothemes_events->get_tours( -1 );
		            	
		            	if ( count( $tours ) > 0 ) {
		            ?>
		            	<table class="tours-grid">
		            	<thead>
		            		<th><?php _e( 'Name', 'woothemes' ); ?></th>
		            		<th><?php _e( 'Dates', 'woothemes' ); ?></th>
		            	</thead>
		            <?php
		            	foreach ( $tours as $k => $v ) {
		            ?>
		            	<tr>
		            		<td><?php echo '<a href="' . get_term_link( $v, 'event_tour' ) . '" title="' . esc_attr( $v->name ) . '">' . $v->name . '</a>'; ?></td>
		            		<td><?php echo $v->tour_dates['start'] . ' ' . __( 'to', 'woothemes' ) . ' ' . $v->tour_dates['end']; ?></td>
		            	</tr>
		            <?php
		            	}
		            ?>
		            	</table>
		            <?php
		            	} else {
		            		_e( 'No tours currently listed.', 'woothemes' );
		            	}
		            ?>
				</section>
				<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
                
            </article><!-- /.post -->
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