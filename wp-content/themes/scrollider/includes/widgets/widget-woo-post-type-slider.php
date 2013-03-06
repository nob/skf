<?php
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'Please do not load this screen directly. Thanks!' );
}

/**
 * WooThemes Post Slider Widget
 *
 * A WooThemes standardized post slider widget for any post type.
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
 * - enqueue_scripts()
 */
class Woo_Widget_Slider extends WP_Widget {
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
		$this->woo_widget_cssclass = 'widget_woo_slider';
		$this->woo_widget_description = __( 'A WooThemes standardized recent post slider widget for any post type.', 'woothemes' );
		$this->woo_widget_idbase = 'woo_slider';
		$this->woo_widget_title = __( 'Woo - Post Type Slideshow', 'woothemes' );

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
		if ( $title ) { $title = $before_title . $title . $after_title; }

		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->woo_widget_cssclass . '_top' );

		// Load widget content here.
		$html = '';

		$args = array(
			'post_type' => $instance['post_type'],
			'posts_per_page' => $instance['limit']
		);

		$query = new WP_Query($args);

		/* Set widget options as class names */
		$class_tail = '';
		if( isset( $instance['effect'] ) ) { $class_tail .= 'effect_' . $instance['effect']; }
		if( isset( $instance['navigation'] ) && ! $instance['navigation'] ) { $class_tail .= ' hide_nav'; }
		if( isset( $instance['autoslide'] ) ) { $autoslide = $instance['autoslide']; } else { $autoslide = 0; }
		if ( $instance['limit'] == 1 ) { $class_tail .= ' oneslide'; }

		$html =  '<div class="flexslider">' . "\n";
		$html .= '<input type="hidden" class="autoslide" value="' . $autoslide . '"/>' . "\n";
		$html .= '<ul class="slides ' . esc_attr( $class_tail ) . '">' . "\n";

		while ( $query->have_posts() ) { $query->the_post();

			$html .=  '<li class="slide">' . "\n";
			$woo_image = woo_image( 'width=500&class=thumbnail&noheight=true&return=true&single=true' );
			if ( $woo_image == '' ) {
				$noimage = ' no-image';
			} else {
				$noimage = '';
			}
			$html .= $woo_image;
			$html .= '<div class="widget-slider-inner' . $noimage . '">' . "\n";
			$html .= $title . "\n";
			$html .= '<a href="' . esc_url( get_permalink( get_the_ID() ) ) . '" class="widget-slider-title" title="' . esc_attr( get_the_title() ) . '">' . get_the_title() . '</a>' . "\n";
			$html .= '<div class="widget-slider-excerpt">' . get_the_excerpt() . '</div>' . "\n";

			$button_text = sprintf( __( 'View %s', 'woothemes' ), $instance['post_type'] );

			switch( $instance['post_type'] ) {
				case 'product':
					if ( function_exists( 'woocommerce_product_add_to_cart_url' ) ) {
						$url = woocommerce_product_add_to_cart_url( array( 'id' => get_the_ID() ) );
						$button_text = __( 'Add to Cart', 'woothemes' );
					} else {
						$url = get_permalink( get_the_ID() );
					}
					$html .= '<a class="widget-slider-button button" href="' . esc_url( $url ) . '">' . $button_text . '</a>' . "\n";
				break;
				case 'portfolio':
					$html .= '<a class="widget-slider-button button" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __( 'View Item', 'woothemes' ) . '</a>' . "\n";
				break;
				default:
					$html .= '<a class="widget-slider-button button" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . $button_text . '</a>' . "\n";
				break;
			}
			$noimage = '';
			$html .= '</div>' . "\n" . '</li>' . "\n";
		}

		$html .= '</ul>' . "\n" . '</div>' . "\n";

		echo $html;

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
		$instance['post_type'] = esc_attr( $new_instance['post_type'] );
		$instance['limit'] = intval( $new_instance['limit'] );
		$instance['effect'] = esc_attr( $new_instance['effect'] );
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
						'post_type' => 'post',
						'limit' => 5,
						'animation' => 'fade',
						'navigation' => 1,
						'autoslide' => 0
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>

		<!-- Post Type: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>">
				<option value="<?php echo esc_attr( 'post' ); ?>"<?php selected( $instance['post_type'], 'post' ); ?>><?php _e( 'Posts', 'woothemes' ); ?></option>
				<?php
				$args = array(
					'_builtin' => false
					);
				$post_types = get_post_types($args,'objects');
				foreach ($post_types as $type => $details ) {
					if( !in_array( $type , array( 'wooframework' , 'shop_order' , 'product_variation' , 'shop_coupon' ) ) ) { ?>
						<option value="<?php echo $type; ?>"<?php selected( $instance['post_type'], $type ); ?>><?php _e( $details->labels->name, 'woothemes' ); ?></option>
				<?php }
				} ?>
			</select>
		</p>

		<!-- Widget Limit: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>"  value="<?php echo $instance['limit']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
		</p>

		<!-- Animation Effect: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'effect' ); ?>"><?php _e( 'Animation effect:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'effect' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'effect' ); ?>">
				<option value="<?php _e( 'fade', 'woothemes' ); ?>"<?php selected( $instance['effect'], 'fade' ); ?>><?php _e( 'Fade', 'woothemes' ); ?></option>
				<option value="<?php _e( 'slide', 'woothemes' ); ?>"<?php selected( $instance['effect'], 'slide' ); ?>><?php _e( 'Slide', 'woothemes' ); ?></option>
			</select>
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
		wp_register_script($this->woo_widget_cssclass, get_template_directory_uri().'/includes/js/post-type-slider.js', array('jquery', 'flexslider'), '1.0.0', true);
		wp_enqueue_script($this->woo_widget_cssclass);
	} // End enqueue_scripts()

} // End Class

/* Register the widget. */
add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Slider");' ), 1 );
?>