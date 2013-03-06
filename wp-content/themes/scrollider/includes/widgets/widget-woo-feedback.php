<?php
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'Please do not load this screen directly. Thanks!' );
}

/**
 * WooThemes Starter Widget
 *
 * A WooThemes standardized starter widget.
 *
 * @package WordPress
 * @subpackage WooFramework
 * @category Widgets
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * var $woo_widget_cssclass
 * var $woo_widget_description
 * var $woo_widget_idbase
 * var $woo_widget_title
 * 
 * - __construct()
 * - widget()
 * - update()
 * - form()
 */
class Woo_Widget_Feedback extends WP_Widget {
	private $woo_widget_cssclass;
	private $woo_widget_description;
	private $woo_widget_idbase;
	private $woo_widget_title;

	/**
	 * Constructor function.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->woo_widget_cssclass = 'widget_woo_feedback';
		$this->woo_widget_description = __( 'This is the feedback widget.', 'woothemes' );
		$this->woo_widget_idbase = 'woo_feedback';
		$this->woo_widget_title = __( 'Woo - Feedback', 'woothemes' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => $this->woo_widget_idbase );

		/* Create the widget. */
		$this->WP_Widget( $this->woo_widget_idbase, $this->woo_widget_title, $widget_ops, $control_ops );	
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since  1.0.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {  
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
			
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) { echo $before_title . $title . $after_title; }
		
		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->woo_widget_cssclass . '_top' );
		
		// Load widget content here.
		$args = array(
			'post_type' => 'feedback',
			'posts_per_page' => $instance['limit']
		);

		$query = new WP_Query($args);

		$html = '';

		$html .= '<ul>';

		while ( $query->have_posts() ) { $query->the_post();

			global $post;

			$feedback_excerpt = get_post_meta( $post->ID, 'feedback_excerpt', true );
			if ( $feedback_excerpt != '' ) { 
				$excerpt = '<p>' . stripslashes( $feedback_excerpt ) . '</p>'; 
			} else { 
				$excerpt = '<p>' . get_the_excerpt() . '</p>'; 
			}
			$feedback_gravatar = get_post_meta( $post->ID, 'feedback_gravatar', true ); 
			$feedback_author = get_post_meta( $post->ID, 'feedback_author', true);
	    	$feedback_www = get_post_meta( $post->ID, 'feedback_website_title', true);
	    	$feedback_url = get_post_meta( $post->ID, 'feedback_url', true);
				
			$html .= '<li>';

			$html .= '<span class="gravatar">' . get_avatar( $feedback_gravatar , '80' ) . '</span>';

			$html .= '<span class="name">' . $feedback_author . '</span>';

			$html .= '<span class="website"><a href="' . $feedback_url . '">'  . $feedback_url . '</a></span>';
			
			$html .= $excerpt;

			$html .= '</li>';

		} 

		$html .= '</ul>';
		
		/*
			NOTE:
			We use a variable to store the output here, as doing tons of "echo" statements
			can get expensive on the server, and we may need to do calculations, etc, prior
			to outputting anything.
			It's also nice and neat. :)
		*/
		
		echo $html; // If using the $html variable to store the output, you need this. ;)
		
		// Add actions for plugins/themes to hook onto.
		do_action( $this->woo_widget_cssclass . '_bottom' );

		/* After widget (defined by themes). */
		echo $after_widget;
	} // End widget()

	/**
	 * Method to update the settings from the form() method.
	 * @since  1.0.0
	 * @param  array $new_instance New settings.
	 * @param  array $old_instance Previous settings.
	 * @return array               Updated settings.
	 */
	public function update ( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		/* Make sure the limit value is definitely an integer. */
		$instance['limit'] = intval( $new_instance['limit'] );

		return $instance;
	} // End update()

	/**
	 * The form on the widget control in the widget administration area.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff.
	 * @since  1.0.0
	 * @param  array $instance The settings for this instance.
	 * @return void
	 */
    public function form( $instance ) {       
   
		/* Set up some default widget settings. */
		/* Make sure all keys are added here, even with empty string values. */
		$defaults = array(
						'title' => __( 'Feedback', 'woothemes' ),
						'limit' => 3
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
			<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>"  value="<?php echo $instance['limit']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
		</p>		

<?php
	} // End form()
} // End Class

/* Register the widget. */
add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Feedback");' ), 1 ); 
?>