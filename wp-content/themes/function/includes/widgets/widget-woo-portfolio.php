<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Portfolio Widget
 *
 * A WooThemes standardized portfolio widget.
 *
 * @package WordPress
 * @subpackage WooFramework
 * @category Widgets
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * protected $woothemes_widget_cssclass
 * protected $woothemes_widget_description
 * protected $woothemes_widget_idbase
 * protected $woothemes_widget_title
 * 
 * - __construct()
 * - widget()
 * - update()
 * - form()
 * - get_orderby_options()
 */
class Widget_Woo_Portfolio extends WP_Widget {
	protected $woothemes_widget_cssclass;
	protected $woothemes_widget_description;
	protected $woothemes_widget_idbase;
	protected $woothemes_widget_title;

	/**
	 * Constructor function.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->woothemes_widget_cssclass = 'widget_woo_portfolio';
		$this->woothemes_widget_description = __( 'Recent portfolio items on your site.', 'woothemes' );
		$this->woothemes_widget_idbase = 'woo_portfolio';
		$this->woothemes_widget_title = __( 'Woo - Portfolio', 'woothemes' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woothemes_widget_cssclass, 'description' => $this->woothemes_widget_description );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => $this->woothemes_widget_idbase );

		/* Create the widget. */
		$this->WP_Widget( $this->woothemes_widget_idbase, $this->woothemes_widget_title, $widget_ops, $control_ops );	
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since  1.0.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		global $post;

		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
			
		/* Before widget (defined by themes). */
		echo $before_widget;

		$args = array();

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) { echo $before_title . $title . $after_title; }
		
		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->woothemes_widget_cssclass . '_top' );

		// Integer values.
		if ( isset( $instance['limit'] ) && ( 0 < count( $instance['limit'] ) ) ) { $args['limit'] = intval( $instance['limit'] ); }
		if ( isset( $instance['specific_id'] ) && ( 0 < count( $instance['specific_id'] ) ) ) { $args['id'] = intval( $instance['specific_id'] ); }

		// Select boxes.
		if ( isset( $instance['orderby'] ) && in_array( $instance['orderby'], array_keys( $this->get_orderby_options() ) ) ) { $args['orderby'] = $instance['orderby']; }
		if ( isset( $instance['order'] ) && in_array( $instance['order'], array_keys( $this->get_order_options() ) ) ) { $args['order'] = $instance['order']; }

		// Display the items.
		
		// If a specific item ID is set.
		if ( 0 < $args['id'] ) {
			$data = get_posts( array( 'numberposts'	=> 1, 'include'	=>	array( intval( $args['id'] ) ), 'post_type'	=>	'portfolio' ) );
			if ( ! is_wp_error( $data ) ) {
				echo '<ul class="portfolio-item-list">' . "\n";
				foreach ( $data as $k => $post ) {
					setup_postdata( $post );
					echo '<li>' . woo_image( 'width=210&height=130&noheight=true&return=true' ) . '</li>' . "\n";
				}
				wp_reset_postdata();
				echo '</ul>' . "\n";
			}
		} else {
		// If displaying general items.
			$query_args = array( 'post_type' => 'portfolio', 'posts_per_page' => $args['limit'], 'order' => $args['order'], 'orderby' => $args['orderby'] );
			
			if ( 0 < intval( $instance['term_id'] ) ) {
				$query_args['tax_query'] = array(
												array( 'taxonomy' => 'portfolio-gallery', 'field' => 'id', 'terms' => intval( $instance['term_id'] ) )
												);
			}

			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				echo '<ul class="portfolio-item-list">' . "\n";
				while ( $query->have_posts() ) {
					$query->the_post();

					echo '<li><a href="' . esc_url( get_permalink( get_the_ID() ) ) . '" title="' . esc_attr( the_title_attribute( array( 'echo' => false ) ) ) . '">' . woo_image( 'width=210&height=130&noheight=true&return=true&link=img' ) . '</a></li>' . "\n";
				}
				wp_reset_postdata();
				echo '</ul>' . "\n";
			}
		}

		// Add actions for plugins/themes to hook onto.
		do_action( $this->woothemes_widget_cssclass . '_bottom' );

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

		/* Make sure the integer values are definitely integers. */
		$instance['limit'] = intval( $new_instance['limit'] );
		$instance['specific_id'] = intval( $new_instance['specific_id'] );
		$instance['term_id'] = intval( $new_instance['term_id'] );

		/* The select box is returning a text value, so we escape it. */
		$instance['orderby'] = esc_attr( $new_instance['orderby'] );
		$instance['order'] = esc_attr( $new_instance['order'] );

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
			'title' => '', 
			'limit' => 5, 
			'orderby' => 'menu_order', 
			'order' => 'DESC', 
			'specific_id' => '', 
			'term_id' => ''
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
		<!-- Widget Order By: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order By:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>">
			<?php foreach ( $this->get_orderby_options() as $k => $v ) { ?>
				<option value="<?php echo $k; ?>"<?php selected( $instance['orderby'], $k ); ?>><?php echo $v; ?></option>
			<?php } ?>       
			</select>
		</p>
		<!-- Widget Order: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order Direction:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>">
			<?php foreach ( $this->get_order_options() as $k => $v ) { ?>
				<option value="<?php echo $k; ?>"<?php selected( $instance['order'], $k ); ?>><?php echo $v; ?></option>
			<?php } ?>       
			</select>
		</p>
		<!-- Widget Portfolio Gallery: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'term_id' ); ?>"><?php _e( 'Portfolio Gallery:', 'woothemes' ); ?></label>
			<?php wp_dropdown_categories( array( 'show_option_all' => __( 'All', 'woothemes' ), 'name' => $this->get_field_name( 'term_id' ), 'selected' => $instance['term_id'], 'taxonomy' => 'portfolio-gallery', 'class' => 'widefat' ) ); ?>
		</p>
		<!-- Widget ID: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'specific_id' ); ?>"><?php _e( 'Specific ID (optional):', 'woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'specific_id' ); ?>"  value="<?php echo $instance['specific_id']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'specific_id' ); ?>" />
		</p>
		<p><small><?php _e( 'Display a specific portfolio item, rather than a list. All other settings are ignored.', 'woothemes' ); ?></small></p>
<?php
	} // End form()

	/**
	 * Get an array of the available orderby options.
	 * @since  1.0.0
	 * @return array
	 */
	protected function get_orderby_options () {
		return array(
					'none' => __( 'No Order', 'woothemes' ), 
					'ID' => __( 'Entry ID', 'woothemes' ), 
					'title' => __( 'Title', 'woothemes' ), 
					'date' => __( 'Date Added', 'woothemes' ), 
					'menu_order' => __( 'Specified Order Setting', 'woothemes' )
					);
	} // End get_orderby_options()

	/**
	 * Get an array of the available order options.
	 * @since  1.0.0
	 * @return array
	 */
	protected function get_order_options () {
		return array(
					'asc' => __( 'Ascending', 'woothemes' ), 
					'desc' => __( 'Descending', 'woothemes' )
					);
	} // End get_order_options()
} // End Class

/* Register the widget. */
add_action( 'widgets_init', create_function( '', 'return register_widget("Widget_Woo_Portfolio");' ), 1 ); 
?>