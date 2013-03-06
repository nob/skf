<?php
/**
 * WooThemes Events Manager.
 *
 * Controls the management of events.
 *
 * @category Modules
 * @package WordPress
 * @subpackage WooFramework
 * @author Matty at WooThemes
 * @date 2012-01-03.
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - var $token
 * - var $singular
 * - var $plural
 * - var $rewrite_tag
 * - var $rewrite_path
 * - var $dir
 *
 * - var $template_url
 *
 * - Constructor Function
 * - function init()
 * - function register_post_type()
 * - function register_taxonomies()
 * - function custom_post_permastruct()
 * - function customise_wooframework_meta_box()
 * - function enqueue_styles()
 * - function add_sortable_columns()
 * - function add_column_headings()
 * - function add_column_data()
 * - function sort_on_custom_columns()
 * - function order_events_by_date()
 * - function list_table_views()
 * - function change_alldates_text()
 * - function customise_featured_image_box_linktext()
 * - function enter_title_here()
 * - function display_formatted_date_text()
 * - function display_formatted_event_date_status()
 * - function get_tours()
 * - function get_events_by_tour()
 * - function get_tour_dates()
 * - function add_tour_column_headings()
 * - function add_tour_column_data()
 * - function get_next_weekend_dates()
 * - function get_dates_of_week()
 * - function get_event_details()
 */

class WooThemes_Events {

	/**
	 * Variables
	 *
	 * @description Setup of variable placeholders, to be populated when the constructor runs.
	 * @since 1.0.0
	 */

	var $token;
	var $singular;
	var $plural;
	var $rewrite_tag;
	var $rewrite_path;
	var $dir;
	
	var $taxonomies;
	
	var $template_url;

	/**
	 * WooThemes_Events function.
	 *
	 * @description Constructor function. Sets up the class and registers variable action hooks.
	 * @access public
	 * @return void
	 */
	function WooThemes_Events () {
		$this->token = 'event';
		$this->singular = __( 'Event', 'woothemes' );
		$this->plural = __( 'Events', 'woothemes' );
		$this->rewrite_tag = '%woo_events_category%';
		$this->dir = 'woo-events';
		
		$this->taxonomies = array(
									'event_category' => array(
															'singular' => __( 'Category', 'woothemes' ), 
															'plural' => __( 'Categories', 'woothemes' ), 
															'rewrite' => 'event-categories'
														), 
									'event_tour' => array(
															'singular' => __( 'Tour', 'woothemes' ), 
															'plural' => __( 'Tours', 'woothemes' ), 
															'rewrite' => 'tours'
														)
								);
		
		$this->template_url = get_template_directory_uri();
	} // End WooThemes_Events()
	
	/**
	 * init function.
	 *
	 * @description This guy runs the show. Rocket boosters... engage!
	 * @access public
	 * @return void
	 */
	function init() {
		add_action( 'init', array( &$this, 'register_post_type' ), 1 );
		add_action( 'init', array( &$this, 'register_taxonomies' ), 1 );
		
		add_rewrite_tag( $this->rewrite_tag,'([^&]+)' );
		add_filter( 'post_type_link', array( &$this, 'custom_post_permastruct' ), 1, 4 );

		add_filter( 'pre_get_posts', array( &$this, 'order_events_by_date' ) );

		if ( is_admin() ) {
			global $pagenow;

			if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == $this->token ) ) {
				add_action( 'admin_print_styles-edit.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-edit-tags.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-post.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-post-new.php', array( &$this, 'enqueue_styles' ), 10 );
			}

