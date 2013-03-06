<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom recent news widget.
Date Created: 2012-01-18.
Last Modified: 2012-01-18.
Author: Matty.
Since: 1.0.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_RecentNews extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_RecentNews () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_recent_news', 'description' => __( 'The recent news on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_recent_news' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_recent_news', __( 'Woo - Recent News', 'woothemes' ), $widget_ops, $control_ops );
		
	} // End Constructor

	/**
	 * widget function.
	 *
	 * @description Displays the widget on the frontend.
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {  
		global $post;
		
		$saved_post = $post;
		
		$html = '';
		
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );

		$category = $instance['category'];
		$width = $instance['width'];
		$height = $instance['height'];
		
		$unique_id = $args['widget_id'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		/* Widget content. */
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_recentnews_top' );
		
		$query_args = array( 'numberposts' => intval( $instance['limit'] ), 'category' => $category, 'suppress_filters' => false );
		
		$posts = get_posts( $query_args );
		
		$html = '';
		
		if ( count( $posts ) > 0 ) {
			$html .= '<ul class="fix">' . "\n";
			foreach ( $posts as $k => $post ) {
				setup_postdata( $post );
				
				$meta = get_post_custom( $post->ID );
				
				$html .= '<li>' . "\n";
				$html .= '<a href="' . esc_url( get_permalink( $post ) ) . '" class="woo-recentnews-thumb-anchor">' . woo_image( 'return=true&link=img&width=' . esc_attr( $width ) . '&height=' . esc_attr( $height ) . '&class=woo-recentnews-thumb rounded' ) . '</a>' . "\n";
				$html .= '<h4><a href="' . esc_url( get_permalink( $post ) ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">' . get_the_title() . '</a></h4>' . "\n";
				$html .= '<p class="excerpt">' . esc_html( get_the_excerpt() ) . '</p>' . "\n";
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>' . "\n";
			$html .= '<div class="fix"></div>' . "\n";
		} else {
			$html = '<p>' . __( 'No recent news is currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
		
		echo $html; 
		
		$post = $saved_post;
		wp_reset_postdata();
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_recentnews_bottom' );

		/* After widget (defined by themes). */
		echo $after_widget;
	} // End widget()

	/**
	 * update function.
	 *
	 * @description Function to update the settings from the form() function.
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array $instance
	 */
	function update ( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['limit'] = esc_attr( $new_instance['limit'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['width'] = esc_attr( $new_instance['width'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['height'] = esc_attr( $new_instance['height'] );
		
		/* The select box is returning a text value, so we escape it. */
		$instance['category'] = esc_attr( $new_instance['category'] );
		
		return $instance;
	} // End update()

   /**
    * form function.
    *
    * @description The form on the widget control in the widget administration area.
    * @access public
    * @param array $instance
    * @return void
    */
   function form( $instance ) {
   		
       /* Set up some default widget settings. */
		$defaults = array(
						'title' => __( 'Recent News', 'woothemes' ), 
						'category' => 0, 
						'limit' => 5, 
						'width' => 266, 
						'height' => 140
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
       <!-- Widget Title: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
       </p>
       <!-- Widget Limit: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>"  value="<?php echo $instance['limit']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
       </p>
       <!-- Widget Width & Height: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'width' ); ?>"  value="<?php echo $instance['width']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'width' ); ?>" />
	       <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'height' ); ?>"  value="<?php echo $instance['height']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'height' ); ?>" />
       </p>
       <p><small><?php _e( 'The width and height of the thumbnail image', 'woothemes' ); ?></small></p>
       <!-- Widget Category: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', 'woothemes' ); ?></label>
			<?php
				$args = array(
							'name' => $this->get_field_name( 'category' ), 
							'id' => $this->get_field_id( 'category' ), 
							'show_option_all' => __( 'All', 'woothemes' ), 
							'selected' => $instance['category']
						);
				
				wp_dropdown_categories( $args );
			?>
		</p>
<?php
	} // End form()
} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------
  
  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_RecentNews");' ), 1 ); 
?>