<?php
/**
 * WooThemes Discography Manager.
 *
 * Controls the management of album releases.
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
 * - var $meta_box_settings
 *
 * - var $taxonomies
 * - var $loaded_playlists
 *
 * - var $template_url

 * - Constructor Function
 * - function init()
 * - function register_post_type()
 * - function register_taxonomies()
 * - function custom_post_permastruct()
 * - function customise_wooframework_meta_box()
 * - function enqueue_styles()
 * - function enqueue_frontend_styles()
 * - function enqueue_admin_scripts()
 * - function enqueue_scripts()
 * - function add_sortable_columns()
 * - function add_column_headings()
 * - function add_column_data()
 * - function sort_on_custom_columns()
 * - function customise_featured_image_box_linktext()
 * - function enter_title_here()
 * - function get_tracks()
 * - function get_albums()
 * - function get_categories()
 * - function trigger_playlist_javascript_generator()
 * - generate_playlist_javascript()
 * - function load_playlist()
 * - function setup_loaded_players()
 * - function setup_player()
 */

class WooThemes_Discography {

	/**
	 * Variables
	 *
	 * @description Setup of variable placeholders, to be populated when the constructor runs.
	 * @since 1.0.0
	 */

	var $token;
	var $singular;
	var $plural;
	var $dir;
	
	var $rewrite_tag;
	
	var $meta_box_settings;
	
	var $taxonomies;
	var $loaded_playlists;
	
	var $template_url;

	/**
	 * WooThemes_Discography function.
	 *
	 * @description Constructor function. Sets up the class and registers variable action hooks.
	 * @access public
	 * @return void
	 */
	function WooThemes_Discography () {
		// Load in the MP3 file-related functions.
		require_once( 'classes/mp3file.class.php' );
		
		$this->token = 'album';
		$this->singular = __( 'Album', 'woothemes' );
		$this->plural = __( 'Albums', 'woothemes' );
		$this->dir = 'woo-discography';
		
		$this->rewrite_tag = '%woo_discography_category%';
		
		$this->meta_box_settings['title'] = __( 'Tracks on this Album', 'woothemes' );
		
		$this->taxonomies = array(
									'album_category' => array(
															'singular' => __( 'Category', 'woothemes' ), 
															'plural' => __( 'Categories', 'woothemes' ), 
															'rewrite' => 'album-categories'
														)
								);
		
		$this->loaded_playlists = array();
		
		$this->template_url = get_template_directory_uri();
	} // End WooThemes_Discography()
	
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

		add_action( 'template_redirect', array( &$this, 'trigger_playlist_javascript_generator' ), 10 );
		
		add_action( 'wp_footer', array( &$this, 'enqueue_scripts' ), 10 );
		add_action( 'wp_footer', array( &$this, 'setup_loaded_players' ), 20 );
		
		add_rewrite_tag( $this->rewrite_tag,'([^&]+)' );
		add_filter( 'post_type_link', array( &$this, 'custom_post_permastruct' ), 1, 4 );
		
