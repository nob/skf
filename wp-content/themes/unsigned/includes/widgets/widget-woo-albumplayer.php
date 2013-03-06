<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom album audio player widget.
Date Created: 2012-01-15.
Last Modified: 2012-01-15.
Author: Matty.
Since: 1.0.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_AlbumPlayer extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_AlbumPlayer () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_albumplayer', 'description' => __( 'Play tracks from an album on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_albumplayer' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_albumplayer', __( 'Woo - Album Player', 'woothemes' ), $widget_ops, $control_ops );
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
		global $woothemes_discography;

		// Don't display this album player widget if we're on it's detail screen.
		if ( ( ( $instance['album'] != '' ) && is_single( $instance['album'] ) ) || ( ( $instance['album'] != '' ) && in_array( $instance['album'], $woothemes_discography->loaded_playlists ) ) ) {
			return;
		}
		
		$woothemes_discography->enqueue_frontend_styles();
		
		$html = '';
		
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );

		$album = $instance['album'];
		$width = $instance['width'];
		$height = $instance['height'];
		
		$unique_id = $args['widget_id'];
		
		if ( $album != '' ) {
			$woothemes_discography->load_playlist( $album );
		}
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		/* Widget content. */
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_albumplayer_top' );
		
		$html .= '<div id="player-' . $album . '" class="woo-audio-player">' . "\n";
		$html .= '</div>' . "\n";
		
		echo $html; 
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_albumplayer_bottom' );

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
		$instance['width'] = esc_attr( $new_instance['width'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['height'] = esc_attr( $new_instance['height'] );
		
		/* The select box is returning a text value, so we escape it. */
		$instance['album'] = esc_attr( $new_instance['album'] );
		
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
   		global $woothemes_discography;
   		
       /* Set up some default widget settings. */
		$defaults = array(
						'title' => __( 'Audio Player', 'woothemes' ), 
						'album' => 0
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$albums = $woothemes_discography->get_albums( array( 'limit' => -1 ) );
?>
       <!-- Widget Title: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
       </p>
       <!-- Widget Album: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'album' ); ?>"><?php _e( 'Album:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'album' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'album' ); ?>">
				<?php
					foreach ( $albums as $k => $v ) {
				?>
					<option value="<?php echo $v->ID; ?>"<?php selected( $instance['album'], $v->ID ); ?>><?php echo $v->post_title; ?></option>
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

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_AlbumPlayer");' ), 1 ); 
?>