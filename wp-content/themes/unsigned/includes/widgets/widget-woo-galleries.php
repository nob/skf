<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom gallery widget.
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

class Woo_Widget_Galleries extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_Galleries () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_galleries', 'description' => __( 'The photo galleries on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_galleries' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_galleries', __( 'Woo - Galleries', 'woothemes' ), $widget_ops, $control_ops );
		
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
		
		$saved_post = $post;
		
		$html = '';
		
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );

		$category = $instance['category'];
		$width = $instance['width'];
		$height = $instance['height'];
		
		$thumb_width = $instance['thumb_width'];
		$thumb_height = $instance['thumb_height'];
		
		$show_thumbnails = $instance['show_thumbnails'];
		
		$unique_id = $args['widget_id'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		/* Widget content. */
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_galleries_top' );
		
		$query_args = array( 'limit' => intval( $instance['limit'] ), 'id' => $category );
		
		$galleries = $woothemes_photos->get_galleries( $query_args );
		
		$html = '';
		
		if ( count( $galleries ) > 0 ) {
			$html .= '<ul>' . "\n";
			foreach ( $galleries as $k => $post ) {
				
				// Setup the cover image. If none is found, attempt to find one of the gallery images.
				$image_args = 'return=true&link=img&width=' . $width . '&height=' . $height . '&class=alignleft woo-photo-thumb rounded';
				$image = $woothemes_photos->get_cover_image( $image_args );
			
				setup_postdata( $post );
				$html .= '<li>' . "\n";
				$html .= '<a href="' . $post->guid . '" class="woo-photo-thumb-anchor">' . $image . '</a>' . "\n";
				$html .= '<h4><a href="' . get_permalink( $post ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">' . get_the_title() . '</a></h4>' . "\n";
				$html .= '<p class="excerpt fix">' . get_the_excerpt() . '</p>' . "\n";
				
				if ( $show_thumbnails ) {
					$photos = $woothemes_photos->get_photos( array( 'id' => $post->ID, 'limit' => 5 ) );
					$html .= '<div class="photos fix">' . "\n";
					foreach ( $photos as $k => $v ) {
						$html .= '<a href="' . $v->guid . '" rel="lightbox[gallery-' . $post->ID . ']" title="' . esc_attr( $v->post_excerpt ) . '">' . woo_image( 'return=true&link=img&width=' . $thumb_width . '&height=' . $thumb_height . '&class=rounded fl woo-photo-thumb&src=' . $v->guid ) . '</a>' . "\n";
					}
					$html .= '</div>' . "\n";
				}

				$html .= '<div class="fix"></div>' . "\n";
				$html .= '<p class="fix"><a class="button" href="' . get_permalink( $post ) . '">' . __( 'View Gallery', 'woothemes' ) . ' &rarr;</a></p>' . "\n";
				
				$html .= '<div class="fix"></div>' . "\n";
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>' . "\n";
		} else {
			$html = '<p>' . __( 'No galleries are currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
		
		echo $html; 
		
		$post = $saved_post;
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_galleries_bottom' );

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
		$instance['limit'] = intval( esc_attr( $new_instance['limit'] ) );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['width'] = esc_attr( $new_instance['width'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['height'] = esc_attr( $new_instance['height'] );
		
		/* The select box is returning a text value, so we escape it. */
		$instance['category'] = esc_attr( $new_instance['category'] );
		
		/* The checkbox is returning a Boolean (true/false), so we check for that. */
		$instance['show_thumbnails'] = (bool) esc_attr( $new_instance['show_thumbnails'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['thumb_width'] = esc_attr( $new_instance['thumb_width'] );
		
		/* The text input field is returning a text value, so we escape it. */
		$instance['thumb_height'] = esc_attr( $new_instance['thumb_height'] );
		
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
						'title' => __( 'Photo Galleries', 'woothemes' ), 
						'category' => 0, 
						'limit' => 5, 
						'width' => 115, 
						'height' => 100, 
						'show_thumbnails' => 1, 
						'thumb_width' => 50, 
						'thumb_height' => 50
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$categories = $woothemes_photos->get_categories( 'limit=' );
?>
       <!-- Widget Title: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
       </p>
       <!-- Widget Limit: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>"  value="<?php echo intval( esc_attr( $instance['limit'] ) ); ?>" class="" size="3" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
       </p>
       <!-- Widget Width & Height: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'width' ); ?>"  value="<?php echo $instance['width']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'width' ); ?>" />
	       <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'height' ); ?>"  value="<?php echo $instance['height']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'height' ); ?>" />
       </p>
       <p><small><?php _e( 'The width and height of the cover image', 'woothemes' ); ?></small></p>
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
		<!-- Widget Show Thumbnails: Checkbox Input -->
       	<p>
        	<input id="<?php echo $this->get_field_id( 'show_thumbnails' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnails' ); ?>" type="checkbox"<?php checked( $instance['show_thumbnails'], 1 ); ?> />
        	<label for="<?php echo $this->get_field_id( 'show_thumbnails' ); ?>"><?php _e( 'Show Thumbnails', 'woothemes' ); ?></label>
	   	</p>
	   	<p><small><?php _e( 'Show the first 5 thumbnails below each gallery', 'woothemes' ); ?></small></p>
	   	 <!-- Widget Thumbnail Width & Height: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'thumb_width' ); ?>"><?php _e( 'Width:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'thumb_width' ); ?>"  value="<?php echo $instance['thumb_width']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'thumb_width' ); ?>" />
	       <label for="<?php echo $this->get_field_id( 'thumb_height' ); ?>"><?php _e( 'Height:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'thumb_height' ); ?>"  value="<?php echo $instance['thumb_height']; ?>" class="" size="3" id="<?php echo $this->get_field_id( 'thumb_height' ); ?>" />
       </p>
       <p><small><?php _e( 'The width and height of the thumbnail images', 'woothemes' ); ?></small></p>
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
add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Galleries");' ), 1 ); 
?>