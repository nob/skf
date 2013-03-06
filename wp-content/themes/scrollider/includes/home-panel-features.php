<?php
/**
 * Homepage Features Panel
 */
 
	/**
 	* The Variables
 	*
 	* Setup default variables, overriding them if the "Theme Options" have been saved.
 	*/
	
	$settings = array(
					'features_area_entries' => 3,
					'features_area_order' => 'DESC',
					'social_panel' => 'true',
					'features_social_text' => 'You can edit this text in your Theme Options panel under "Homepage Options > Features"'
					);
					
	$settings = woo_get_dynamic_values( $settings );
	$orderby = 'date';
	if ( $settings['features_area_order'] == 'rand' )
		$orderby = 'rand';
	
?>
			<?php
    		$features_args = array(
					'post_type' => 'features',  
					'posts_per_page' => $settings['features_area_entries'], 
					'order' => $settings['features_area_order'], 
					'orderby' => $orderby
				);	
			?>

			<section id="sub-feature">

				<div class="col-full">

					<div id="social">
						<h3><?php _e('Find us socially', 'woothemes'); ?></h3>
						<?php
							if ( isset($settings['social_panel']) && $settings['social_panel'] == 'true' ) 
								get_template_part( 'includes/social-panel' );
						?>
						<?php if ( isset($settings['features_social_text']) && $settings['features_social_text'] != '' ) { ?>
							<p><?php echo $settings['features_social_text']; ?></p>
						<?php } ?>
					</div>

					<div id="features">

						<?php 			
						   /* The Query. */			   
						$the_query = new WP_Query( $features_args );

						/* The Loop. */	
						if ( $the_query->have_posts() ) { $count = 0; ?>			
		    			
		    			<ul>

						<?php
						while ( $the_query->have_posts() ) { $the_query->the_post(); $count++;
		    				?>
		    				<li class="fix <?php if ( $count % 3 == 0 ) { echo 'last'; } ?>">
		    					
		    					<?php $feature_icon = get_post_meta( $post->ID, 'feature_icon', true ); if ( $feature_icon ) { ?><div class="image"><img src="<?php echo get_post_meta( $post->ID, 'feature_icon', true ); ?>" alt="" /></div><?php } ?>
		    					<div class="entry">
		    					<?php $feature_readmore = get_post_meta( $post->ID, 'feature_readmore', true ); ?>
			    				<h2><a href="<?php if ( $feature_readmore != '' ) { echo $feature_readmore; } else { the_permalink(); } ?>"><?php the_title(); ?></a></h2>
			    				<?php $feature_excerpt = get_post_meta( $post->ID, 'feature_excerpt', true ); ?>
			    				<p>
			    					<?php 
			    					if ( $feature_excerpt != '' ) { 
			    						echo stripslashes( $feature_excerpt ); 
			    					} else { 
			    						echo strip_tags( get_the_excerpt() ); 
			    					} ?>
			    					<a href="<?php if ( $feature_readmore != '' ) { echo $feature_readmore; } else { the_permalink(); } ?>" class="read-more"><?php _e( 'Read More', 'woothemes' ); ?></a>
			    				</p>
			    				
			    				</div>

			    				</li>
			    				<?php if ( $count % 3 == 0 ) { echo '<li class="fix clear"></li>'; } ?>
		    				<?php
		    			} // End While Loop ?>

		    			</ul>
		    			
		    			<?php } // End If Statement ?>
	    			
	    			</div>

    			</div><!-- /.col-full -->
    		
    		</section><!-- /#mini-features -->
    		<?php wp_reset_postdata(); ?>