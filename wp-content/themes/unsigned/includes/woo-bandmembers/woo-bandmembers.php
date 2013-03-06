<?php
/**
 * WooThemes Band Members Manager.
 *
 * Controls the management of band member information.
 *
 * @category Modules
 * @package WordPress
 * @subpackage WooFramework
 * @author Matty at WooThemes
 * @date 2012-01-17.
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - var $token
 * - var $singular
 * - var $plural
 * - var $dir
 *
 * - var $band_members
 *
 * - var $template_url

 * - Constructor Function
 * - function init()
 * - function register_post_type()
 * - function customise_wooframework_meta_box()
 * - function enqueue_styles()
 * - function add_column_headings()
 * - function add_column_data()
 * - function order_members_by_menu_order()
 * - function customise_featured_image_box_linktext()
 * - function enter_title_here()
 * - function get_band_members()
 */

class WooThemes_BandMembers {

	/**
	 * Variables
	 *
	 * @description Setup of variable placeholders, to be populated when the constructor runs.
	 * @since 1.0.0
	 */

	var $token;
	var $singular;
	var $plural;
	
	var $band_members;
	
	var $template_url;

	/**
	 * WooThemes_Press function.
	 *
	 * @description Constructor function. Sets up the class and registers variable action hooks.
	 * @access public
	 * @return void
	 */
	function WooThemes_BandMembers () {
		$this->token = 'band_member';
		$this->singular = __( 'Band Member', 'woothemes' );
		$this->plural = __( 'Band Members', 'woothemes' );
		$this->dir = 'woo-bandmembers';
		
		$this->band_members = array();
		
		$this->template_url = get_template_directory_uri();
	} // End WooThemes_BandMembers()
	
	/**
	 * init function.
	 *
	 * @description This guy runs the show. Rocket boosters... engage!
	 * @access public
	 * @return void
	 */
	function init() {
		add_action( 'init', array( &$this, 'register_post_type' ), 1 );

		add_filter( 'pre_get_posts', array( &$this, 'order_members_by_menu_order' ) );

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
	    'all_items' => sprintf( __( 'All Band Members', 'woothemes' ), $this->plural ),
	    'view_item' => sprintf( __( 'View %s', 'woothemes' ), $this->singular ),
	    'search_items' => sprintf( __( 'Search %s', 'woothemes' ), $this->plural ),
	    'not_found' =>  sprintf( __( 'No %s Found', 'woothemes' ), $this->plural ),
	    'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'woothemes' ), $this->plural ), 
	    'parent_item_colon' => '',
	    'menu_name' => __( 'Band', 'woothemes' )
	
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
	    'rewrite' => array( 'slug' => apply_filters( 'woo_bandmembers_rewrite_base', 'member' ), 'with_front' => false ), 
	    'capability_type' => 'post',
	    'has_archive' => apply_filters( 'woo_bandmembers_archive_rewrite_base', 'members' ), 
	    'hierarchical' => false,
	    'menu_position' => 5, 
	    'menu_icon' => $this->template_url . '/includes/' . $this->dir . '/assets/images/icon_16.png', 
	    'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' )
	  );
	
		register_post_type( $this->token, $args );
	} // End register_post_type()
	
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
			$settings['title'] = __( 'Band Member Information', 'woothemes' );
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
		$new_columns['member_image'] = '';
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
			
			case 'member_image':
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( array( 70 ) );
				}
			break;
			
			default:
			break;
		
		}
	} // End add_column_data()
	
	/**
	 * order_members_by_menu_order function.
	 * 
	 * @access public
	 * @param object $query
	 * @return object $query
	 */
	function order_members_by_menu_order ( $query ) {
		if ( isset( $query->query_vars['post_type'] ) && ( $query->query_vars['post_type'] != $this->token ) ) {
			return $query;
		}
		
		if ( ( ! $query->is_admin ) && ( ! $query->is_post_type_archive( $this->token ) ) ) {
			return $query;
		} 
 		
        $query->set( 'orderby', 'menu_order' );
		$query->set( 'order', apply_filters('woo_bandmembers_query_order_direction', 'ASC' ) );
		
		$query->parse_query();
			
		return $query;
	} // End order_members_by_menu_order()
	
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
			$original = str_replace( 'featured image', __( 'member photograph', 'woothemes' ), $original );
		}
		
		if ( ( strtolower( $original ) == 'featured image' ) && ( get_post_type() == $this->token ) ) {
			$original = __( 'Member Photograph', 'woothemes' );
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
			$title = __( 'Enter this band member\'s name here', 'woothemes' );
		}
		
		return $title;
	} // End enter_title_here()
	
	/**
	 * get_band_members function.
	 * 
	 * @access public
	 * @param array $args (default: '')
	 * @return array $band_members
	 */
	function get_band_members ( $args = '' ) {
		if ( count( $this->band_members ) > 0 ) {
			$band_members = $this->band_members;
		} else {
			$defaults = array(
				'id' => 0, 
				'limit' => 5, 
				'orderby' => 'menu_order', 
				'order' => 'ASC'
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			// Allow child themes/plugins to filter here.
			$args = apply_filters( 'get_band_members_args', $args );
			
			$galleries = array();
			
			$query_args = array(
								'numberposts' => $args['limit'], 
								'post_type' => $this->token,  
								'orderby' => $args['orderby'], 
								'order' => $args['order']
								);
			
			$entries = get_posts( $query_args );
			
			if ( count( $entries ) > 0 ) {
				foreach ( $entries as $k => $v ) {
					$band_members[$k] = $v;
					
					$thumbnail = woo_image( 'link=url&return=true&id=' . $v->ID );
					if ( $thumbnail != '' ) {
						$band_members[$k]->thumbnail = $thumbnail;
					}
				}
			}
		}
		
		// Allow child themes/plugins to filter here.
		$band_members = apply_filters( 'get_band_members', $band_members, $args );
		
		return $band_members;
	} // End get_band_members()
} // End Class
?>