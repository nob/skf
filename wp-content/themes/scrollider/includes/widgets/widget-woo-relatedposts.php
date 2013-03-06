<?php
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'Please do not load this screen directly. Thanks!' );
}

/**
 * WooThemes Related Posts Widget
 *
 * Slinding widget that displays related posts.
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
class Woo_Widget_RelatedPosts extends WP_Widget {
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
		$this->woo_widget_cssclass = 'widget_woo_relatedposts widget_woo_newsfromblog';
		$this->woo_widget_description = __( 'Slinding widget that displays related posts on single post screens.', 'woothemes' );
		$this->woo_widget_idbase = 'woo_relatedposts';
		$this->woo_widget_title = __( 'Woo - Related Posts', 'woothemes' );
		
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
		global $post, $post_id;

		if ( ! is_singular() || ( is_singular() && is_page() ) ) return;

		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
			
		/* Before widget (defined by themes). */
		echo $before_widget;

		$post_type = get_post_type();
		$obj = get_post_type_object( $post_type );

		// Get the specific terms we want to work with for related entries.
		$terms = $this->get_post_terms( $obj );

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			if ( stristr( $title, '%post_type%' ) ) {
				$title = str_replace( '%post_type%', $obj->labels->name, $title );
			}
			$title = $before_title . '<span>' . $title . '</span>' . $after_title;
		}
		
		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->woo_widget_cssclass . '_top' );
		
		// Load widget content here.
		$html = '';

		$args = array(
			'post_type' => get_post_type(),
			'limit' => $instance['limit']
		);

		if ( is_array( $terms ) && 0 < count( $terms ) ) {
			$args['specific_terms'] = $terms;
		}

		$query = woo_get_posts_by_taxonomy( $args );

		// If the data isn't present or is incorrect, return and don't display related posts.
		if ( is_wp_error( $query ) || ! is_array( $query ) || 0 >= count( $query ) ) return;

		/* Set widget options as class names */
		$class_tail = '';
		if( isset( $instance['navigation'] ) && ! $instance['navigation'] ) { $class_tail .= ' hide_nav'; }
		if( isset( $instance['autoslide'] ) ) { $autoslide = $instance['autoslide']; } else { $autoslide = 0; }
?>
		<div class="section-blog flexslider">
			<input type="hidden" class="autoslide" value="<?php echo $autoslide; ?>"/>
			<?php echo $title; ?>
			<ul class="slides">

				<li class="slide">

				<?php $count = 0; foreach ( $query as $k => $post ) { setup_postdata( $post ); $count++; $post_id = get_the_ID(); ?>
						<?php get_template_part( 'content', 'home' ); ?>
						<?php if ($count % 3 == 0 && $count != count( $query )): ?></li><li class="slide"><?php endif; ?>
				<?php } ?>

				</li>

			</ul>
		</div>
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
						'title' => __( 'Related Posts', 'woothemes' ), 
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
			<span class="description"><?php _e( 'Time in seconds between slides', 'woothemes' ); ?></span>
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

	/**
	 * Get the terms for a specific post (detected within the assigned taxonomies for that post type)
	 * @since  1.0.0
	 * @param  object $obj Post type object.
	 * @return array       Assigned terms.
	 */
	private function get_post_terms ( $obj ) {
		global $post;
		$terms = array();

		$taxonomies = array();
		if ( isset( $obj->taxonomies ) &&  0 < count( $obj->taxonomies ) ) {
			$taxonomies = $obj->taxonomies;
		} else {
			if ( 'post' == $obj->name ) {
				$taxonomies = array( 'category', 'post_tag' );
			}
		}

		if ( 0 < count( $taxonomies ) ) {
			foreach ( $taxonomies as $k => $v ) {
				$tax_terms = get_the_terms( get_the_ID(), $v );
				if ( ! is_wp_error( $tax_terms ) && is_array( $tax_terms ) ) {
					foreach ( $tax_terms as $i => $j ) {
						$terms[] = $j->slug;
					}
				}
			}
		}

		return $terms;
	} // End get_post_terms()
} // End Class

/* Register the widget. */
add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_RelatedPosts");' ), 1 ); 
?>