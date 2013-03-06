<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom videos widget.
Date Created: 2012-01-19.
Last Modified: 2012-01-19.
Author: Matty.
Since: 1.0.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()
- function load_javascript ()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_Videos extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_Videos () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_woo_videos', 'description' => __( 'The videos on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_videos' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_videos', __( 'Woo - Videos', 'woothemes' ), $widget_ops, $control_ops );
		
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
		global $woothemes_videos;
		$html = '';
		
		add_action( 'wp_footer', array( &$this, 'load_javascript' ) );
		
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
		do_action( 'widget_woo_videos_top' );
		
		$query_args = array( 'limit' => $instance['limit'], 'id' => $category );
		
		$videos = $woothemes_videos->get_videos( $query_args );
		
		$html = '';
		
		if ( count( $videos ) > 0 ) {
			$count = 0;
			$video_thumbnails = ''; // Setup our thumbnails in here for output below.
			
			$html .= '<div class="box">' . "\n";
			
			foreach ( $videos as $k => $v ) {
				// Don't display this video if we're already on it's single detail screen.
				if ( is_single( $v->ID ) ) { continue; }
				$count++;
				$class = 'inactive';
				if ( $count == 1 ) {
					$class = 'active';
				}
				
				$html .= '<div id="video-id-' . $v->ID . '" class="video-display ' . $class . '">' . "\n";
				$html .= woo_get_embed( 'embed', $width, $height, 'widget_video', $v->ID );
				$html .= '</div><!--/.video-display-->' . "\n";
				
				$video_thumbnails .= '<li id="video-title-' . $v->ID . '" class="' . $class . ' alignleft" title="' . esc_attr( get_the_title( $v->ID ) ) . '"><div class="rounded-border fl"><a href="' . get_permalink( $v ) . '" class="' . $class . '">' . woo_image( 'return=true&link=img&width=' . '100' . '&height=' . '100' . '&class=rounded fl woo-video-thumb&id=' . $v->ID . '&alt=' . esc_attr( get_the_title( $v->ID ) ) ) . '</a></div></li>' . "\n";
								
			}
			
			$html .= '<ul class="video-thumbnails">' . "\n";
			$html .= $video_thumbnails;
			$html .= '</ul>' . "\n";
			$html .= '</div>' . "\n";
				
		} else {
			$html = '<p>' . __( 'No videos are currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
				
		echo $html; 
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_videos_bottom' );
				
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
   		global $woothemes_videos;
       /* Set up some default widget settings. */
		$defaults = array(
						'title' => __( 'Videos', 'woothemes' ), 
						'category' => 0, 
						'limit' => 5, 
						'width' => 300, 
						'height' => 200
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$categories = $woothemes_videos->get_categories();
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
       <p><small><?php _e( 'The width and height of the video clips', 'woothemes' ); ?></small></p>
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
	
	/**
	 * load_javascript function.
	 * 
	 * @access public
	 * @return void
	 */
	function load_javascript() {
		global $woothemes_videos;

		if ( isset( $woothemes_videos->widget_js_loaded ) && ( $woothemes_videos->widget_js_loaded === true ) ) {
			return;
		}
		
		wp_enqueue_script( 'jquery' );
?>
<!--JavaScript for the Woo - Videos widget-->
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery( '.video-thumbnails a.active' ).click( function ( e ) { return false; });
	
	jQuery( '.video-thumbnails a' ).click( function ( e ) {
		var video_id = jQuery( this ).parents( 'li' ).attr( 'id' ).replace( 'video-title-', '' );
		var parentObj = jQuery( this ).parents( '.widget' );
		
		parentObj.find( '.video-display.active' ).addClass( 'inactive' ).removeClass( 'active' );
		parentObj.find( '.video-display#video-id-' + video_id ).addClass( 'active' ).removeClass( 'inactive' );
		
		parentObj.find( '.video-thumbnails .active' ).removeClass( 'active' ).addClass( 'inactive' );
		jQuery( this ).removeClass( 'inactive' ).addClass( 'active' ).parent().removeClass( 'inactive' ).addClass( 'active' );
		
		return false;
	});
});
</script>
<?php		
		// Tell $woothemes_videos that we've loaded the script.
		$woothemes_videos->widget_js_loaded = true;
	} // End load_javascript()
} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------
  
  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Videos");' ), 1 ); 
?>