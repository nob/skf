<?php
/**
 * WooThemes Photo Album Manager.
 *
 * Controls the management of photo albums.
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
 * - var $template_url
 *
 * - var $placeholder_image

 * - Constructor Function
 * - function init()
 * - function register_post_type()
 * - function register_taxonomies()
 * - function custom_post_permastruct()
 * - function customise_wooframework_meta_box()
 * - function enqueue_styles()
 * - function enqueue_scripts()
 * - function add_sortable_columns()
 * - function add_column_headings()
 * - function add_column_data()
 * - function sort_on_custom_columns()
 * - function enter_title_here()
 * - function meta_box_setup()
 * - function meta_box_content()
 * - function meta_box_save()
 * - function meta_box_refresh_content()
 * - function generate_admin_checkbox_html()
 * - function get_photos()
 * - function get_galleries()
 * - function get_categories()
 * - function get_cover_image()
 */

class WooThemes_Photos {

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
	
	var $meta_box_settings;
	
	var $taxonomies;
	
	var $template_url;
	
	var $placeholder_image;

	/**
	 * WooThemes_Photos function.
	 *
	 * @description Constructor function. Sets up the class and registers variable action hooks.
	 * @access public
	 * @return void
	 */
	function WooThemes_Photos () {
		$this->token = 'gallery';
		$this->singular = __( 'Gallery', 'woothemes' );
		$this->plural = __( 'Galleries', 'woothemes' );
		$this->dir = 'woo-photos';
		$this->rewrite_tag = '%woo_photos_category%';
		
		$this->meta_box_settings['title'] = __( 'Photographs in this Gallery', 'woothemes' );
		
		$this->taxonomies = array(
									'gallery_category' => array(
															'singular' => __( 'Category', 'woothemes' ), 
															'plural' => __( 'Categories', 'woothemes' ), 
															'rewrite' => 'gallery-categories'
														)
								);
		
		$this->template_url = get_template_directory_uri();
		
		$this->placeholder_image = get_template_directory_uri() . '/images/no-image.png';
	} // End WooThemes_Photos()
	
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

			add_action( 'wp_ajax_woo_photos_refresh', array( &$this, 'meta_box_refresh_content' ) );
			add_action( 'wp_ajax_nopriv_woo_photos_refresh', array( &$this, 'meta_box_refresh_content' ) ); 

