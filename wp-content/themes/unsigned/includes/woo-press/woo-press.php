<?php
/**
 * WooThemes Press Clippings Manager.
 *
 * Controls the management of press clippings.
 *
 * @category Modules
 * @package WordPress
 * @subpackage WooFramework
 * @author Matty at WooThemes
 * @date 2012-01-10.
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - var $token
 * - var $singular
 * - var $plural
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
 * - function customise_featured_image_box_linktext()
 * - function enter_title_here()
 * - function get_press()
 * - function get_categories()
 */

class WooThemes_Press {

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
	
	var $taxonomies;
	
	var $template_url;

	/**
	 * WooThemes_Press function.
	 *
	 * @description Constructor function. Sets up the class and registers variable action hooks.
	 * @access public
	 * @return void
	 */
	function WooThemes_Press () {
		$this->token = 'press';
		$this->singular = __( 'Press Clipping', 'woothemes' );
		$this->plural = __( 'Press', 'woothemes' );
		$this->rewrite_tag = '%woo_press_category%';
		$this->dir = 'woo-press';
		
		$this->taxonomies = array(
									'press_category' => array(
															'singular' => __( 'Category', 'woothemes' ), 
															'plural' => __( 'Categories', 'woothemes' ), 
															'rewrite' => 'press-categories'
														)
								);
		
		$this->template_url = get_template_directory_uri();
	} // End WooThemes_Press()
	
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

		if ( is_admin() ) {
			global $pagenow;

			if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == $this->token ) || $pagenow == 'post.php' ) {
				add_action( 'admin_print_styles-edit.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-edit-tags.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-post.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-post-new.php', array( &$this, 'enqueue_styles' ), 10 );
			}

			if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ) {
				add_filter( 'woothemes_metabox_settings', array( &$this, 'customise_wooframework_meta_box' ), 10, 3 );
				add_filter( 'gettext', array( &$this, 'customise_featured_image_box_linktext' ), 10, 2 );
			}

			add_filter( 'manage_edit-' . $this->token . '_columns', array( &$this, 'add_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( &$this, 'add_column_data' ), 10, 2 );
			
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 10 );
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
	    // 'rewrite' => apply_filters( 'woo_press_rewrite_base', 'press' ), 
	    'rewrite' => array( 'slug' => apply_filters( 'woo_press_rewrite_base', 'press' ) . '/' . $this->rewrite_tag . '' ),
	    'capability_type' => 'post',
	    'has_archive' => apply_filters( 'woo_press_archive_rewrite_base', 'press-clippings' ), 
	    'hierarchical' => false,
	    'menu_position' => 20, 
	    'menu_icon' => $this->template_url . '/includes/' . $this->dir . '/assets/images/icon_16.png', 
	    'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' )
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
		$terms = get_the_terms( $post->ID, 'press_category' );
		
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
	function customise_wooframework_meta_box( $settings, $type, $handle ) {
		if ( $type == $this->token ) {
			$settings['title'] = __( 'Press Clipping Details', 'woothemes' );
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
		$columns['publication_date'] = 'publication_date';
		$columns['media_portal'] = 'media_portal';
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
		$new_columns['title'] = _x( 'Title', 'column name', 'woothemes' );
		$new_columns['publication_date'] = __( 'Publication Date', 'woothemes' );
		$new_columns['media_portal'] = __( 'Media', 'woothemes' );
		$new_columns['press_category'] = __( 'Categories', 'woothemes' );
		$new_columns['author'] = __( 'Added By', 'woothemes' );
 		$new_columns['date'] = _x( 'Added On', 'column name', 'woothemes' );
 
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
			
			case 'publication_date':
				$value = __( 'No Publication Date Specified', 'woothemes' );
				if ( isset( $meta['_publication_date'] ) && ( $meta['_publication_date'][0] != '' ) ) {
					$value = date( get_option( 'date_format' ), $meta['_publication_date'][0] );
				}
				echo $value;
			break;
			
			case 'media_portal':
				$value = __( 'No Media Specified', 'woothemes' );
				if ( isset( $meta['_media_portal'] ) && ( $meta['_media_portal'][0] != '' ) ) {
					$value = esc_attr( $meta['_media_portal'][0] );
				}
				echo $value;
			break;
			
			case 'press_category':
				$value = __( 'No Categories Specified', 'woothemes' );
				$terms = get_the_terms( $id, 'press_category' );
				
				if ( $terms && ! is_wp_error( $terms ) ) {
					$term_links = array();
			
					foreach ( $terms as $term ) {
						$term_links[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $this->token, 'tag_ID' => $term->term_id, 'taxonomy' => 'press_category', 'action' => 'edit' ), 'edit-tags.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'event_category', 'display' ) )
						);
					}
									
					$value = join( ', ', $term_links );
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
				in_array( $orderby, array( 'publication_date' ) ) && 
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
	 * customise_featured_image_box_linktext function.
	 * 
	 * @access public
	 * @param string $original
	 * @param string $translation
	 * @return string $original
	 */
	function customise_featured_image_box_linktext ( $original, $translation ) {
		if ( in_array( strtolower( $original ), array( 'set featured image', 'remove featured image' ) ) && ( get_post_type() == $this->token ) ) {
			$original = str_replace( 'featured image', __( 'press clipping image', 'woothemes' ), $original );
		}
		
		if ( ( strtolower( $original ) == 'featured image' ) && ( get_post_type() == $this->token ) ) {
			$original = __( 'Press Clipping Image', 'woothemes' );
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
			$title = __( 'Enter press clipping title here', 'woothemes' );
		}
		
		return $title;
	} // End enter_title_here()
	
	/**
	 * get_press function.
	 * 
	 * @access public
	 * @param array $args (default: '')
	 * @return array $press
	 */
	function get_press ( $args = '' ) {
		$defaults = array(
			'id' => 0, 
			'limit' => 5, 
			'orderby' => 'post_date', 
			'order' => 'DESC'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'get_press_args', $args );
		
		$press = array();
		
		$query_args = array(
							'numberposts' => $args['limit'], 
							'post_type' => 'press',  
							'orderby' => $args['orderby'], 
							'order' => $args['order']
							);

		if ( $args['id'] > 0 ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'press_category',
					'field' => 'id',
					'terms' => $args['id']
				)
			);
		}
		
		$entries = get_posts( $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$press[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$press = apply_filters( 'get_press', $press, $args );
		
		return $press;
	} // End get_videos()
	
	/**
	 * get_categories function.
	 * 
	 * @access public
	 * @param array $args (default: '')
	 * @return array $categories
	 */
	function get_categories ( $args = '' ) {
		$defaults = array(
			'id' => 0, 
			'limit' => 0, 
			'category' => 0, 
			'orderby' => 'id', 
			'order' => 'ASC'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'get_press_categories_args', $args );
		
		$categories = array();
		
		$query_args = array(
							'orderby' => $args['orderby'], 
							'order' => $args['order']
							);
		
		if ( 0 < $args['limit'] ) {
			$query_args['number'] = intval( $args['limit'] );
		}

		$entries = get_terms( 'press_category', $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$categories[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$categories = apply_filters( 'get_press_categories', $categories, $args );
		
		return $categories;
	} // End get_categories()
} // End Class
?>