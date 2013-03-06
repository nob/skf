<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom tours list widget.
Date Created: 2012-01-05.
Last Modified: 2012-01-05.
Author: Matty.
Since: 1.0.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()
- function filter ()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_Tours extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_Tours () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_tours', 'description' => __( 'The upcoming, current, past or all tours on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_tours' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_tours', __( 'Woo - Tours', 'woothemes' ), $widget_ops, $control_ops );
		
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
		global $woothemes_events;
		
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
		do_action( 'widget_woo_tours_top' );
		
		$tours = $woothemes_events->get_tours( $limit );

		// Make sure we only have the desired tours displayed.
		$tours = $this->filter_tours( $tours, $instance );

		$html = '';
		
		if ( count( $tours ) > 0 ) {
			$html .= '<ul>' . "\n";
			foreach ( $tours as $k => $v ) {
				$html .= '<li><a href="' . get_term_link( $v, 'event_tour' ) . '">' .$v->name . '</a><p>' . $v->tour_dates['start'] . ' ' . __( 'to', 'woothemes' ) . ' ' . $v->tour_dates['end'] . '</p></li>' . "\n";
			}
			$html .= '</ul>' . "\n";
		} else {
			$html = '<p>' . __( 'No tours are currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
		
		echo $html; 
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_tours_bottom' );

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
						'title' => __( 'Tours', 'woothemes' ), 
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
				<option value="upcoming"<?php selected( $instance['display_type'], 'upcoming' ); ?>><?php _e( 'Upcoming Tours', 'woothemes' ); ?></option>
				<option value="right-now"<?php selected( $instance['display_type'], 'right-now' ); ?>><?php _e( 'Tours Happening Right Now', 'woothemes' ); ?></option>
				<option value="past"<?php selected( $instance['display_type'], 'past' ); ?>><?php _e( 'Past Tours', 'woothemes' ); ?></option>       
			</select>
		</p>
<?php
	} // End form()
	
	/**
	 * filter_tours function.
	 * 
	 * @access public
	 * @param array $tours
	 * @param array $instance
	 * @return array $tours
	 */
	function filter_tours ( $tours, $instance ) {
		foreach ( $tours as $k => $v ) {			
			$start_date = $v->events[0]->event_start;
			$end_date = $v->events[count( $v->events )-1]->event_end;
			
			// Filter out tours based on display type.
			switch ( $instance['display_type'] ) {
				case 'right-now':
					if ( $start_date <= time() && $end_date > time() ) {} else {
						unset( $tours[$k] );
					}
				break;
				
				case 'past':
					if ( $start_date < time() && $end_date < time() ) {} else {
						unset( $tours[$k] );
					}
				break;
				
				default:
				case 'upcoming':
					if ( $start_date > time() ) {} else {
						unset( $tours[$k] );
					}
				break;
			}
		}
		
		return $tours;
	} // End filter_tours()
} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------
  
  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Tours");' ), 1 ); 
?>