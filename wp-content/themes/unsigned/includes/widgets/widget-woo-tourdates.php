<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom tour events list widget.
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

class Woo_Widget_TourDates extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_TourDates () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_tourdates', 'description' => __( 'The events that are part of a specific tour on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_tourdates' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_tourdates', __( 'Woo - Tour Dates', 'woothemes' ), $widget_ops, $control_ops );
		
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

		$tour = $instance['tour'];

		$unique_id = $args['widget_id'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
		
			echo $before_title . $title . $after_title;
		
		} // End IF Statement
		
		/* Widget content. */
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_tourdates_top' );
		
		$events = $woothemes_events->get_events_by_tour( $tour );
		
		$html = '';
		
		// Prepare and display tour details (name and description, if one is set).
		$tour_data = get_term( $tour, 'event_tour' );
		
		$html .= '<div class="container">' . "\n";

		$html .= '<h6>' . $tour_data->name . '</h6>' . "\n";
		
		if ( isset( $tour_data->description ) ) {
			$html .= '<p class="description">' . $tour_data->description . '</p>' . "\n";
		}
		
		$html .= '</div>' . "\n";
		
		if ( count( $events ) > 0 ) {
			$html .= '<table>' . "\n";
			$html .='<thead>' . "\n";
				$html .= '<th>' . __( 'Date', 'woothemes' ) . '</th>' . "\n";
				$html .= '<th>' . __( 'Venue', 'woothemes' ) . '</th>' . "\n";
				$html .= '<th>' . __( 'Tickets', 'woothemes' ) . '</th>' . "\n";
			$html .= '</thead>' . "\n";
			foreach ( $events as $k => $post ) {
				setup_postdata( $post );
				$meta = get_post_custom( $post->ID );
				
				$venue = get_the_title( $post->ID );
				
				if ( isset( $meta['_event_venue'] ) && ( $meta['_event_venue'][0] != '' ) ) {
					$venue = $meta['_event_venue'][0];
				}

				$tickets_defaulttext = '';
				$tickets_anchor = $tickets_defaulttext;
				
				if ( isset( $meta['_tickets_text'] ) && ( $meta['_tickets_text'][0] != '' ) ) {
					$tickets_anchor = $meta['_tickets_text'][0];
				}
				
				if ( isset( $meta['_tickets_url'] ) && ( $meta['_tickets_url'][0] != '' ) ) {
					if ( $tickets_anchor == $tickets_defaulttext ) {
						$tickets_anchor = __( 'Buy Tickets', 'woothemes' );
					}
					$tickets_anchor = '<a href="' . esc_url( $meta['_tickets_url'][0] ) . '">' . $tickets_anchor . '</a>';
				}

				$html .= '<tr>' . "\n";
				$html .= '<td>' . date_i18n( get_option( 'date_format' ), $meta['_event_start'][0] ) . '</td>' . "\n";
				$html .= '<td><a href="' . get_permalink( $post->ID ) . '">' . $venue . '</a><p>' . get_the_title( $post->ID ) . '</p></td>' . "\n";
				$html .= '<td>' . $tickets_anchor . '</td>' . "\n";
				$html .= '</tr>' . "\n";
			}
			$html .= '</table>' . "\n";
		} else {
			$html = '<div class="container"><p>' . __( 'No tour dates are currently listed.', 'woothemes' ) . '</p></div>' . "\n";
		}
				
		echo $html; 
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_tourdates_bottom' );

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
		
		/* The select box is returning a text value, so we escape it. */
		$instance['tour'] = esc_attr( $new_instance['tour'] );
		
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
   		global $woothemes_events;
   		
       /* Set up some default widget settings. */
		$defaults = array(
						'title' => __( 'Tour Dates', 'woothemes' ), 
						'tour' => -1
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$tours = $woothemes_events->get_tours();
?>
       <!-- Widget Title: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
       </p>
       <!-- Widget Tour: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'tour' ); ?>"><?php _e( 'Tour:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'tour' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'tour' ); ?>">
				<?php
					foreach ( $tours as $k => $v ) {
				?>
					<option value="<?php echo $v->term_id; ?>"<?php selected( $instance['tour'], $v->term_id ); ?>><?php echo $v->name; ?></option>
				<?php
					}
				?>     
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

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_TourDates");' ), 1 ); 
?>