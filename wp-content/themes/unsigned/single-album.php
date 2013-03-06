<?php
/**
 * Single Album Template
 *
 * This template is the default template for "album" posts. It is used to display content when someone is viewing a
 * singular view of a post ('album' post_type).
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
	
	$meta = get_post_custom( get_the_ID() );
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
				
				<a title="<?php the_title_attribute(); ?>" rel="lightbox" href="<?php the_permalink(); ?>">
					<?php echo $image; ?>
            	</a>
				
            	<?php
            				}
            			}
           		?>
                
                <section class="entry">
                	<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
				</section>
				<?php if ( class_exists( 'woocommerce' ) && isset( $meta['_product_id'] ) ) { ?>
				<section class="entry product-details">
				<?php
					$product = &new woocommerce_product( $meta['_product_id'][0] );

					echo do_shortcode( '[add_to_cart id="' . $product->id . '"]' );
				?>
				</section>
				<?php } ?>
				<?php
					if ( count( $tracks = $woothemes_discography->get_tracks( array( 'id' => get_the_ID() ) ) ) > 0 ) {
				?>
				<section class="entry audio-player">
					<?php
						$woothemes_discography->load_playlist( get_the_ID() );
					?>
					<div id="player-<?php echo get_the_ID(); ?>" class="woo-audio-player">
					</div><!--/.woo-audio-player-->
				</section>
				<?php
					}
				?>
            </article><!-- .post -->
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