			if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ) {
				add_filter( 'woothemes_metabox_settings', array( &$this, 'customise_wooframework_meta_box' ), 10, 3 );
				add_filter( 'gettext', array( &$this, 'customise_featured_image_box_linktext' ), 10, 2 );
			}
			
			// add_filter( 'manage_edit-' . $this->token . '_sortable_columns', array( &$this, 'add_sortable_columns' ), 10, 1 );
			add_filter( 'manage_edit-' . $this->token . '_columns', array( &$this, 'add_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( &$this, 'add_column_data' ), 10, 2 );
			// add_filter( 'pre_get_posts', array( &$this, 'sort_on_custom_columns' ), 10, 1 );
			
			if ( $pagenow == 'edit.php' ) {
				if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == $this->token ) ) {
					add_filter( 'pre_get_posts', array( &$this, 'order_events_by_date' ) );
					add_filter( 'views_edit-event', array( &$this, 'list_table_views' ), 10 );
					add_filter( 'gettext', array( &$this, 'change_alldates_text' ), 10, 2 );
				}
			}
			
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 10 );
			
			// Custom columns for the "event_tour" taxonomy.
			add_filter( 'manage_edit-' . 'event_tour' . '_columns', array( &$this, 'add_tour_column_headings' ), 10, 1 );
			add_action( 'manage_' . 'event_tour' . '_custom_column', array( &$this, 'add_tour_column_data' ), 10, 3 );
		}
	} // End init()

	/**
	 * register_post_type function.
	 * 
	 * @access public
	 * @return void
	 */
	function register_post_type () {
		$labels = array(
	    'name' => $this->plural,
	    'singular_name' => $this->singular,
	    'add_new' => _x( 'Add New', $this->token ),
	    'add_new_item' => sprintf( __( 'Add New %s', 'woothemes' ), $this->singular ),
	    'edit_item' => sprintf( __( 'Edit %s', 'woothemes' ), $this->singular ),
	    'new_item' => sprintf( __( 'New %s', 'woothemes' ), $this->singular ),
	    'all_items' => sprintf( __( 'All %s', 'woothemes' ), $this->plural ),
	    'view_item' => sprintf( __( 'View %s', 'woothemes' ), $this->singular ),
	    'search_items' => sprintf( __( 'Search %s', 'woothemes' ), $this->plural ),
	    'not_found' =>  sprintf( __( 'No %s Found', 'woothemes' ), $this->plural ),
	    'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'woothemes' ), $this->plural ), 
	    'parent_item_colon' => '',
	    'menu_name' => $this->plural
	
	  );
	  $args = array(
	    'labels' => $labels, 
	    'can_export' => true, 
	    'public' => true,
	    'exclude_from_search' => false, 
	    'publicly_queryable' => true,
	    'show_ui' => true, 
	    'show_in_menu' => true, 
	    'show_in_nav_menus' => true, 
	    'query_var' => true,
	    // 'rewrite' => apply_filters( 'woo_events_rewrite_base', 'events' ), 
	    'rewrite' => array( 'slug' => apply_filters( 'woo_events_rewrite_base', 'events' ) . '/' . $this->rewrite_tag . '' ),
	    'capability_type' => 'post',
	    'has_archive' => apply_filters( 'woo_events_archive_rewrite_base', 'events' ), 
	    'hierarchical' => false,
	    'menu_position' => 5, 
	    'menu_icon' => $this->template_url . '/includes/' . $this->dir . '/assets/images/icon_16.png', 
	    'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ), 
	    'taxonomies' => array_keys( $this->taxonomies )
	  );
	
		register_post_type( $this->token, $args );
	} // End register_post_type()
	
	/**
	 * register_taxonomies function.
	 * 
	 * @access public
	 * @return void
	 */
	function register_taxonomies () {
		global $pagenow;
		
		foreach ( $this->taxonomies as $k => $v ) {
			// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
				'name' => sprintf( _x( '%s', 'taxonomy general name', 'woothemes' ), $v['plural'] ),
				'singular_name' => sprintf( _x( '%s', 'taxonomy singular name', 'woothemes' ), $v['singular'] ),
				'search_items' =>  sprintf( __( 'Search %s', 'woothemes' ), $v['plural'] ),
				'all_items' => sprintf( __( 'All %s', 'woothemes' ), $v['plural'] ),
				'parent_item' => sprintf( __( 'Parent %s', 'woothemes' ), $v['singular'] ),
				'parent_item_colon' => sprintf( __( 'Parent %s:', 'woothemes' ), $v['singular'] ),
				'edit_item' => sprintf( __( 'Edit %s', 'woothemes' ), $v['singular'] ), 
				'update_item' => sprintf( __( 'Update %s', 'woothemes' ), $v['singular'] ),
				'add_new_item' => sprintf( __( 'Add New %s', 'woothemes' ), $v['singular'] ), 
				'new_item_name' => sprintf( __( 'New %s Name', 'woothemes' ), $v['singular'] ),
				'menu_name' => $v['plural']
			); 	
			
			$hierarchical = true;
			
			register_taxonomy( $k ,array( $this->token ), array(
				'hierarchical' => $hierarchical,
				'labels' => $labels,
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => $v['rewrite'], 'with_front' => false )
			));
		}
	} // End register_taxonomies()
	
	/**
	 * custom_post_permastruct function.
	 * 
	 * @access public
	 * @param string $permalink
	 * @param object $post
	 * @param string $leavename
	 * @param sting $sample
	 * @return string $permalink
	 */
	function custom_post_permastruct ( $permalink, $post, $leavename, $sample ) {
		// Abort if post is not a product
		if ( $post->post_type !== $this->token ) return $permalink;
		
		// Abort early if the placeholder rewrite tag isn't in the generated URL
		if ( false === strpos( $permalink, $this->rewrite_tag ) ) return $permalink;
		
		// Get the custom taxonomy terms in use by this post
		$terms = get_the_terms( $post->ID, 'event_category' );
		
		if ( empty( $terms ) ) {
			// If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
			$permalink = str_replace( $this->rewrite_tag, __( 'uncategorized', 'woothemes' ), $permalink );
		} else {
			// Replace the placeholder rewrite tag with the first term's slug
			$first_term = array_shift( $terms );
			
			$permalink = str_replace( $this->rewrite_tag, $first_term->slug, $permalink );
		}
		
		return $permalink;
	} // End custom_post_permastruct()
	
	/**
	 * customise_wooframework_meta_box function.
	 * 
	 * @access public
	 * @param array $settings
	 * @param string $type
	 * @param string $handle
	 * @return array $settings
	 */
	function customise_wooframework_meta_box ( $settings, $type, $handle ) {
		if ( $type == $this->token ) {
			$settings['title'] = __( 'Event Details', 'woothemes' );
		}
		
		return $settings;
	} // End customise_wooframework_meta_box()
	
	/**
	 * enqueue_styles function.
	 * 
	 * @access public
	 * @return void
	 */
	function enqueue_styles () {
		global $pagenow;

		wp_register_style( 'woo-' . $this->token . '-admininterface', $this->template_url . '/includes/' . $this->dir . '/assets/css/admin.css', '', '1.0.0' );
		
		if ( $pagenow == 'edit-tags.php' || ( get_query_var( 'post_type' ) == $this->token ) || ( get_post_type() == $this->token ) ) {
			wp_enqueue_style( 'woo-' . $this->token . '-admininterface' );
		}
	} // End enqueue_styles()
	
	/**
	 * add_sortable_columns function.
	 * 
	 * @access public
	 * @param array $columns
	 * @return array $columns
	 */
	function add_sortable_columns ( $columns ) {
		$columns['event_date'] = 'event_date';
		return $columns;
	} // End add_sortable_columns()
	
	/**
	 * add_column_headings function.
	 * 
	 * @access public
	 * @param array $defaults
	 * @return array $new_columns
	 */
	function add_column_headings ( $defaults ) {
		
		$new_columns['cb'] = '<input type="checkbox" />';
		// $new_columns['id'] = __( 'ID' );
		$new_columns['title'] = _x( 'Event Title', 'column name', 'woothemes' );
		$new_columns['event_date'] = __( 'Event Date', 'woothemes' );
		$new_columns['event_venue'] = __( 'Venue', 'woothemes' );
		$new_columns['event_category'] = __( 'Category', 'woothemes' );
		$new_columns['event_tour'] = __( 'Tour', 'woothemes' );
		$new_columns['event_date_status'] = '';
		// $new_columns['author'] = __( 'Added By', 'woothemes' );
 		// $new_columns['date'] = _x( 'Added On', 'column name', 'woothemes' );
 
		return $new_columns;
		
	} // End add_column_headings()
	
	/**
	 * add_custom_column_data function.
	 * 
	 * @access public
	 * @param string $column_name
	 * @param int $id
	 * @return void
	 */
	function add_column_data ( $column_name, $id ) {
		global $wpdb, $post;
		
		$meta = get_post_custom( $id );
		
		switch ( $column_name ) {
		
			case 'id':
				echo $id;
			break;
			
			case 'event_date':
				$value = __( 'No Dates Specified', 'woothemes' );
				if ( isset( $meta['_event_start'] ) && ( $meta['_event_start'][0] != '' ) ) {
					$start = $meta['_event_start'][0];
					$end = $meta['_event_end'][0];
					
					$value = $this->display_formatted_date_text( $start, $end );
				}
				echo $value;
			break;
			
			case 'event_venue':
				$value = __( 'No Venue Specified', 'woothemes' );
				if ( isset( $meta['_event_venue'] ) && ( $meta['_event_venue'][0] != '' ) ) {
					$value = $meta['_event_venue'][0];
				}
				echo $value;
			break;
			
			case 'event_category':
				$value = __( 'No Categories Specified', 'woothemes' );
				$terms = get_the_terms( $id, 'event_category' );
				
				if ( $terms && ! is_wp_error( $terms ) ) {
					$term_links = array();
			
					foreach ( $terms as $term ) {
						$term_links[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $this->token, 'tag_ID' => $term->term_id, 'taxonomy' => 'event_category', 'action' => 'edit' ), 'edit-tags.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'event_category', 'display' ) )
						);
					}
									
					$value = join( ', ', $term_links );
				}
				echo $value;
			break;
			
			case 'event_tour':
				$value = __( 'No Tours Specified', 'woothemes' );
				$terms = get_the_terms( $id, 'event_tour' );
				
				if ( $terms && ! is_wp_error( $terms ) ) {
					$term_links = array();
			
					foreach ( $terms as $term ) {
						$term_links[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $this->token, 'tag_ID' => $term->term_id, 'taxonomy' => 'event_tour', 'action' => 'edit' ), 'edit-tags.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'event_tour', 'display' ) )
						);
					}
									
					$value = join( ', ', $term_links );
				}
				echo $value;
			break;
			
			case 'event_date_status':
				$value = '';
				if ( isset( $meta['_event_start'] ) && ( $meta['_event_start'][0] != '' ) ) {
					$start = $meta['_event_start'][0];
					$end = $meta['_event_end'][0];
					$value = $this->display_formatted_event_date_status( $start, $end );
				}
				echo $value;
			break;
			
			default:
			break;
		
		}
	} // End add_column_data()
	
	/**
	 * sort_on_custom_columns function.
	 * 
	 * @access public
	 * @param object $query
	 * @return object $query
	 */
	function sort_on_custom_columns ( $query ) {
		global $post_type;
		
		if ( ( $post_type == $this->token ) ) {
			$orderby = get_query_var( 'orderby' );
			$order = get_query_var( 'order' );
			
			if (
				in_array( $orderby, array( 'event_start' ) ) && 
				in_array( $order, array( 'asc', 'desc' ) )
				) {
				$query->set( 'orderby', 'meta_value' );
				$query->set( 'order', $order );
				$query->set( 'meta_key', '_' . $orderby );
				$query->parse_query();
			}
		}
		
		return $query;
	} // End sort_on_custom_columns()
	
	/**
	 * order_events_by_date function.
	 * 
	 * @access public
	 * @param object $query
	 * @return object $query
	 */
	function order_events_by_date ( $query ) {
		if ( $query->is_admin && ( ! isset( $query->query_vars['post_type'] ) || ( $this->token != $query->query_vars['post_type'] ) ) ) {
			return $query;
		}

		if ( ! isset( $query->query_vars['event_tour'] ) && ! isset( $query->query_vars['event_category'] ) && ! $query->is_post_type_archive( $this->token ) ) {
			return $query;
		}

		$query->set( 'meta_key', '_event_start' );
        $query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', apply_filters('woo_events_query_order_direction', 'ASC' ) );
		
		$status = 'all';
		
		if ( isset( $_GET['post_status'] ) && in_array( strtolower( $_GET['post_status'] ), array( 'upcoming', 'right-now', 'past' ) ) ) {
			$status = strtolower( $_GET['post_status'] );
		}
		
		if ( $status != 'all' ) {
			$query->set( 'meta_value', time() );
		}
		
		switch( $status ) {
			case 'upcoming':
				$query->set( 'meta_compare', '>' );
			break;
			case 'right-now':
				unset( $query->query_vars['meta_key'] );
				unset( $query->query_vars['meta_value'] );
				$meta_query = array(
									'relation' => 'AND', 
									array( 'key' => '_event_start', 'value' => time(), 'compare' => '<=', 'type' => 'numeric' ), 
									array( 'key' => '_event_end', 'value' => time(), 'compare' => '>', 'type' => 'numeric' )
									);

				$query->set( 'meta_query', $meta_query );
			break;
			case 'past':
				unset( $query->query_vars['meta_key'] );
				unset( $query->query_vars['meta_value'] );
				$meta_query = array(
									'relation' => 'AND', 
									array( 'key' => '_event_start', 'value' => time(), 'compare' => '<', 'type' => 'numeric' ), 
									array( 'key' => '_event_end', 'value' => time(), 'compare' => '<', 'type' => 'numeric' )
									);

				$query->set( 'meta_query', $meta_query );
			break;
		}
		
		$query->parse_query();
			
		return $query;
	} // End order_events_by_date()
	
	/**
	 * list_table_views function.
	 * 
	 * @access public
	 * @param array $views
	 * @return array $views
	 */
	function list_table_views ( $views ) {
		$types = array( 'upcoming' => __( 'Upcoming', 'woothemes' ), 'right-now' => __( 'Right Now', 'woothemes' ), 'past' => __( 'Past', 'woothemes' ) );
		foreach ( $types as $k => $v ) {
			$link = add_query_arg( 'post_status', $k, add_query_arg( 'post_type', $this->token, 'edit.php' ) );
			$class = $this->token . '-view-' . $k;
			if ( isset( $_GET['post_status'] ) && ( strtolower( $_GET['post_status'] ) == $k ) ) {
				$class .= ' current';
			}
			$views[$k] = '<a href="' . $link . '" class="' . $class . '">' . $v . '</a>';
		}
		
		unset( $views['publish'] );
		
		return $views;
	} // End list_table_views()
	
	/**
	 * change_alldates_text function.
	 * 
	 * @access public
	 * @param string $original
	 * @param string $translation
	 * @return string $original
	 */
	function change_alldates_text ( $original, $translation ) {
		if ( strtolower( $original ) == 'show all dates' ) {
			$original = sprintf( __( 'Show %s added in:', 'woothemes' ), strtolower( $this->plural ) );
		}
		
		return $original;
	} // End change_alldates_text()
	
	/**
	 * customise_featured_image_box_linktext function.
	 * 
	 * @access public
	 * @param string $original
	 * @param string $translation
	 * @return string $original
	 */
	function customise_featured_image_box_linktext ( $original, $translation ) {
		if ( in_array( strtolower( $original ), array( 'set featured image', 'remove featured image' ) ) && ( get_post_type() == $this->token ) ) {
			$original = str_replace( 'featured image', __( 'event flyer', 'woothemes' ), $original );
		}
		
		if ( ( strtolower( $original ) == 'featured image' ) && ( get_post_type() == $this->token ) ) {
			$original = __( 'Event Flyer', 'woothemes' );
		}
		
		return $original;
	} // End customise_featured_image_box_linktext()
	
	/**
	 * enter_title_here function.
	 * 
	 * @access public
	 * @param string $title
	 * @return string $title
	 */
	function enter_title_here ( $title ) {
		if ( get_post_type() == $this->token ) {
			$title = __( 'Enter event title here', 'woothemes' );
		}
		
		return $title;
	} // End enter_title_here()
	
	/**
	 * display_formatted_date_text function.
	 * 
	 * @access public
	 * @param int $start (timestamp)
	 * @param int $end (timestamp)
	 * @return string $text
	 */
	function display_formatted_date_text ( $start, $end ) {
		$text = '';
		
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		
		$text = '<strong>' . __( 'Start:', 'woothemes' ) . '</strong> ' . date_i18n( $date_format, $start ) . ' ' . __( 'at', 'woothemes' ) . ' ' . date_i18n( $time_format, $start );
		
		if ( $end > $start ) {
			$text .= '<br /><strong>' . __( 'End:', 'woothemes' ) . '</strong> ';
			$text .= date_i18n( $date_format, $end ) . ' ' . __( 'at', 'woothemes' ) . ' ' . date_i18n( $time_format, $end );
		}
		
		return $text;
	} // End display_formatted_date_text()
	
	/**
	 * display_formatted_event_date_status function.
	 *
	 * @description Is the event in the past, present or future? Is it happening right now?
	 * @access public
	 * @param int $start (timestamp)
	 * @param int $end (timestamp)
	 * @return string $text
	 */
	function display_formatted_event_date_status ( $start, $end ) {
		$text = '';
		$class = 'event-timeframe';
		
		// Upcoming Events
		if ( $start > time() ) {
			$text = __( 'Upcoming', 'woothemes' );
			$class .= ' upcoming';
		}
		
		// Past Events
		if ( $end < time() ) {
			$text = __( 'Past', 'woothemes' );
			$class .= ' past';
		}
		
		// Happening Right Now
		if ( ( time() > $start ) && ( time() < $end ) ) {
			$text = __( 'Right Now', 'woothemes' );
			$class .= ' right-now';
		}
		
		return '<span class="' . $class . '">' . $text . '</span>';
	} // End display_formatted_event_date_status()
	
	/**
	 * get_tours function.
	 * 
	 * @access public
	 * @param int $limit (default: 5)
	 * @return array $tours
	 */
	function get_tours ( $limit = 5 ) {
		$tours = array();
		
		$args = array();
		if ( $limit > 0 ) {
			$args['number'] = intval( $limit );
		}
		$terms = get_terms( 'event_tour', $args );
		
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $k => $v ) {
				$events = $this->get_events_by_tour( $v->term_id );
				$tours[$events[0]->event_start] = $v;
				$tours[$events[0]->event_start]->events = $events;
				
				$tour_dates = $this->get_tour_dates( $v->term_id );
				$tours[$events[0]->event_start]->tour_dates = $tour_dates;
			}
		
			ksort( $tours );
		}
		
		return $tours;
	} // End get_tours()
	
	/**
	 * get_events_by_tour function.
	 * 
	 * @access public
	 * @param int $id
	 * @return array $events
	 */
	function get_events_by_tour ( $id ) {
		$events = array();
		
		if ( $id != '' ) {
			$args = array(
						'post_type' => $this->token, 
						'posts_per_page' => -1, 
						'orderby' => 'meta_value_num', 
						'order' => 'ASC', 
						'meta_key' => '_event_start'
						);
						
			$args['tax_query'] = array(
						array(
							'taxonomy' => 'event_tour',
							'field' => 'id',
							'terms' => $id
						)
					);
			
			$events = get_posts( $args );
			
			if ( count( $events ) > 0 ) {
				foreach ( $events as $k => $v ) {
					$start_date = get_post_meta( $v->ID, '_event_start', true );
					$end_date = get_post_meta( $v->ID, '_event_end', true );
					
					$tickets_url = get_post_meta( $v->ID, '_tickets_url', true );
					$tickets_text = get_post_meta( $v->ID, '_tickets_text', true );
					
					$events[$k]->event_start = $start_date;
					$events[$k]->event_end = $end_date;
					$events[$k]->tickets_url = $tickets_url;
					$events[$k]->tickets_text = $tickets_text;
				}
			}
		}
		
		return $events;
	} // End get_events_by_tour()
	
	/**
	 * get_tour_dates function.
	 * 
	 * @access public
	 * @param int $id
	 * @param array $events
	 * @return array $dates
	 */
	function get_tour_dates ( $id, $events = array() ) {
		$dates = array( 'start' => '', 'end' => '' );
		
		if ( ! empty( $events ) ) {
		
		} else {
			$events = $this->get_events_by_tour( $id ); // Get events by $id
		}
		
		if ( count( $events ) > 0 ) {
			$events = array_reverse( $events );

			$format = get_option( 'date_format' );
			
			$start_date = get_post_meta( $events[0]->ID, '_event_start', true );
			$dates['start'] = date_i18n( $format, $start_date );
			
			$end_date = get_post_meta( $events[count( $events ) - 1]->ID, '_event_end', true );
			$dates['end'] = date_i18n( $format, $end_date );
		}
		return $dates;
	} // End get_tour_dates()
	
	/**
	 * add_tour_column_headings function.
	 * 
	 * @access public
	 * @param array $columns
	 * @return array $new_columns
	 */
	function add_tour_column_headings ( $columns ) {

		$new_columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => __( 'Name', 'woothemes' ),
			'tour_dates' => __( 'Tour Dates', 'woothemes' ),
			// 'description' => __('Description'),
			// 'slug' => __('Slug'),
			'posts' => __( 'Events', 'woothemes' )
		);
		
		return $new_columns;
	} // End add_tour_column_headings()
	
	/**
	 * add_tour_column_data function.
	 * 
	 * @access public
	 * @param mixed $out
	 * @param mixed $column_name
	 * @param mixed $id
	 * @return void
	 */
	function add_tour_column_data ( $out, $column_name, $id ) {
		
		switch ($column_name) {
			case 'tour_dates':
				$dates = $this->get_tour_dates( $id );
				$out .= $dates['start'] . ' - ' . $dates['end'];
			break;
			
			default:
			break;
		}
		
	return $out;
	} // End add_tour_column_data()
	
	/**
	 * get_next_weekend_dates function.
	 * 
	 * @access public
	 * @param int $now (timestamp)
	 * @return array $weekend_dates
	 */
	function get_next_weekend_dates ( $now ) {
		$end_date = strtotime( '+1 week' );
		$weekend_dates = array();
		$stored_indecies = array();
		
		while ( date( 'Y-m-d', $now ) != date( 'Y-m-d', $end_date ) ) {
		    $day_index = date( 'w', $now );
		    if ( in_array( $day_index, array( 0, 5, 6 ) ) && ! in_array( $day_index, $stored_indecies ) ) { // Sunday, Friday or Saturday
		        $weekend_dates[] = $now;
		    }
		    $now = strtotime( date( 'Y-m-d', $now ) . '+1 day' );
		}
		
		return $weekend_dates;
	} // End get_next_weekend_dates()
	
	/**
	 * get_dates_of_week function.
	 *
	 * @description Get the timestamps for each day of the current day's week.
	 * @access public
	 * @param int $now (timestamp)
	 * @return array $dates
	 */
	function get_dates_of_week ( $now ) {
		$dates = array();
		
		$i = date( 'w', $now );

		// Reset the date to the closest Sunday past.
		if ( $i > 0 ) {
			$type = 'days';
			if ( $i == 1 ) { $type = 'day'; }
			$now = strtotime( date( 'Y-m-d', $now ) . '-' . $i . ' ' . $type );
			$i = 0;
		}
		
		while ( $i <= 6 ) {
			$dates[] = $now;

			$now = strtotime( date( 'Y-m-d', $now ) . '+1 day' );

			$i++;
		}

		return $dates;
	} // End get_dates_of_week()
	
	/**
	 * get_event_details function.
	 * 
	 * @access public
	 * @param int $id
	 * @return array $details
	 */
	function get_event_details ( $id ) {
		$meta = get_post_custom( $id );
		$details = array();
		
		// Venue
		if ( isset( $meta['_event_venue'] ) && ( $meta['_event_venue'][0] != '' ) ) {
			$details['venue'] = array( 'label' => __( 'Venue', 'woothemes' ), 'value' => $meta['_event_venue'][0] );
		}

		// Tickets Link
		$tickets_defaulttext = '';
		$tickets_anchor = $tickets_defaulttext;
		
		if ( isset( $meta['_tickets_text'] ) && ( $meta['_tickets_text'][0] != '' ) ) {
			$tickets_anchor = $meta['_tickets_text'][0];
		}
		
		if ( isset( $meta['_tickets_url'] ) && ( $meta['_tickets_url'][0] != '' ) ) {
			if ( $tickets_anchor == $tickets_defaulttext ) {
				$tickets_anchor = __( 'Buy Tickets', 'woothemes' );
			}
			$tickets_anchor = '<a href="' . esc_url( $meta['_tickets_url'][0] ) . '">' . $tickets_anchor . '</a>';
		}
		
		if ( $tickets_anchor != $tickets_defaulttext ) {
			$details['tickets'] = array( 'label' => __( 'Tickets', 'woothemes' ), 'value' => $tickets_anchor );
		}
		
		// Start
		$details['start'] = array( 'label' => __( 'Start', 'woothemes' ), 'value' => date_i18n( get_option( 'date_format' ), $meta['_event_start'][0] ) . ' @ ' . date_i18n( get_option( 'time_format' ), $meta['_event_start'][0] ) );
		
		// End
		$details['end'] = array( 'label' => __( 'End', 'woothemes' ), 'value' => date_i18n( get_option( 'date_format' ), $meta['_event_end'][0] ) . ' @ ' . date_i18n( get_option( 'time_format' ), $meta['_event_end'][0] ) );
		
		if ( isset( $meta['_ticket_price'] ) && ( $meta['_ticket_price'][0] != '' ) ) {
			$details['cover-charge'] = array( 'label' => __( 'Cover Charge', 'woothemes' ), 'value' => $meta['_ticket_price'][0] );
		}
		
		return $details;
	} // End get_event_details()
} // End Class
?>