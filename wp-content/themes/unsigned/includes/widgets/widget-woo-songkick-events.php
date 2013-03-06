<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom SongKick events widget.
Date Created: 2012-02-07.
Last Modified: 2012-02-07.
Author: Matty.
Since: 1.2.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_SongKick_Events extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_SongKick_Events () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_events', 'description' => __( 'The upcoming, current, past or all events on your Songkick profile', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_songkick_events' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_songkick_events', __( 'Woo - Songkick - Events', 'woothemes' ), $widget_ops, $control_ops );
		
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
		global $woothemes_songkick;
		
		$html = '';
		
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
		
		$limit = $instance['limit']; if ( ! intval( $limit ) ) { $limit = 5; }

		$unique_id = $args['widget_id'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
		
			echo $before_title . $title . $after_title;
		
		} // End IF Statement
		
		/* Widget content. */
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_songkick_events_top' );

		$args = array( 'display_type' => $instance['display_type'], 'limit' => $limit );

		$html = $woothemes_songkick->generate_events_list_html( $args );

		echo $html; 
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_songkick_events_bottom' );

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
		
		$instance['limit'] = esc_attr( $new_instance['limit'] );
		
		/* The select box is returning a text value, so we escape it. */
		$instance['display_type'] = esc_attr( $new_instance['display_type'] );
		
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
						'title' => __( 'Events', 'woothemes' ), 
						'limit' => 5, 
						'display_type' => 'upcoming'
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
       <!-- Widget Display Type: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'display_type' ); ?>"><?php _e( 'Display Type:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'display_type' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'display_type' ); ?>">
				<option value="upcoming"<?php selected( $instance['display_type'], 'upcoming' ); ?>><?php _e( 'Upcoming Events', 'woothemes' ); ?></option>
				<option value="past"<?php selected( $instance['display_type'], 'past' ); ?>><?php _e( 'Past Events', 'woothemes' ); ?></option>    
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

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_SongKick_Events");' ), 1 ); 
?>