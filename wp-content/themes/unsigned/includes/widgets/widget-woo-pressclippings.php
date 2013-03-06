<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom press clippings widget.
Date Created: 2012-02-02.
Last Modified: 2012-02-02.
Author: Matty.
Since: 1.0.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_PressClippings extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_PressClippings () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_press_clippings', 'description' => __( 'The press clippings on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_press_clippings' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_press_clippings', __( 'Woo - Press Clippings', 'woothemes' ), $widget_ops, $control_ops );
		
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
		global $post, $woothemes_press;
		
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
		do_action( 'widget_press_clippings_top' );
		
		$query_args = array( 'limit' => $instance['limit'], 'id' => $category );
		
		$press = $woothemes_press->get_press( $query_args );
		
		$html = '';
		
		if ( count( $press ) > 0 ) {
			$html .= '<ul class="fix">' . "\n";
			foreach ( $press as $k => $post ) {
				setup_postdata( $post );
				
				$meta = get_post_custom( $post->ID );
				
				$html .= '<li>' . "\n";
				if ( woo_image( 'return=true&link=url' ) != '' ) {
					$html .= '<a href="' . esc_url( get_permalink( $post ) ) . '" class="woo-press-thumb-anchor">' . woo_image( 'return=true&link=img&width=' . $width . '&height=' . $height . '&class=woo-press-thumb rounded' ) . '</a>' . "\n";
				}
				$html .= '<h4><a href="' . esc_url( get_permalink( $post ) ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">' . get_the_title() . '</a></h4>' . "\n";
				$html .= '<p class="excerpt">' . get_the_excerpt() . '</p>' . "\n";
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>' . "\n";
			$html .= '<div class="fix"></div>' . "\n";
		} else {
			$html = '<p>' . __( 'No press clippings are currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
		
		echo $html; 
		
		$post = $saved_post;
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_press_clippings_bottom' );

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
   		global $woothemes_press;
   		
       /* Set up some default widget settings. */
		$defaults = array(
						'title' => __( 'Press Clippings', 'woothemes' ), 
						'category' => 0, 
						'limit' => 5, 
						'width' => 266, 
						'height' => 140
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$categories = $woothemes_press->get_categories();
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
			<select name="<?php echo $this->get_field_name( 'category' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>">
				<option value="0"<?php selected( $instance['category'], 0 ); ?>><?php _e( 'All', 'woothemes' ); ?></option>
				<?php
					foreach ( $categories as $k => $v ) {
				?>
					<option value="<?php echo $v->term_id; ?>"<?php selected( $instance['category'], $v->term_id ); ?>><?php echo $v->name; ?></option>
				<?php
					}
				?>     
			</select>
		</p>
<?php
	} // End form()
} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------
  
  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_PressClippings");' ), 1 ); 
?>