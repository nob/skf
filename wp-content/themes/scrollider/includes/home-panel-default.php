<?php
/**
 * Homepage Default Content Panel
 */
 
	/**
 	* Load JS for widget
 	*/
	
	wp_register_script('home-default-content', get_template_directory_uri().'/includes/js/news-from-blog.js', array('jquery', 'flexslider'), '1.0.0', true);
	wp_enqueue_script('home-default-content');
?>
	<section id="home-default-content" class="col-full">

		<div class="col-full">

			<?php

			$instance = array(
				'title' => __('News from the Blog'),
				'limit' => 6,
				'effect' => 'slide',
				'navigation' => true
			);

			$args = array(
				'before_widget' => '<div class="widget widget_woo_newsfromblog">',
				'after_widget' => '</div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>',
			);

			the_widget( 'Woo_Widget_NewsFromBlog' , $instance , $args );

			?>
		</div>

	</section>