			if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == $this->token ) || $pagenow == 'post.php' ) {
				add_action( 'admin_print_styles-edit.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-edit-tags.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-post.php', array( &$this, 'enqueue_styles' ), 10 );
				add_action( 'admin_print_styles-post-new.php', array( &$this, 'enqueue_styles' ), 10 );
				
				add_action( 'admin_print_scripts-edit.php', array( &$this, 'enqueue_scripts' ), 10 );
				add_action( 'admin_print_scripts-edit-tags.php', array( &$this, 'enqueue_scripts' ), 10 );
				add_action( 'admin_print_scripts-post.php', array( &$this, 'enqueue_scripts' ), 10 );
				add_action( 'admin_print_scripts-post-new.php', array( &$this, 'enqueue_scripts' ), 10 );
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
	    // 'rewrite' => apply_filters( 'woo_photos_rewrite_base', 'galleries' ), 
	    'rewrite' => array( 'slug' => apply_filters( 'woo_photos_rewrite_base', 'galleries' ) . '/' . $this->rewrite_tag . '' ),
	    'capability_type' => 'post',
	    'has_archive' => apply_filters( 'woo_photos_archive_rewrite_base', 'galleries' ), 
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
		$terms = get_the_terms( $post->ID, 'gallery_category' );
		
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
			$settings['title'] = __( 'Gallery Details', 'woothemes' );
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
	 * enqueue_scripts function.
	 * 
	 * @access public
	 * @return void
	 */
	function enqueue_scripts () {
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
		$new_columns['cover'] = __( 'Cover Image', 'woothemes' );
		$new_columns['gallery_category'] = __( 'Categories', 'woothemes' );
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
			
			case 'cover':
				the_post_thumbnail( array( 70 ) );
			break;
			
			case 'gallery_category':
				$value = __( 'No Categories Specified', 'woothemes' );
				$terms = get_the_terms( $id, 'gallery_category' );
				
				if ( $terms && ! is_wp_error( $terms ) ) {
					$term_links = array();
			
					foreach ( $terms as $term ) {
						$term_links[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $this->token, 'tag_ID' => $term->term_id, 'taxonomy' => 'gallery_category', 'action' => 'edit' ), 'edit-tags.php' ) ),
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
				in_array( $orderby, array( 'photo_count' ) ) && 
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
			$original = str_replace( 'featured image', __( 'photo album cover', 'woothemes' ), $original );
		}
		
		if ( ( strtolower( $original ) == 'featured image' ) && ( get_post_type() == $this->token ) ) {
			$original = __( 'Photo Album Cover', 'woothemes' );
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
			$title = __( 'Enter photo album title here', 'woothemes' );
		}
		
		return $title;
	} // End enter_title_here()
	
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
		
		$selected_photos = get_post_meta( $post_id, '_selected_photos', true );

		if ( ! is_array( $selected_photos ) ) {
			$selected_photos = array();
		}
		
		// Get attached photographs.
		$args = array( 'numberposts' => -1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_parent' => $post_id, 'orderby' => 'menu_order', 'order' => 'ASC' );
		
		$posts = get_posts( $args );
		
		$tab_querystring = 'post_id=' . $post_id . '&TB_iframe=1&width=640&height=790&is_woo_photo=yes';
		
		$html .= '<p class="woo-manage-buttons">' . "\n";
			
			// Only on the non-"edit" screens.
			if ( $pagenow != 'post.php' ) {
				$html .= '<span id="submitpost">' . "\n";
				$html .= '<input type="submit" name="save" id="save-post" value="' . esc_attr__( 'Start Managing Photos', 'woothemes' ) . '" tabindex="4" class="button button-highlighted woo-start-managing" />' . "\n";
				$html .= '<img src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="ajax-loading" id="draft-ajax-loading" alt="" />' . "\n";
				$html .= '<br class="clear" />' . "\n";
				$html .= '</span>' . "\n";
			}
			// Only on the "edit" screens.
			if ( $pagenow == 'post.php' ) {
				$html .= '<a href="' . admin_url( 'media-upload.php?tab=type&' . $tab_querystring ) . '" class="button thickbox" onclick="return false;">' . __( 'Upload', 'woothemes' ) . '</a>';
				$html .= '<a href="' . admin_url( 'media-upload.php?tab=gallery&' . $tab_querystring ) . '" class="button thickbox" onclick="return false;">' . __( 'Manage', 'woothemes' ) . '</a>';
			
				$html .= '<a href="#refresh" class="refresh hide-if-no-js">' . __( 'Refresh Thumbnails', 'woothemes' ) . '</a>' . "\n";
				$html .= '<a href="#select-all" class="select-all hide-if-no-js">' . __( 'Select All', 'woothemes' ) . '</a>' . "\n";
				$html .= '<a href="#clear-all" class="clear-all hide-if-no-js">' . __( 'Clear All', 'woothemes' ) . '</a>' . "\n";
				$html .= '<img src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="ajax-loading" style="display: inline; margin-left: 5px;" id="ajax-loading" alt="' . __( 'Loading', 'woothemes' ) . '" />' . "\n";
			}
		$html .= '</p>' . "\n";
		
		// Only on the "edit" screens.
		if ( $pagenow == 'post.php' ) {			
			if ( count( $posts ) > 0 ) {
				$html .= $this->generate_admin_checkbox_html( $posts, $selected_photos );
			} else {
				$html .= '<div class="thumbnails"><input type="hidden" name="placeholder" /></div>' . "\n";
			}
											
			$html .= '<p class="help">' . __( 'Use the thumbnails above to select which images you\'d like to display in this gallery. If none are selected, all images will be displayed.', 'woothemes' ) . '</p>' . "\n";
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
		
		if ( isset( $_POST['selected_photos'] ) && ( count( $_POST['selected_photos'] ) > 0 ) ) {
			
			delete_post_meta( $post_id, '_selected_photos' );
			
			$selected_photos;
			
			foreach ( $_POST['selected_photos'] as $k => $v ) {
				$selected_photos[] = $v;
			}
			
			add_post_meta( $post_id, '_selected_photos', $selected_photos, false );
		} else {
			delete_post_meta( $post_id, '_selected_photos' ); // Double check
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
		
		$selected_photos = get_post_meta( $post_id, '_selected_photos', true );

		if ( ! is_array( $selected_photos ) ) {
			$selected_photos = array();
		}
		
		$current_ids = $_POST['current_ids'];
		
		// Get attached photographs.
		$args = array( 'numberposts' => -1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_parent' => $post_id, 'post__not_in' => $current_ids );
		
		$posts = get_posts( $args );
		
		if ( count( $posts ) > 0 ) {
			echo $this->generate_admin_checkbox_html( $posts, $selected_photos, false );
		} else {
			echo '<input type="hidden" name="placeholder" />' . "\n";
		}
		
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
		foreach ( $posts as $k => $post ) {
			setup_postdata( $post );
			
			$checked = '';
			$class = ' unselected';
			if ( in_array( $post->ID, $selected ) ) {
				$checked = ' checked="checked"';
				$class = ' selected';
			}
			$html .= '<input type="checkbox" name="selected_photos[]" value="' . $post->ID . '"' . $checked . ' class="alignleft" />' . "\n";
			$html .= wp_get_attachment_image( $post->ID, array( 50, 50 ), false, array( 'class' => 'alignleft gallery-thumbnail' . $class ) ) . "\n";
		}
		if ( $wrapping_html == true ) {
			$html .= '</div>' . "\n";
		}
		
		return $html;
	} // End generate_admin_checkbox_html()
	
	/**
	 * get_photos function.
	 * 
	 * @access public
	 * @param array $args (default: '')
	 * @return array $photos
	 */
	function get_photos ( $args = '' ) {
		$defaults = array(
			'id' => 0, 
			'limit' => 5, 
			'orderby' => 'menu_order', 
			'order' => 'ASC'
		);

		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'get_photos_args', $args );
		
		$photos = array();
		
		$query_args = array(
							'numberposts' => $args['limit'], 
							'post_type' => 'attachment',  
							'orderby' => $args['orderby'], 
							'order' => $args['order'], 
							'post_mime_type' => 'image'
							);
		
		if ( $args['id'] > 0 ) {
			$query_args['post_parent'] = $args['id'];
			
			// Check for specific photos to be selected.
			$selected_photos = get_post_meta( $args['id'], '_selected_photos', true );
			
			if ( is_array( $selected_photos ) && ( count( $selected_photos ) > 0 ) ) {
				$query_args['post__in'] = $selected_photos;
			}
			
		}

		$entries = get_posts( $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$photos[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$photos = apply_filters( 'get_photos', $photos, $args );
		
		return $photos;
	} // End get_photos()
	
	/**
	 * get_galleries function.
	 * 
	 * @access public
	 * @param array $args (default: '')
	 * @return array $galleries
	 */
	function get_galleries ( $args = '' ) {
		$defaults = array(
			'id' => 0, 
			'limit' => 5, 
			'orderby' => 'menu_order', 
			'order' => 'ASC'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'get_galleries_args', $args );
		
		$galleries = array();
		
		$query_args = array(
							'numberposts' => $args['limit'], 
							'post_type' => 'gallery',  
							'orderby' => $args['orderby'], 
							'order' => $args['order']
							);
		
		if ( $args['id'] > 0 ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'gallery_category',
					'field' => 'id',
					'terms' => $args['id']
				)
			);
		}
		
		$entries = get_posts( $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$galleries[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$galleries = apply_filters( 'get_galleries', $galleries, $args );
		
		return $galleries;
	} // End get_galleries()
	
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
		$args = apply_filters( 'get_gallery_categories_args', $args );
		
		$galleries = array();
		
		$query_args = array(
							'number' => $args['limit'],  
							'orderby' => $args['orderby'], 
							'order' => $args['order']
							);
		
		$entries = get_terms( 'gallery_category', $query_args );
		
		if ( count( $entries ) > 0 ) {
			foreach ( $entries as $k => $v ) {
				$categories[] = $v;
			}
		}
		
		// Allow child themes/plugins to filter here.
		$categories = apply_filters( 'get_gallery_categories', $categories, $args );
		
		return $categories;
	} // End get_categories()
	
	/**
	 * get_cover_image function.
	 * 
	 * @access public
	 * @param string $image_args
	 * @return string $image
	 */
	function get_cover_image ( $image_args ) {
		global $post;
		$image = woo_image( $image_args );
				
		if ( ! $image ) {
			$args = array( 'id' => get_the_ID(), 'limit' => 1 );
			$photos = $this->get_photos( $args );
			
			if ( count( $photos ) > 0 ) {
				$image = woo_image( $image_args . '&src=' . $photos[0]->guid );
			} else {
				$image = woo_image( $image_args . '&src=' . $this->placeholder_image );
			}
		}
		
		return $image;
	} // End get_cover_image()
} // End Class
?>