<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom albums widget.
Date Created: 2012-01-15.
Last Modified: 2012-01-15.
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

class Woo_Widget_Albums extends WP_Widget {

	/**
	 * Constructor function.
	 *
	 * @description Sets up the widget.
	 * @access public
	 * @return void
	 */
	function Woo_Widget_Albums () {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_albums', 'description' => __( 'The album releases on your site', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_albums' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_albums', __( 'Woo - Albums', 'woothemes' ), $widget_ops, $control_ops );
		
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
		global $woothemes_discography, $post;
		
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
		do_action( 'widget_woo_albums_top' );
		
		$query_args = array( 'limit' => $instance['limit'], 'id' => $category );
		
		$albums = $woothemes_discography->get_albums( $query_args );
		
		$html = '';
		
		if ( count( $albums ) > 0 ) {
			$html .= '<ul>' . "\n";
			foreach ( $albums as $k => $post ) {
				setup_postdata( $post );
				
				$meta = get_post_custom( $post->ID );
				
				$html .= '<li>' . "\n";
				$html .= '<a href="' . $post->guid . '" class="woo-album-thumb-anchor">' . woo_image( 'return=true&link=img&width=' . $width . '&height=' . $height . '&class=alignleft woo-album-thumb rounded' ) . '</a>' . "\n";
				$html .= '<h4><a href="' . get_permalink( $post ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">' . get_the_title() . '</a></h4>' . "\n";
				
				// Release Date
				if ( isset( $meta['_release_date'] ) && ( $meta['_release_date'][0] != '' ) ) {
					$html .= '<p>' . __( 'Released:', 'woothemes' ) . ' ' . date_i18n( get_option( 'date_format' ), $meta['_release_date'][0] ) . '</p>' . "\n";
				}
				
				// Catalog ID
				if ( isset( $meta['_catalog_id'] ) && ( $meta['_catalog_id'][0] != '' ) ) {
					$html .= '<p>' . $meta['_catalog_id'][0] . '</p>' . "\n";
				}
				
				if ( class_exists( 'woocommerce' ) && isset( $meta['_product_id'] ) ) {
					$product = new WC_Product( intval( $meta['_product_id'][0] ) );
					
					$html .= do_shortcode( '[add_to_cart id="' . $product->id . '"]' );
				}

				$html .= '<div class="fix"></div>' . "\n";
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>' . "\n";
		} else {
			$html = '<p>' . __( 'No albums are currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
		
		echo $html; 
		
		$post = $saved_post;
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_albums_bottom' );

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
   		global $woothemes_discography;
   		
       /* Set up some default widget settings. */
		$defaults = array(
						'title' => __( 'Albums', 'woothemes' ), 
						'category' => 0, 
						'limit' => 5, 
						'width' => 75, 
						'height' => 75
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$categories = $woothemes_discography->get_categories();
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
<?php
	} // End form()
} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------
  
  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Albums");' ), 1 ); 
?>