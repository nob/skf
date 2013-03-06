<?php
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'Please do not load this screen directly. Thanks!' );
}

/**
 * WooThemes News From Blog Widget
 *
 * Slinding widget that displays your latest posts.
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
class Woo_Widget_NewsFromBlog extends WP_Widget {
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
		$this->woo_widget_cssclass = 'widget_woo_newsfromblog';
		$this->woo_widget_description = __( 'Sliding widget that displays your latest posts.', 'woothemes' );
		$this->woo_widget_idbase = 'woo_newsfromblog';
		$this->woo_widget_title = __( 'Woo - News From Blog', 'woothemes' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => $this->woo_widget_idbase );

		/* Create the widget. */
		$this->WP_Widget( $this->woo_widget_idbase, $this->woo_widget_title, $widget_ops, $control_ops );

		if( ! is_admin() && is_active_widget( false, false, $this->woo_widget_idbase ) ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		}

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
		if ( $title ) { $title = $before_title . '<span>' . $title . '</span>' . $after_title; }
		
		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->woo_widget_cssclass . '_top' );
		
		// Load widget content here.
		$html = '';

		$args = array(
			'post_type' => 'post',
			'posts_per_page' => $instance['limit']
		);

		$query = new WP_Query($args);
		$query_count = $query->post_count;

		/* Set widget options as class names */
		$class_tail = '';
		if( isset( $instance['navigation'] ) && ! $instance['navigation'] ) { $class_tail .= 'hide_nav'; }
		if( isset( $instance['autoslide'] ) ) { $autoslide = $instance['autoslide']; } else { $autoslide = 0; }
?>

		<div class="section-blog flexslider">
			<input type="hidden" class="autoslide" value="<?php echo $autoslide; ?>"/>
			<?php echo $title; ?>
			<ul class="slides <?php echo $class_tail; ?>"> 

				<li class="slide">

				<?php $count = 0; while ( $query->have_posts() ) { $query->the_post(); $count++; ?>
				
						<?php get_template_part( 'content', 'home' );	 ?>

					<?php if ($count % 3 == 0 && $count != $query_count ): ?></li><li class="slide"><?php endif; ?>

				<?php } ?>

				</li>

			</ul>
		</div>
		<div class="fix"></div>
		<div class="flexslider-nav-container"></div>
	
<?php
		wp_reset_postdata();
		
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

		/* Save widget options */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = intval( $new_instance['limit'] );
		$instance['navigation'] = (bool) esc_attr( $new_instance['navigation'] );
		$instance['autoslide'] = intval( esc_attr( $new_instance['autoslide'] ) );
		
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
						'title' => __( 'Recent Posts', 'woothemes' ), 
						'limit' => 6,
						'navigation' => 1,
						'autoslide' => 0
					);
		
		$instance = wp_parse_args( (array) $instance, $defaults );
?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>

		<!-- Widget Limit: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>"  value="<?php echo intval( $instance['limit'] ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
		</p>

		<!-- Navigation: Checkbox Input -->
       	<p>
        	<input id="<?php echo $this->get_field_id( 'navigation' ); ?>" name="<?php echo $this->get_field_name( 'navigation' ); ?>" type="checkbox"<?php checked( $instance['navigation'], 1 ); ?> />
        	<label for="<?php echo $this->get_field_id( 'navigation' ); ?>"><?php _e( 'Show navigation', 'woothemes' ); ?></label>
	   	</p>

	   	<!-- Auto slide interval: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'autoslide' ); ?>"><?php _e( 'Auto slide interval:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'autoslide' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'autoslide' ); ?>">
				<option value="0"<?php selected( $instance['autoslide'], '0' ); ?>><?php _e( 'Off', 'woothemes' ); ?></option>
				<option value="1"<?php selected( $instance['autoslide'], '1' ); ?>><?php _e( '1', 'woothemes' ); ?></option>
				<option value="2"<?php selected( $instance['autoslide'], '2' ); ?>><?php _e( '2', 'woothemes' ); ?></option>
				<option value="3"<?php selected( $instance['autoslide'], '3' ); ?>><?php _e( '3', 'woothemes' ); ?></option>
				<option value="4"<?php selected( $instance['autoslide'], '4' ); ?>><?php _e( '4', 'woothemes' ); ?></option>
				<option value="5"<?php selected( $instance['autoslide'], '5' ); ?>><?php _e( '5', 'woothemes' ); ?></option>
				<option value="6"<?php selected( $instance['autoslide'], '6' ); ?>><?php _e( '6', 'woothemes' ); ?></option>
				<option value="7"<?php selected( $instance['autoslide'], '7' ); ?>><?php _e( '7', 'woothemes' ); ?></option>
				<option value="8"<?php selected( $instance['autoslide'], '8' ); ?>><?php _e( '8', 'woothemes' ); ?></option>
				<option value="9"<?php selected( $instance['autoslide'], '9' ); ?>><?php _e( '9', 'woothemes' ); ?></option>
				<option value="10"<?php selected( $instance['autoslide'], '10' ); ?>><?php _e( '10', 'woothemes' ); ?></option>
			</select>
			<span class="description">Time in seconds between slides</span>
		</p>
<?php
	} // End form()

	/**
	 * Register and enqueue JS for widget
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->woo_widget_cssclass, get_template_directory_uri() . '/includes/js/news-from-blog.js', array( 'jquery', 'flexslider' ), '1.0.0', true );
		wp_enqueue_script( $this->woo_widget_cssclass );
	} // End enqueue_scripts()

} // End Class

/* Register the widget. */
add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_NewsFromBlog");' ), 1 ); 
?>