		if ( is_admin() ) {
			global $pagenow;

			add_action( 'wp_ajax_woo_tracks_refresh', array( &$this, 'meta_box_refresh_content' ) );
			add_action( 'wp_ajax_nopriv_woo_tracks_refresh', array( &$this, 'meta_box_refresh_content' ) );

			if ( ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == $this->token ) ) || $pagenow == 'post.php' ) {
				add_action( 'admin_print_styles-edit.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-edit-tags.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-post.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-post-new.php', array( &$this, 'enqueue_styles' ), 10 );
				
				add_action( 'admin_print_scripts-edit.php', array( &$this, 'enqueue_admin_scripts' ), 10 );
				add_action( 'admin_print_scripts-edit-tags.php', array( &$this, 'enqueue_admin_scripts' ), 10 );
				add_action( 'admin_print_scripts-post.php', array( &$this, 'enqueue_admin_scripts' ), 10 );
				add_action( 'admin_print_scripts-post-new.php', array( &$this, 'enqueue_admin_scripts' ), 10 );
			}

			if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ) {
				add_filter( 'woothemes_metabox_settings', array( &$this, 'customise_wooframework_meta_box' ), 10, 3 );
				add_filter( 'gettext', array( &$this, 'customise_featured_image_box_linktext' ), 10, 2 );
				add_action( 'admin_menu', array( &$this, 'meta_box_setup' ), 20 );
				add_action( 'save_post', array( &$this, 'meta_box_save' ) );
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
	    // 'rewrite' => apply_filters( 'woo_discography_rewrite_base', 'albums' ), 
	    'rewrite' => array( 'slug' => apply_filters( 'woo_discography_rewrite_base', 'albums' ) . '/' . $this->rewrite_tag . '' ),
	    'capability_type' => 'post',
	    'has_archive' => apply_filters( 'woo_discography_archive_rewrite_base', 'albums' ), 
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
		$terms = get_the_terms( $post->ID, 'album_category' );
		
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
			$settings['title'] = __( 'Release Details', 'woothemes' );
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
	 * enqueue_frontend_styles function.
	 * 
	 * @access public
	 * @return void
	 */
	function enqueue_frontend_styles () {
		wp_register_style( 'woo-' . $this->token . '-player', $this->template_url . '/includes/' . $this->dir . '/assets/css/player.css', '', '1.0.0' );
		
		wp_enqueue_style( 'woo-' . $this->token . '-player' );
	} // End enqueue_frontend_styles()
	
	/**
	 * enqueue_admin_scripts function.
	 * 
	 * @access public
	 * @return void
	 */
	function enqueue_admin_scripts () {
		global $pagenow, $post;

		wp_register_script( 'woo-' . $this->token . '-admininterface', $this->template_url . '/includes/' . $this->dir . '/assets/js/functions.js', array( 'autosave' ), '1.0.0' );

		if ( $pagenow == 'edit-tags.php' || ( get_query_var( 'post_type' ) == $this->token ) || ( get_post_type() == $this->token ) ) {
			wp_enqueue_script( 'woo-' . $this->token . '-admininterface' );
			
			$translation_strings = array( 'loading' => __( 'Loading', 'woothemes' ) );
			
			$ajax_vars = array( 'meta_box_content_nonce' => wp_create_nonce( 'meta_box_content_nonce' ), 'post_id' => $post->ID );

			$data = array_merge( $translation_strings, $ajax_vars );
	
			/* Specify variables to be made available to the general.js file. */
			wp_localize_script( 'woo-' . $this->token . '-admininterface', 'woo_localized_data', $data );
		}
	} // End enqueue_admin_scripts()
	
	/**
	 * enqueue_scripts function.
	 * 
	 * @access public
	 * @return void
	 */
	function enqueue_scripts () {
		wp_register_script( 'woo-' . $this->token . '-jplayer', $this->template_url . '/includes/' . $this->dir . '/assets/js/jquery-jplayer/jquery.jplayer.js', array( 'jquery' ), '2.0.22', true );
		wp_register_script( 'woo-' . $this->token . '-ttw-music-player', $this->template_url . '/includes/' . $this->dir . '/assets/js/ttw-music-player.js', array( 'woo-' . $this->token . '-jplayer' ), '1.0.1', true );

		wp_enqueue_script( 'woo-' . $this->token . '-ttw-music-player' );
	} // End enqueue_scripts()
	
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
		$new_columns['release_date'] = __( 'Release Date', 'woothemes' );
		$new_columns['album_category'] = __( 'Categories', 'woothemes' );
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
			
			case 'release_date':
				$value = __( 'No Release Date Specified', 'woothemes' );
				if ( isset( $meta['_release_date'] ) && ( $meta['_release_date'][0] != '' ) ) {
					$value = date_i18n( get_option( 'date_format' ), $meta['_release_date'][0] );
				}
				echo $value;
			break;
			
			case 'album_category':
				$value = __( 'No Categories Specified', 'woothemes' );
				$terms = get_the_terms( $id, 'album_category' );
				
				if ( $terms && ! is_wp_error( $terms ) ) {
					$term_links = array();
			
					foreach ( $terms as $term ) {
						$term_links[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $this->token, 'tag_ID' => $term->term_id, 'taxonomy' => 'album_category', 'action' => 'edit' ), 'edit-tags.php' ) ),
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
				in_array( $orderby, array( 'release_date' ) ) && 
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
			$original = str_replace( 'featured image', __( 'album cover', 'woothemes' ), $original );
		}
		
		if ( ( strtolower( $original ) == 'featured image' ) && ( get_post_type() == $this->token ) ) {
			$original = __( 'Album Cover', 'woothemes' );
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
			$title = __( 'Enter album title here', 'woothemes' );
		}
		
		return $title;
	} // End enter_title_here()
	
	/**
	 * get_tracks function.
	 * 
	 * @access public
	 * @param array $args (default: '')
	 * @return array $tracks
	 */
	function get_tracks ( $args = '' ) {
		$defaults = array(
			'id' => 0, 
			'limit' => 5, 
			'orderby' => 'menu_order', 
			'order' => 'ASC'
		);

		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'get_tracks_args', $args );
		
		$tracks = array();
		
		$query_args = array(
							'numberposts' => $args['limit'], 
							'post_type' => 'attachment',  
							'orderby' => $args['orderby'], 
							'order' => $args['order'], 
							'post_mime_type' => 'audio'
							);
		
		if ( $args['id'] > 0 ) {
			$query_args['post_parent'] = $args['id'];
		}
		
		// Check for specific tracks to be selected.
		$selected_tracks = get_post_meta( $args['id'], '_selected_audio', true );
		
		if ( is_array( $selected_tracks ) && ( count( $selected_tracks ) > 0 ) ) {
			$query_args['post__in'] = $selected_tracks;
		}
		
		$entries = get_posts( $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$tracks[$k] = $v;
				
				// Check for a track length. If we don't have one stored, determine it.
				$track_length = get_post_meta( $v->ID, '_track_length_ms', true );
				if ( $track_length == '' ) {
					$mp3file = new mp3file( get_attached_file( $v->ID ) );
					$data = $mp3file->get_metadata();
					
					// Store the data so we don't need to look for it again.
					if ( isset( $data['Length'] ) && ( $data['Length'] != 'unknown' ) ) {
						add_post_meta( $v->ID, '_track_length', $data['Length'], true );
					}
					
					if ( isset( $data['Length mm:ss'] ) && ( $data['Length mm:ss'] != 'unknown' ) ) {
						add_post_meta( $v->ID, '_track_length_ms', $data['Length mm:ss'], true );
					}
					
					$tracks[$k]->track_length = $data['Length'];
					$tracks[$k]->track_length_ms = $data['Length mm:ss'];
				}
			}
		}
		
		// Allow child themes/plugins to filter here.
		$tracks = apply_filters( 'get_tracks', $tracks, $args );
		
		return $tracks;
	} // End get_tracks()
	
	/**
	 * get_albums function.
	 * 
	 * @access public
	 * @param array $args (default: '')
	 * @return array $albums
	 */
	function get_albums ( $args = '' ) {
		$defaults = array(
			'id' => 0, 
			'limit' => 5, 
			'orderby' => 'menu_order', 
			'order' => 'ASC'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'get_albums_args', $args );
		
		$galleries = array();
		
		$query_args = array(
							'numberposts' => $args['limit'], 
							'post_type' => 'album',  
							'orderby' => $args['orderby'], 
							'order' => $args['order']
							);

		if ( $args['id'] > 0 ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'album_category',
					'field' => 'id',
					'terms' => $args['id']
				)
			);
		}
		
		$entries = get_posts( $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$albums[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$albums = apply_filters( 'get_albums', $albums, $args );
		
		return $albums;
	} // End get_albums()
	
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
		$args = apply_filters( 'get_album_categories_args', $args );
		
		$galleries = array();
		
		$query_args = array(
							'number' => $args['limit'],  
							'orderby' => $args['orderby'], 
							'order' => $args['order']
							);
		
		$entries = get_terms( 'album_category', $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$categories[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$categories = apply_filters( 'get_album_categories', $categories, $args );
		
		return $categories;
	} // End get_categories()
	
	/**
	 * trigger_playlist_javascript_generator function.
	 * 
	 * @access public
	 * @return void
	 */
	function trigger_playlist_javascript_generator () {
		if ( /*isset( $_GET['woo_load_playlist'] ) && ( $_GET['woo_load_playlist'] == 'yes' ) && */isset( $_GET['woo_playlist_id'] ) && ( $_GET['woo_playlist_id'] != '' ) ) {
			header( 'Content-Type:text/javascript' );
			
			$id = intval( $_GET['woo_playlist_id'] );
			
			$html = $this->generate_playlist_javascript( $id );
			
			echo $html;
			die();
		}
	} // End trigger_playlist_javascript_generator()
	
	/**
	 * generate_playlist_javascript function.
	 * 
	 * @access public
	 * @param int $id
	 * @return string $html
	 */
	function generate_playlist_javascript ( $id ) {
		$html = '';
		
		$tracks = $this->get_tracks( array( 'limit' => -1, 'id' => $id ) );
		
		$cover_image = woo_image( 'return=true&link=url&id=' . $id . '&width=80&height=80' );
		
$html .= 'var playlistNumber' . $id . ' = [' . "\n";
foreach ( $tracks as $k => $v ) {
$html .= '{
    mp3:\'' . $v->guid . '\',
    title:\'' . esc_attr( $v->post_title ) . '\',
    artist:\'' . get_bloginfo( 'name' ) . '\',';
/*
    rating:4,
    buy:\'\',
    price:\'0.99\',
*/
$html .= '
    duration:\'' . $v->track_length_ms . '\',
    cover:\'' . $cover_image . '\'
}';

if ( $k < ( count( $tracks ) - 1 ) ) {
	$html .= ', ';
}
}
$html .= '];';
		
		return $html;
	} // End generate_playlist_javascript()
 
	 /**
	  * load_playlist function.
	  * 
	  * @access public
	  * @param int $id
	  * @return void
	  */
	 function load_playlist ( $id ) {
	 	if ( ! in_array( $id, $this->loaded_playlists ) ) {
			$this->loaded_playlists[] = $id;
		}
	 } // End load_playlist()
	 
	 /**
	  * setup_loaded_players function.
	  * 
	  * @access public
	  * @return void
	  */
	 function setup_loaded_players () {
	 	if ( count( $this->loaded_playlists ) > 0 ) {
	 		$html = '';
	 			
	 		foreach ( $this->loaded_playlists as $k => $v ) {
		 		$playlist_js_url = add_query_arg( 'woo_playlist_id', $v, home_url( '/' ) );
					
				$html .= '<script type="text/javascript" src="' . $playlist_js_url . '"></script>' . "\n";
			}
	 		$html .= '<script type="text/javascript">' . "\n";
	 			$html .= 'jQuery(document).ready(function(){' . "\n";
			foreach ( $this->loaded_playlists as $k => $v ) {
				$html .= $this->setup_player( $v );
			}
				$html .= '});' . "\n";
			$html .= '</script>' . "\n";
			
			echo $html;
		}
	 } // End setup_loaded_players()
	 
	 /**
	  * setup_player function.
	  * 
	  * @access public
	  * @param int $id
	  * @return string $html
	  */
	 function setup_player ( $id ) {
	 	global $is_gecko;
	 
	 	$data = get_post( $id );
	 	$post = $data;
	 	setup_postdata( $post );
	 	
	 	
	 	if ( isset( $data->post_excerpt ) && ( $data->post_excerpt != '' ) ) {
	 		$description = $data->post_excerpt;
	 	} else {
	 		$description = wp_trim_excerpt();
	 	}

	 	$description = str_replace( "'", "\'", strip_tags( $description ) );
	 	$album_title = str_replace( "'", "\'", strip_tags( $data->post_title ) );
	 	
	 	$html = 'jQuery( \'.woo-audio-player#player-' . $id . '\' ).ttwMusicPlayer( playlistNumber' . $id . ', {
		        autoPlay:false, 
		        tracksToShow: playlistNumber' . $id . '.length, 
		        description: \'' . $description . '\', 
		        albumTitle: \'' . $album_title . '\', 
		        playerID: \'' . $id . '\', 
		        jPlayer:{
		            swfPath: \'' . get_template_directory_uri() . '/includes/woo-discography/assets/js/jquery-jplayer' . '\'' . "\n";
			if ( $is_gecko ) {
					$html .= ', supplied: \'mp3\'' . "\n";
					$html .= ', solution: \'flash\'' . "\n";
			}
		$html .= '
		        }
	    });' . "\n";
		
		wp_reset_postdata();
		
		return $html;
	 } // End setup_player()
	 
	 /**
	 * meta_box_setup function.
	 * 
	 * @access public
	 * @return void
	 */
	function meta_box_setup () {		
		add_meta_box( 'woo-' . $this->token, $this->meta_box_settings['title'], array( &$this, 'meta_box_content' ), $this->token, 'normal', 'low' );
	} // End meta_box_setup()
	
	/**
	 * meta_box_content function.
	 * 
	 * @access public
	 * @return void
	 */
	function meta_box_content () {
		global $post_id, $pagenow;

		$html = '';

		$html .= '<input type="hidden" name="woo_' . $this->token . '_noonce" id="woo_' . $this->token . '_noonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />' . "\n";
		
		$selected = get_post_meta( $post_id, '_selected_audio', true );

		if ( ! is_array( $selected ) ) {
			$selected = array();
		}
		
		// Get attached audio.
		$args = array( 'numberposts' => -1, 'post_type' => 'attachment', 'post_mime_type' => 'audio', 'post_parent' => $post_id, 'orderby' => 'menu_order', 'order' => 'ASC' );
		
		$posts = get_posts( $args );
		
		$tab_querystring = 'post_id=' . $post_id . '&TB_iframe=1&width=640&height=790&is_woo_audio=yes';
		
		$html .= '<p class="woo-manage-buttons">' . "\n";
		
		// Only on the non-"edit" screens.
		if ( $pagenow != 'post.php' ) {
			$html .= '<span id="submitpost">' . "\n";
			$html .= '<input type="submit" name="save" id="save-post" value="' . esc_attr__( 'Start Managing Tracks', 'woothemes' ) . '" tabindex="4" class="button button-highlighted woo-start-managing" />' . "\n";
			$html .= '<img src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="ajax-loading" id="draft-ajax-loading" alt="" />' . "\n";
			$html .= '<br class="clear" />' . "\n";
			$html .= '</span>' . "\n";
		}
	
		// Only on the "edit" screens.
		if ( $pagenow == 'post.php' ) {
			$html .= '<a href="' . admin_url( 'media-upload.php?tab=type&' . $tab_querystring ) . '" class="button thickbox" onclick="return false;">' . __( 'Upload', 'woothemes' ) . '</a>';
			$html .= '<a href="' . admin_url( 'media-upload.php?tab=gallery&' . $tab_querystring ) . '" class="button thickbox" onclick="return false;">' . __( 'Manage', 'woothemes' ) . '</a>';
			$html .= '<a href="#refresh" class="refresh hide-if-no-js">' . __( 'Refresh Tracks', 'woothemes' ) . '</a>' . "\n";
			$html .= '<a href="#select-all" class="select-all hide-if-no-js">' . __( 'Select All', 'woothemes' ) . '</a>' . "\n";
			$html .= '<a href="#clear-all" class="clear-all hide-if-no-js">' . __( 'Clear All', 'woothemes' ) . '</a>' . "\n";
			$html .= '<img src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="ajax-loading" style="display: inline; margin-left: 5px;" id="ajax-loading" alt="' . __( 'Loading', 'woothemes' ) . '" />' . "\n";

		}
		$html .= '</p>' . "\n";
		
		// Only on the "edit" screens.
		if ( $pagenow == 'post.php' ) {
			$html .= '<ul class="album-tracks">' . "\n";		
			if ( count( $posts ) > 0 ) {
				$html .= $this->generate_admin_checkbox_html( $posts, $selected );
			} else {
				$html .= '<li></li>' . "\n";
			}
			$html .= '</ul>' . "\n";
												
			$html .= '<p class="help">' . __( 'Use the checkboxes above to select which audio tracks you\'d like to display with this album. If none are selected, all tracks will be displayed.', 'woothemes' ) . '</p>' . "\n";
		}
		
		echo $html;
	} // End meta_box_content()
	
	/**
	 * meta_box_save function.
	 * 
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	function meta_box_save ( $post_id ) {
		global $post, $messages;
		
		// Verify
		if ( ( get_post_type() != $this->token ) || ! wp_verify_nonce( $_POST['woo_' . $this->token . '_noonce'], plugin_basename(__FILE__) ) ) {  
			return $post_id;  
		}
		 
		if ( 'page' == $_POST['post_type'] ) {  
			if ( ! current_user_can( 'edit_page', $post_id ) ) { 
				return $post_id;
			}
		} else {  
			if ( ! current_user_can( 'edit_post', $post_id ) ) { 
				return $post_id;
			}
		}
		
		if ( isset( $_POST['selected_audio'] ) && ( count( $_POST['selected_audio'] ) > 0 ) ) {
			
			delete_post_meta( $post_id, '_selected_audio' );
			
			$selected = array();
			
			foreach ( $_POST['selected_audio'] as $k => $v ) {
				$selected[] = $v;
			}
			
			add_post_meta( $post_id, '_selected_audio', $selected, false );
		} else {
			delete_post_meta( $post_id, '_selected_audio' ); // Double check
		}
		
	} // End meta_box_save()
	
	/**
	 * meta_box_refresh_content function.
	 * 
	 * @access public
	 * @return void
	 */
	function meta_box_refresh_content () {
		$nonce = $_POST['meta_box_content_nonce'];
		
		//Add nonce security to the request
		if ( ! wp_verify_nonce( $nonce, 'meta_box_content_nonce' ) ) {
			die();
		}
		
		$post_id = intval( $_POST['post_id'] );
		
		$selected = get_post_meta( $post_id, '_selected_audio', true );

		if ( ! is_array( $selected ) ) {
			$selected = array();
		}
		
		$current_ids = $_POST['current_ids'];
		
		// Get attached photographs.
		$args = array( 'numberposts' => -1, 'post_type' => 'attachment', 'post_mime_type' => 'audio', 'post_parent' => $post_id, 'post__not_in' => $current_ids );
		
		$posts = get_posts( $args );
		
		echo $this->generate_admin_checkbox_html( $posts, $selected_photos, false );
		
		die(); // WordPress may print out a spurious zero without this can be particularly bad if using JSON
	}// End meta_box_refresh_content()
	
	/**
	 * generate_admin_checkbox_html function.
	 * 
	 * @access public
	 * @param object $posts
	 * @return string $html
	 */
	function generate_admin_checkbox_html ( $posts, $selected, $wrapping_html = true ) {
		$html = '';
		if ( $wrapping_html == true ) {
			$html .= '<div class="thumbnails">' . "\n";
		}
		
		if ( ! is_array( $selected ) ) {
			$selected = array();
		}
		
		foreach ( $posts as $k => $post ) {
			setup_postdata( $post );
			
			$checked = '';
			$class = ' unselected';
			if ( in_array( $post->ID, $selected ) ) {
				$checked = ' checked="checked"';
				$class = ' selected';
			}
			$html .= '<li>' . "\n";
			$html .= '<label class="' . $class . '">' . "\n";
			$html .= '<input type="checkbox" name="selected_audio[]" value="' . $post->ID . '"' . $checked . ' class="alignleft" />' . "\n";
			$html .= $post->post_title . '</label>' . "\n";
			$html .= '</li>' . "\n";
		}
		if ( $wrapping_html == true ) {
			$html .= '</div>' . "\n";
		}
		
		return $html;
	} // End generate_admin_checkbox_html()
} // End Class
?>