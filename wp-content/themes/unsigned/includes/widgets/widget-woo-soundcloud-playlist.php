<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom SoundCloud playlist widget.
Date Created: 2012-01-18.
Last Modified: 2012-01-18.
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

class Woo_Widget_SoundCloud_Playlist extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_SoundCloud_Playlist () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_soundcloud_playlist', 'description' => __( 'A playlist from your SoundCloud account', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_soundcloud_playlist' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_soundcloud_playlist', __( 'Woo - SoundCloud - Playlist', 'woothemes' ), $widget_ops, $control_ops );
		
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
		global $woothemes_soundcloud;
		
		$html = '';
		
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );

		$playlist = $instance['playlist'];
		$player_type = $instance['player_type'];
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
		do_action( 'widget_woo_soundcloud_playlist_top' );
		
		$html .= $woothemes_soundcloud->generate_player( $playlist, 'playlists', array( 'width' => $width, 'height' => $height, 'type' => $player_type ) );
		
		echo $html; 
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_soundcloud_playlist_bottom' );

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
		$instance['playlist'] = esc_attr( $new_instance['playlist'] );
		
		/* The select box is returning a text value, so we escape it. */
		$instance['player_type'] = esc_attr( $new_instance['player_type'] );
		
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
   		global $woothemes_soundcloud;
   		
       /* Set up some default widget settings. */
		$defaults = array(
						'title' => __( 'SoundCloud Playlist', 'woothemes' ), 
						'playlist' => 0, 
						'width' => '100%', 
						'height' => 300, 
						'player_type' => 'standard'
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$playlists = $woothemes_soundcloud->get_playlists();
		
		$types = array( 'standard' => __( 'Standard', 'woothemes' ), 'html5' => __( 'HTML5 (beta)', 'woothemes' ), 'tiny' => __( 'Mini', 'woothemes' ) );
?>
       <!-- Widget Title: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
       </p>
       <!-- Widget Width & Height: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'width' ); ?>"  value="<?php echo $instance['width']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'width' ); ?>" />
	       <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'height' ); ?>"  value="<?php echo $instance['height']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'height' ); ?>" />
       </p>
       <p><small><?php _e( 'The width and height of the player', 'woothemes' ); ?></small></p>
       <!-- Widget Playlist: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'playlist' ); ?>"><?php _e( 'Playlist:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'playlist' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'playlist' ); ?>">
				<?php
					foreach ( $playlists as $k => $v ) {
				?>
					<option value="<?php echo $v->id; ?>"<?php selected( $instance['playlist'], $v->id ); ?>><?php echo $v->title; ?></option>
				<?php
					}
				?>     
			</select>
		</p>
		 <!-- Widget Player Type: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'player_type' ); ?>"><?php _e( 'Player Type:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'player_type' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'player_type' ); ?>">
				<?php
					foreach ( $types as $k => $v ) {
				?>
					<option value="<?php echo $k; ?>"<?php selected( $instance['player_type'], $k ); ?>><?php echo $v; ?></option>
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

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_SoundCloud_Playlist");' ), 1 ); 
?>