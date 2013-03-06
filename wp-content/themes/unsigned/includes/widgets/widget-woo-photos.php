<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom gallery photos widget.
Date Created: 2012-01-14.
Last Modified: 2012-01-14.
Author: Matty.
Since: 1.0.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()
- function filter ()
- function load_prettyphoto_assets()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_Photos extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_Photos () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_photos', 'description' => __( 'The photos on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_photos' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_photos', __( 'Woo - Photos', 'woothemes' ), $widget_ops, $control_ops );
		
		/* Enqueue prettyPhoto as this widget requires it. */
		if ( ! is_admin() && is_active_widget( false, false, $this->id_base, true ) ) {
			add_action( 'wp_print_styles', array( &$this, 'load_prettyphoto_assets' ) );
			add_action( 'wp_print_scripts', array( &$this, 'load_prettyphoto_assets' ) );
		}
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
		global $woothemes_photos, $post;
		
		$html = '';
		
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );

		$gallery = $instance['gallery'];
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
		do_action( 'widget_woo_photos_top' );
		
		$query_args = array( 'limit' => $instance['limit'], 'id' => $gallery );
		
		$photos = $woothemes_photos->get_photos( $query_args );
		
		$html = '';
		
		$saved_post = $post;
		
		if ( count( $photos ) > 0 ) {
		
				$html .= '<div class="box">' . "\n";
		
			if ( $gallery == '' ) { $gallery = $unique_id; }
			$rel = $gallery;
		
			foreach ( $photos as $k => $post ) {
				setup_postdata( $post );
			
				$html .= '<div class="rounded-border fl">' . "\n";
				$html .= '<a href="' . $post->guid . '" rel="lightbox[photos-' . $rel . ']" title="' . esc_attr( $post->post_excerpt ) . '">' . woo_image( 'return=true&link=img&width=' . $width . '&height=' . $height . '&class=fl rounded woo-photo-thumb&src=' . $post->guid ) . '</a>' . "\n";
				$html .= '</div>' . "\n";
								
			}
				$html .= '<div class="fix"></div>' . "\n";
				$html .= '</div>' . "\n";
		
			$post = $saved_post;
		} else {
			$html = '<p>' . __( 'No photos are currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
				
		echo $html; 
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_photos_bottom' );
				
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
		$instance['gallery'] = esc_attr( $new_instance['gallery'] );
		
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
   		global $woothemes_photos;
   		
       /* Set up some default widget settings. */
		$defaults = array(
						'title' => __( 'Photos', 'woothemes' ), 
						'gallery' => 0, 
						'limit' => 5, 
						'width' => 115, 
						'height' => 100
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$galleries = $woothemes_photos->get_galleries();
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
       <!-- Widget Gallery: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'gallery' ); ?>"><?php _e( 'Gallery:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'gallery' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'gallery' ); ?>">
				<option value="0"<?php selected( $instance['gallery'], 0 ); ?>><?php _e( 'All', 'woothemes' ); ?></option>
				<?php
					foreach ( $galleries as $k => $v ) {
				?>
					<option value="<?php echo $v->ID; ?>"<?php selected( $instance['gallery'], $v->ID ); ?>><?php echo $v->post_title; ?></option>
				<?php
					}
				?>     
			</select>
		</p>
<?php
	} // End form()
	
	/**
	 * load_prettyphoto_assets function.
	 * 
	 * @access public
	 * @return void
	 */
	function load_prettyphoto_assets () {
		switch ( current_filter() ) {
			case 'wp_print_styles':
				wp_enqueue_style( 'prettyPhoto' );
			break;
			
			case 'wp_print_scripts':
				wp_enqueue_script( 'prettyPhoto-loader' );
			break;
		}
	} // End load_prettyphoto_assets()
} // End Class

/**
 * Register the widget on `widgets_init`.
 *
 * @access public
 * @return boolean
 */
add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Photos");' ), 1 ); 
?>