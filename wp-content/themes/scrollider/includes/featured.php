<?php
/**
 * Homepage Slider
 */
	global $wp_query, $post, $panel_error_message;
	
	$settings = array(
					'featured_type' => 'full',
					'featured_entries' => 3,
					'featured_height' => 380,
					'featured_tags' => '',
					'slider_video_title' => 'true',
					'featured_order' => 'DESC',
					'featured_speed' => '7',
					'featured_hover' => 'false',
					'featured_touchswipe' => 'true',
					'featured_animation_speed' => '0.6',
					'featured_pagination' => 'false',
					'featured_nextprev' => 'true',
					'featured_opacity' => '0.5',
					'featured_slide_group' => 0
					);
					
	$settings = woo_get_dynamic_values( $settings );
	
	$count = 0;
?>

<?php
	$featposts = $settings['featured_entries']; // Number of featured entries to be shown
	$orderby = 'date';
	if ( $settings['featured_order'] == 'rand' )
		$orderby = 'rand';
	$slidertype = $settings['featured_type'];
	$slider_video_title = $settings['slider_video_title'];
?>

<div id="featured-wrap">
<?php
	$args = array( 'post_type' => 'slide', 'numberposts' => $featposts, 'order' => $settings['featured_order'], 'orderby' => $orderby, 'suppress_filters' => 0 );
	
	if ( 0 < intval( $settings['featured_slide_group'] ) ) {
	$args['tax_query'] = array(
								array( 'taxonomy' => 'slide-page', 'field' => 'id', 'terms' => intval( $settings['featured_slide_group'] ) )
							);
	}

	$slides = get_posts( $args );
?>

<?php if ( count($slides) > 0 ) { ?>

	<div id="featured" class="flexslider<?php if ($settings['featured_navigation_type'] == 'pagination') { echo ' has-pagination'; } ?>">
<div class="controls-container">
		<ul class="slides">

			<?php
				foreach( $slides as $post ) { setup_postdata( $post ); $count++;
					$css_class = '';

					if ( get_post_meta( get_the_ID(), '_enable_content_overlay', true ) == 'true' ) {
						$css_class = ' has-overlay';
					}
			?>
			<li class="slide<?php echo esc_attr( $css_class ); ?>">

				<?php $url = get_post_meta($post->ID, 'url', true); ?>

	    		<?php
		    		if ( $slidertype == "full" ) {
		    			$has_embed = woo_embed( 'width=800&height=400&class=slide-video' );
		    		} else {
		    			$has_embed = woo_embed( 'width=960&height=' . $settings['featured_height'] . '&class=slide-video-carousel' );
		    		}
	    			if ( $has_embed ) {
	    				echo $has_embed; 
	    			} else {
	    				
	    				if ( isset($url) && $url != '' ) { ?>
	    				<a href="<?php echo $url ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
	    				<?php }
	    				
	    				if ( $slidertype != "full" ) {
	    					woo_image( 'width=960&height=' . $settings['featured_height'] . '&class=slide-image&link=img&noheight=true' );
	    				} else {
	    					woo_image( 'width=2560&noheight=true&class=slide-image full&link=img' );
	    				}
	    				
	    				if ( isset($url) && $url != '' ) { ?></a><?php }
	    			}
	    		?>

	    		<div class="slide-content-container<?php if (!woo_image('return=true')) echo ' no-image' ?>">
	    	    	<article class="slide-content col-full <?php if ( !$has_embed ) { echo 'not-video'; } ?>">
	    	    		
	    	    		<?php if ( !$has_embed OR ( $has_embed && $settings['slider_video_title'] != 'true' ) )  { // Hide title/description if video post ?>
	    	    		<header>
	    	    			
	    	    			<h1>
	    	    				<?php if ( isset($url) && $url != '' ) { ?><a href="<?php echo $url ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php } ?>
	    	    					<?php
	    	    						$slide_title = get_the_title();
	    	    						echo woo_text_trim ( $slide_title, 25 );
	    	    					?>
	    	    				<?php if ( isset($url) && $url != '' ) { ?></a><?php } ?>
	    	    			</h1>

	    	    		</header>
	    	    		<?php } ?>
	    	    			
    	    			<div class="entry">

	        				<?php 
		        				if ( !$has_embed OR ( $has_embed && $settings['slider_video_title'] != 'true' ) )  { // Hide title/description if video post
	    	    					the_content();
	    	    				}
    	    				?>
    	    			</div><!-- /.entry -->
	    	    			    	    		
	    	    	</article>
    	    	</div><!-- /.slide-content-container -->
			</li><!-- /.slide -->

			<?php } ?> 
			
		</ul><!-- /.slides -->
		
		<?php if ( $settings['featured_pagination'] == "true" && $count > 1 ) { ?>
		<div class="manual col-full">
			<ol class="flex-control-nav">
			<?php
				$count = 0;
				foreach($slides as $post) : setup_postdata($post); $count++;

					echo '<li><a>'. $count .'</a></li>';

				endforeach;
			?>
			</ol>
		</div>
		<?php } ?>

		</div>

	</div><!-- /#featured -->

<?php } else { ?>
	<div class="col-full"><?php echo do_shortcode('[box type="info"]Please add some slides in the WordPress admin backend to show in the Featured Slider.[/box]'); ?></div>
<?php } ?> 

</div><!-- /#featured-wrap -->