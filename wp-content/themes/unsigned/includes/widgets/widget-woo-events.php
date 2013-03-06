<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom events widget.
Date Created: 2012-01-04.
Last Modified: 2012-01-04.
Author: Matty.
Since: 1.0.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_Events extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_Events () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_events', 'description' => __( 'The upcoming, current, past or all events on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_events' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_events', __( 'Woo - Events', 'woothemes' ), $widget_ops, $control_ops );
		
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
		$html = '';
		
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
		
		$limit = $instance['limit']; if ( ! intval( $limit ) ) { $limit = 5; }

		$unique_id = $args['widget_id'];
		
		$show_flyer = $instance['show_flyer'];
		
		$width = $instance['width'];
		
		$height = $instance['height'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
		
			echo $before_title . $title . $after_title;
		
		} // End IF Statement
		
		/* Widget content. */
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_events_top' );
		
		$args = array( 'post_type' => 'event', 'posts_per_page' => $limit, 'suppress_filters' => false );
		
		if ( isset( $instance['category'] ) && ( $instance['category'] > 0 ) ) {
			$category = intval( $instance['category'] );
			
			$args['tax_query'] = array(
									array(
										'taxonomy' => 'event_category',
										'field' => 'id',
										'terms' => $category
									)
								);
		}
		
		// If we're viewing a single event, hide that event from the widget.
		if ( is_single() && get_post_type() == 'event' ) {
			$args['post__not_in'] = array( get_the_ID() );
		}
		
		// Add an appropriate meta query, if necessary, according to the display_type.
		$args = $this->setup_meta_query( $args, $instance );

		$posts = get_posts( $args );

		$html = '';
		
		if ( count( $posts ) > 0 ) {
			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );
			
			$html .= '<ul>' . "\n";
			foreach ( $posts as $k => $post ) {
				setup_postdata( $post );
				
				$meta = get_post_custom( $post->ID );
				
				if ( $show_flyer ) {
					$image_args = array(
									'return' => 'true', 
									'link' => 'img', 
									'width' => $width, 
									'height' => $height, 
									'meta' => esc_attr( get_the_title( $post->ID ) ), 
									'class' => 'rounded', 
									'id' => $post->ID
									);
				}
				
				$html .= '<li>' . "\n";
				if ( $show_flyer ) {
					$html .= '<a href="' . get_permalink( $post->ID ) . '">' . woo_image( $image_args ) . '</a>' . "\n";
				}
				$html .= '<h4 class="event-title"><a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( get_the_title( $post->ID ) ) . '">' . get_the_title( $post->ID ) . '</a></h4>' . "\n";
				if ( isset( $meta['_event_start'] ) && ( $meta['_event_start'][0] != '' ) ) {
					$html .= '<p class="date"><strong class="label">' . __( 'Date:', 'woothemes' ) . '</strong> ' . date_i18n( $date_format, $meta['_event_start'][0] ) . ' @ ' . date_i18n( $time_format, $meta['_event_start'][0] ) . '</p>' . "\n";
				}
				if ( isset( $meta['_event_venue'] ) && ( $meta['_event_venue'][0] != '' ) ) {
					$html .= '<p class="venue"><strong class="label">' . __( 'Venue:', 'woothemes' ) . '</strong> ' . $meta['_event_venue'][0] . '</p>' . "\n";
				}
				if ( isset( $meta['_ticket_price'] ) && ( $meta['_ticket_price'][0] != '' ) ) {
					$html .= '<p class="price"><strong class="label">' . __( 'Price:', 'woothemes' ) . '</strong> ' . $meta['_ticket_price'][0] . '</p>' . "\n";
				}
				$html .= '<p class="excerpt fix">' . apply_filters( 'get_the_excerpt', $post->post_excerpt ) . '</p>' . "\n";
				$html .= '<div class="fix"></div>' . "\n";
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>' . "\n";
			
				$html .= '<div class="fix"></div>' . "\n";
			
		} else {
			$html = '<p>' . __( 'No events are currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
		
		echo $html; 
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_events_bottom' );

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
		
		/* The select box is returning a text value, so we escape it. */
		$instance['category'] = esc_attr( $new_instance['category'] );
		
		/* The checkbox is returning a Boolean (true/false), so we check for that. */
		$instance['show_flyer'] = (bool) esc_attr( $new_instance['show_flyer'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['width'] = esc_attr( $new_instance['width'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['height'] = esc_attr( $new_instance['height'] );
		
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
						'display_type' => 'all', 
						'category' => 0, 
						'show_flyer' => 1, 
						'width' => 200, 
						'height' => 300
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
				<option value="all"<?php selected( $instance['display_type'], 'all' ); ?>><?php _e( 'All Events', 'woothemes' ); ?></option>
				<option value="upcoming"<?php selected( $instance['display_type'], 'upcoming' ); ?>><?php _e( 'Upcoming Events', 'woothemes' ); ?></option>
				<option value="right-now"<?php selected( $instance['display_type'], 'right-now' ); ?>><?php _e( 'Events Happening Right Now', 'woothemes' ); ?></option>
				<option value="past"<?php selected( $instance['display_type'], 'past' ); ?>><?php _e( 'Past Events', 'woothemes' ); ?></option>
				<option value="weekend"<?php selected( $instance['display_type'], 'weekend' ); ?>><?php _e( 'Events This Weekend', 'woothemes' ); ?></option>
				<option value="week"<?php selected( $instance['display_type'], 'week' ); ?>><?php _e( 'All Events This Week', 'woothemes' ); ?></option>         
			</select>
		</p>
		<!-- Widget Event Category: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', 'woothemes' ); ?></label>
			<?php
				$args = array(
							'taxonomy' => 'event_category', 
							'show_option_all' => __( 'All', 'woothemes' ), 
							'title_li' => '', 
							'selected' => $instance['category'], 
							'name' => $this->get_field_name( 'category' ), 
							'id' => $this->get_field_id( 'category' ), 
							'class' => 'widefat'
						);
				wp_dropdown_categories( $args );
			?>
		</p>
		<!-- Widget Show Flyer: Checkbox Input -->
       	<p>
        	<input id="<?php echo $this->get_field_id( 'show_flyer' ); ?>" name="<?php echo $this->get_field_name( 'show_flyer' ); ?>" type="checkbox"<?php checked( $instance['show_flyer'], 1 ); ?> />
        	<label for="<?php echo $this->get_field_id( 'show_flyer' ); ?>"><?php _e( 'Show Event Flyer', 'woothemes' ); ?></label>
	   	</p>
	   	 <!-- Widget Flyer Width & Height: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'width' ); ?>"  value="<?php echo $instance['width']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'width' ); ?>" />
	       <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'height' ); ?>"  value="<?php echo $instance['height']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'height' ); ?>" />
       </p>
       <p><small><?php _e( 'The width and height of the event flyer', 'woothemes' ); ?></small></p>
<?php
	} // End form()
	
	/**
	 * setup_meta_query function.
	 * 
	 * @access public
	 * @param array $args
	 * @param object $instance
	 * @return array $args
	 */
	function setup_meta_query ( $args, $instance ) {
		global $woothemes_events;
		
		$meta_query = '';
		
		// Adjust arguments based on display type.
		switch ( $instance['display_type'] ) {
			case 'right-now':
				$meta_query = array(
									'relation' => 'AND', 
									array( 'key' => '_event_start', 'value' => time(), 'compare' => '<=', 'type' => 'numeric' ), 
									array( 'key' => '_event_end', 'value' => time(), 'compare' => '>', 'type' => 'numeric' )
									);
			break;
			
			case 'past':
				$meta_query = array(
									'relation' => 'AND', 
									array( 'key' => '_event_start', 'value' => time(), 'compare' => '<', 'type' => 'numeric' ), 
									array( 'key' => '_event_end', 'value' => time(), 'compare' => '<', 'type' => 'numeric' )
									);
			break;
			
			case 'weekend':
				// Get this weekend's timestamps.
				$dates = $woothemes_events->get_next_weekend_dates( time() );
				
				// Make sure we have only the first and last timestamp.
				$timestamps = array( $dates[0], $dates[count( $dates )-1] );

				$meta_query = array(
									array( 'key' => '_event_start', 'value' => $timestamps, 'compare' => 'BETWEEN', 'type' => 'numeric' )
									);
			break;
			
			case 'week':
				// Get this week's timestamps.
				$dates = $woothemes_events->get_dates_of_week( time() );
				
				// Make sure we have only the first and last timestamp.
				$timestamps = array( $dates[0], $dates[count( $dates )-1] );
				
				$meta_query = array(
									array( 'key' => '_event_start', 'value' => $timestamps, 'compare' => 'BETWEEN', 'type' => 'numeric' )
									);
			break;
			
			case 'upcoming':
				$meta_query = array(
									array( 'key' => '_event_start', 'value' => time(), 'compare' => '>', 'type' => 'numeric' )
									);
				
			break;
			
			default:
			break;
		}
		
		if ( $meta_query != '' ) {
			$args['meta_query'] = $meta_query;
		}
		
		return $args;
	} // End setup_meta_query()
	
} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------
  
  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Events");' ), 1 ); 
?>