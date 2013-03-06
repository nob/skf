<?php
/**
 * WooThemes Video Manager.
 *
 * Controls the management of video clips.
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
 * - var $dir
 *
 * - var $rewrite_tag
 *
 * - var $template_url

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
 * - function get_videos()
 * - function get_categories()
 */

class WooThemes_Videos {

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
	 * WooThemes_Videos function.
	 *
	 * @description Constructor function. Sets up the class and registers variable action hooks.
	 * @access public
	 * @return void
	 */
	function WooThemes_Videos () {
		$this->token = 'video';
		$this->singular = __( 'Video', 'woothemes' );
		$this->plural = __( 'Videos', 'woothemes' );
		$this->dir = 'woo-videos';
		
		$this->rewrite_tag = '%woo_videos_category%';
		
		$this->taxonomies = array(
									'video_category' => array(
															'singular' => __( 'Category', 'woothemes' ), 
															'plural' => __( 'Categories', 'woothemes' ), 
															'rewrite' => 'video-categories'
														)
								);
		
		$this->template_url = get_template_directory_uri();
	} // End WooThemes_Videos()
	
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
			
			// add_filter( 'manage_edit-' . $this->token . '_sortable_columns', array( &$this, 'add_sortable_columns' ), 10, 1 );
			add_filter( 'manage_edit-' . $this->token . '_columns', array( &$this, 'add_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( &$this, 'add_column_data' ), 10, 2 );
			// add_filter( 'pre_get_posts', array( &$this, 'sort_on_custom_columns' ), 10, 1 );
			
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
	    // 'rewrite' => apply_filters( 'woo_videos_rewrite_base', 'videos' ), 
	    'rewrite' => array( 'slug' => apply_filters( 'woo_videos_rewrite_base', 'videos' ) . '/' . $this->rewrite_tag . '' ),
	    'capability_type' => 'post',
	    'has_archive' => apply_filters( 'woo_videos_archive_rewrite_base', 'videos' ), 
	    'hierarchical' => false,
	    'menu_position' => 5, 
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
		$terms = get_the_terms( $post->ID, 'video_category' );
		
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
			$settings['title'] = __( 'Video Details', 'woothemes' );
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
		$columns['release_date'] = 'release_date';
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
		$new_columns['video_category'] = __( 'Categories', 'woothemes' );
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
			
			case 'video_category':
				$value = __( 'No Categories Specified', 'woothemes' );
				$terms = get_the_terms( $id, 'video_category' );
				
				if ( $terms && ! is_wp_error( $terms ) ) {
					$term_links = array();
			
					foreach ( $terms as $term ) {
						$term_links[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $this->token, 'tag_ID' => $term->term_id, 'taxonomy' => 'video_category', 'action' => 'edit' ), 'edit-tags.php' ) ),
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
	 * customise_featured_image_box_linktext function.
	 * 
	 * @access public
	 * @param string $original
	 * @param string $translation
	 * @return string $original
	 */
	function customise_featured_image_box_linktext ( $original, $translation ) {
		if ( in_array( strtolower( $original ), array( 'set featured image', 'remove featured image' ) ) && ( get_post_type() == $this->token ) ) {
			$original = str_replace( 'featured image', __( 'video posterframe', 'woothemes' ), $original );
		}
		
		if ( ( strtolower( $original ) == 'featured image' ) && ( get_post_type() == $this->token ) ) {
			$original = __( 'Video Posterframe', 'woothemes' );
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
			$title = __( 'Enter video title here', 'woothemes' );
		}
		
		return $title;
	} // End enter_title_here()
		
	/**
	 * get_videos function.
	 * 
	 * @access public
	 * @param array $args (default: '')
	 * @return array $videos
	 */
	function get_videos ( $args = '' ) {
		$defaults = array(
			'id' => 0, 
			'limit' => 5, 
			'orderby' => 'post_date', 
			'order' => 'DESC'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'get_videos_args', $args );
		
		$videos = array();
		
		$query_args = array(
							'numberposts' => $args['limit'], 
							'post_type' => 'video',  
							'orderby' => $args['orderby'], 
							'order' => $args['order']
							);
		
		// Make sure the video has the embed code field and that it's not empty.
		$query_args['meta_query'] = array(
				array(
					'key' => 'embed',
					'value' => '',
					'compare' => '!='
				)
			);
		
		if ( $args['id'] > 0 ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'video_category',
					'field' => 'id',
					'terms' => $args['id']
				)
			);
		}
		
		$entries = get_posts( $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$videos[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$videos = apply_filters( 'get_videos', $videos, $args );
		
		return $videos;
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
			'limit' => 5, 
			'category' => 0, 
			'orderby' => 'id', 
			'order' => 'ASC'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'get_video_categories_args', $args );
		
		$categories = array();
		
		$query_args = array(
							'number' => $args['limit'],  
							'orderby' => $args['orderby'], 
							'order' => $args['order']
							);
		
		$entries = get_terms( 'video_category', $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$categories[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$categories = apply_filters( 'get_video_categories', $categories, $args );
		
		return $categories;
	} // End get_categories()
} // End Class
?>