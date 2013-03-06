<?php
/**
 * Single Gallery Template
 *
 * This template is the default template for "gallery" posts. It is used to display content when someone is viewing a
 * singular view of a post ('gallery' post_type).
 * @link http://codex.wordpress.org/Post_Types
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options, $woothemes_photos;
	
/**
 * The Variables
 *
 * Setup default variables, overriding them if the "Theme Options" have been saved.
 */
	
	$settings = array(
					'thumb_single' => 'false', 
					'single_w' => 200, 
					'single_h' => 200, 
					'thumb_single_align' => 'alignright'
					);
					
	$settings = woo_get_dynamic_values( $settings );
?>
       
    <div id="content" class="col-full">
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
			
				<?php woo_post_meta(); ?>

				<?php echo woo_embed( 'width=580' ); ?>
				
				<?php
                	if ( $settings['thumb_single'] == 'true' && ! woo_embed( '' ) ) {
						$image = woo_image( 'return=true&width=' . $settings['single_w'] . '&height=' . $settings['single_h'] . '&link=img&class=thumbnail rounded ' . $settings['thumb_single_align'] );
												
						if ( $image != '' ) {
				?>
				
					<?php echo $image; ?>
				
            	<?php
            				}
            			}
           		?>
                
                <section class="entry">
                	<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
				</section>
				
				<div class="fix"></div>
				
				<section class="entry gallery-photos">
				<?php
					// Display photographs for this gallery.
					$query_args = array( 'limit' => -1, 'id' => get_the_ID() );
					$photos = $woothemes_photos->get_photos( $query_args );

					$html = '';
					
					if ( count( $photos ) > 0 ) {
						foreach ( $photos as $k => $v ) {
							$html .= '<a href="' . $v->guid . '" rel="lightbox[single-' . get_the_ID() . ']" title="' . esc_attr( $v->post_excerpt ) . '">' . woo_image( 'return=true&link=img&width=' . $width . '&height=' . $height . '&class=alignleft rounded woo-photo-thumb&src=' . $v->guid ) . '</a>' . "\n";
						}
					} else {
						$html = '<p>' . __( 'No photos are currently listed.', 'woothemes' ) . '</p>' . "\n";
					}
					
					echo $html; 
				?>
				</section>         
				
				<div class="fix"></div>

            </article><!-- .post -->
						
	        <nav id="post-entries" class="fix">
	            <div class="nav-prev fl"><?php previous_post_link( '%link', '<span class="meta-nav">&larr;</span> %title' ); ?></div>
	            <div class="nav-next fr"><?php next_post_link( '%link', '%title <span class="meta-nav">&rarr;</span>' ); ?></div>
	        </nav><!-- #post-entries -->
            <?php
				} // End WHILE Loop
			} else {
		?>
			<article <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
			</article><!-- .post -->             
       	<?php } ?>  
        
		</section><!-- #main -->

        <?php get_sidebar(); ?>

    </div><!-- #content -->
		
<?php get_footer(); ?>