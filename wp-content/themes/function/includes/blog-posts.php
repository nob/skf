<?php
/**
 * Homepage Features Panel
 */
 
	/**
 	* The Variables
 	*
 	* Setup default variables, overriding them if the "Theme Options" have been saved.
 	*/
	
	global $woo_options;
	
	$settings = array(
					'blog_thumb_w' => 224, 
					'blog_thumb_h' => 150, 
					'homepage_number_of_posts' => 8,
					'homepage_blog_area_title' => __('Latest Blog Posts', 'woothemes'), 
					'homepage_posts_category' => 0
					);
					
	$settings = woo_get_dynamic_values( $settings );
	$orderby = 'date';
	
?>
			<?php
			$number_of_features = $settings['homepage_number_of_posts'];
			/* The Query. */
			$query_args = array( 
					'post_type' => 'post', 
					'posts_per_page' => $number_of_features, 
					'orderby' => $orderby
				);

			if ( 0 < intval( $settings['homepage_posts_category'] ) ) {
				$query_args['tax_query'] = array(
												array( 'taxonomy' => 'category', 'field' => 'id', 'terms' => intval( $settings['homepage_posts_category'] ) )
											);
			}

			$the_query = new WP_Query( $query_args );
			/* Query Count */
			$query_count = $the_query->post_count;
			/* The Loop. */
			if ( $the_query->have_posts() ) { $count = 0; ?>
			<section id="blog-posts" class="home-section fix">
    		
    			<header class="block">
    				<h1><?php echo stripslashes( $settings['homepage_blog_area_title'] ); ?></h1>
    			</header>
    			
    			<ul class="slides">

					<?php while ( $the_query->have_posts() ) { $the_query->the_post(); $count++; ?>
						<li>
		    				<div class="item<?php if ($count % 4 == 0) { echo ' last'; } ?>">
			    				<?php woo_image( 'noheight=true&width=' . $settings['blog_thumb_w'] . '&height=' . $settings['blog_thumb_h'] ); ?>
			    				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			    				<p><?php echo woo_text_trim( get_the_excerpt() , 12 ); ?></p>
			    				<p><a class="btn" href="<?php the_permalink(); ?>"><?php _e('Read More', 'woothemes'); ?></a></p>
			    			</div>
		    			</li>
    				<?php } // End While Loop ?>

    			</ul>
    		
    		</section>
    		<?php } // End If Statement ?>
    		
    		<?php wp_reset_postdata(); ?>