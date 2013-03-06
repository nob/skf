<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page' );
}
?>
<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Exclude categories from displaying on the "Blog" page template.
- Exclude categories from displaying on the homepage.
- Register WP Menus
- Breadcrumb display
- Page navigation
- Post Meta
- Subscribe & Connect
- Comment Form Fields
- Comment Form Settings
- Archive Description
- WooPagination markup
- Google maps (for contact template)
- WooCommerce compatiblity
- Add custom CSS class to the <body> tag if the lightbox option is enabled.
- Load PrettyPhoto JavaScript and CSS if the lightbox option is enabled.
- Determine the number of columns currently displaying on the homepage - woo_get_homepage_column_count()
- Check if WooCommerce is activated
- Sticky shortcode

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Exclude categories from displaying on the "Blog" page template.
/*-----------------------------------------------------------------------------------*/

// Exclude categories on the "Blog" page template.
add_filter( 'woo_blog_template_query_args', 'woo_exclude_categories_blogtemplate' );

function woo_exclude_categories_blogtemplate ( $args ) {

	if ( ! function_exists( 'woo_prepare_category_ids_from_option' ) ) { return $args; }

	$excluded_cats = array();

	// Process the category data and convert all categories to IDs.
	$excluded_cats = woo_prepare_category_ids_from_option( 'woo_exclude_cats_blog' );

	// Homepage logic.
	if ( count( $excluded_cats ) > 0 ) {

		// Setup the categories as a string, because "category__not_in" doesn't seem to work
		// when using query_posts().

		foreach ( $excluded_cats as $k => $v ) { $excluded_cats[$k] = '-' . $v; }
		$cats = join( ',', $excluded_cats );

		$args['cat'] = $cats;
	}

	return $args;

} // End woo_exclude_categories_blogtemplate()

/*-----------------------------------------------------------------------------------*/
/* Exclude categories from displaying on the homepage.
/*-----------------------------------------------------------------------------------*/

// Exclude categories on the homepage.
add_filter( 'pre_get_posts', 'woo_exclude_categories_homepage' );

function woo_exclude_categories_homepage ( $query ) {

	if ( ! function_exists( 'woo_prepare_category_ids_from_option' ) ) { return $query; }

	$excluded_cats = array();

	// Process the category data and convert all categories to IDs.
	$excluded_cats = woo_prepare_category_ids_from_option( 'woo_exclude_cats_home' );

	// Homepage logic.
	if ( is_home() && ( count( $excluded_cats ) > 0 ) ) {
		$query->set( 'category__not_in', $excluded_cats );
	}

	$query->parse_query();

	return $query;

} // End woo_exclude_categories_homepage()

/*-----------------------------------------------------------------------------------*/
/* Register WP Menus */
/*-----------------------------------------------------------------------------------*/
if ( function_exists( 'wp_nav_menu') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array( 'primary-menu' => __( 'Primary Menu', 'woothemes' ) ) );
	register_nav_menus( array( 'top-menu' => __( 'Top Menu', 'woothemes' ) ) );
}

/*-----------------------------------------------------------------------------------*/
/* Breadcrumb display */
/*-----------------------------------------------------------------------------------*/

add_action('woo_main_before','woo_display_breadcrumbs',10);
if (!function_exists( 'woo_display_breadcrumbs')) {
	function woo_display_breadcrumbs() {
		global $woo_options;
		if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) {
		echo '<section id="breadcrumbs">';
			woo_breadcrumbs();
		echo '</section><!--/#breadcrumbs-->';
		}
	} // End woo_display_breadcrumbs()
} // End IF Statement


/*-----------------------------------------------------------------------------------*/
/* Page navigation */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'woo_pagenav')) {
	function woo_pagenav() {

		global $woo_options;

		// If the user has set the option to use simple paging links, display those. By default, display the pagination.
		if ( array_key_exists( 'woo_pagination_type', $woo_options ) && $woo_options[ 'woo_pagination_type' ] == 'simple' ) {
			if ( get_next_posts_link() || get_previous_posts_link() ) {
		?>
            <nav class="nav-entries fix">
                <?php next_posts_link( '<span class="nav-prev fl">'. __( '<span class="meta-nav">&larr;</span> Older posts', 'woothemes' ) . '</span>' ); ?>
                <?php previous_posts_link( '<span class="nav-next fr">'. __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'woothemes' ) . '</span>' ); ?>
            </nav>
		<?php
			}
		} else {
			woo_pagination();

		} // End IF Statement

	} // End woo_pagenav()
} // End IF Statement


/*-----------------------------------------------------------------------------------*/
/* Post Meta */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_post_meta')) {
	function woo_post_meta( ) {
?>
<aside class="post-meta">

	<span class="post-avatar">
		<?php echo get_avatar( get_the_author_meta('ID'), 50); ?>
	</span><!-- .post-avatar -->

	<?php if ( 'post' == get_post_type() ) { ?>
	<span class="post-comments">
		<?php

		$comment_count = get_comments_number();

		if ( comments_open() && $comment_count != 0)
			comments_popup_link( __( '0', 'woothemes' ), __( '1', 'woothemes' ), __( '%', 'woothemes' ) ); 
		
		?>
	</span><!-- .post-comments -->
	<?php } ?>

	<span class="post-date">
		<?php the_time( 'd F' ); ?>
	</span><!-- .post-date -->

</aside>
<?php if ( ( !is_home() || !is_front_page() ) && 'post' == get_post_type() ) { ?>
<p class="post-category"><?php _e('Categories: ', 'woothemes'); ?> <?php the_category( ', ') ?></p>
<?php } ?>
<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Portfolio Meta */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_portfolio_meta')) {
	function woo_portfolio_meta( ) {
?>
<aside class="portfolio-meta">
	<ul>
		<li class="portfolio-date">
			<?php the_time( get_option( 'date_format' ) ); ?>
		</li>
		<li class="portfolio-comments">
			<?php comments_popup_link( __( 'Leave a comment', 'woothemes' ), __( '1 Comment', 'woothemes' ), __( '% Comments', 'woothemes' ) ); ?>
		</li>
		<li><?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<li class="edit">', '</li>' ); ?></li>
	</ul>
</aside>
<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Subscribe / Connect */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_subscribe_connect')) {
	function woo_subscribe_connect($widget = 'false', $title = '', $form = '', $social = '', $contact_template = 'false') {

		//Setup default variables, overriding them if the "Theme Options" have been saved.
		$settings = array(
						'connect' => 'false', 
						'connect_title' => __('Subscribe' , 'woothemes'), 
						'connect_related' => 'true', 
						'connect_content' => __( 'Subscribe to our e-mail newsletter to receive updates.', 'woothemes' ),
						'connect_newsletter_id' => '', 
						'connect_mailchimp_list_url' => '',
						'feed_url' => '',
						'connect_rss' => '',
						'connect_twitter' => '',
						'connect_facebook' => '',
						'connect_youtube' => '',
						'connect_flickr' => '',
						'connect_linkedin' => '',
						'connect_delicious' => '',
						'connect_rss' => '',
						'connect_googleplus' => ''
						);
		$settings = woo_get_dynamic_values( $settings );

		// Setup title
		if ( $widget != 'true' )
			$title = $settings[ 'connect_title' ];

		// Setup related post (not in widget)
		$related_posts = '';
		if ( $settings[ 'connect_related' ] == "true" AND $widget != "true" )
			$related_posts = do_shortcode( '[related_posts limit="5"]' );

?>
	<?php if ( $settings[ 'connect' ] == "true" OR $widget == 'true' ) : ?>
	<aside id="connect" class="fix">
		<h3><?php if ( $title ) echo apply_filters( 'widget_title', $title ); else _e('Subscribe','woothemes'); ?></h3>

		<div <?php if ( $related_posts != '' ) echo 'class="col-left"'; ?>>
			<?php if ($settings[ 'connect_content' ] != '' AND $contact_template == 'false') echo '<p>' . stripslashes($settings[ 'connect_content' ]) . '</p>'; ?>

			<?php if ( $settings[ 'connect_newsletter_id' ] != "" AND $form != 'on' ) : ?>
			<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open( 'http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $settings[ 'connect_newsletter_id' ]; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520' );return true">
				<input class="email" type="text" name="email" value="<?php esc_attr_e( 'E-mail', 'woothemes' ); ?>" onfocus="if (this.value == '<?php _e( 'E-mail', 'woothemes' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'E-mail', 'woothemes' ); ?>';}" />
				<input type="hidden" value="<?php echo $settings[ 'connect_newsletter_id' ]; ?>" name="uri"/>
				<input type="hidden" value="<?php bloginfo( 'name' ); ?>" name="title"/>
				<input type="hidden" name="loc" value="en_US"/>
				<input class="submit" type="submit" name="submit" value="<?php _e( 'Submit', 'woothemes' ); ?>" />
			</form>
			<?php endif; ?>

			<?php if ( $settings['connect_mailchimp_list_url'] != "" && $form != 'on' && $settings['connect_newsletter_id'] == "" ) : ?>
			<!-- Begin MailChimp Signup Form -->
			<div id="mc_embed_signup">
				<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="<?php echo $settings['connect_mailchimp_list_url']; ?>" method="post" target="popupwindow" onsubmit="window.open('<?php echo $settings['connect_mailchimp_list_url']; ?>', 'popupwindow', 'scrollbars=yes,width=650,height=520');return true">
					<input type="text" name="EMAIL" class="required email" value="<?php _e('E-mail','woothemes'); ?>"  id="mce-EMAIL" onfocus="if (this.value == '<?php _e('E-mail','woothemes'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail','woothemes'); ?>';}">
					<input type="submit" value="<?php _e('Submit', 'woothemes'); ?>" name="subscribe" id="mc-embedded-subscribe" class="btn submit button">
				</form>
			</div>
			<!--End mc_embed_signup-->
			<?php endif; ?>

			<?php if ( $social != 'on' ) : ?>
			<div class="social<?php if ( $related_posts == '' && $settings['connect_newsletter_id' ] != "" ) echo ' fr'; ?>">
		   		<?php if ( $settings['connect_rss' ] == "true" ) { ?>
		   		<a href="<?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" title="RSS"></a>

		   		<?php } if ( $settings['connect_twitter' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_twitter'] ); ?>" class="twitter" title="<?php esc_attr_e( 'Twitter', 'woothemes' ); ?>"></a>

		   		<?php } if ( $settings['connect_facebook' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_facebook'] ); ?>" class="facebook" title="<?php esc_attr_e( 'Facebook', 'woothemes' ); ?>"></a>

		   		<?php } if ( $settings['connect_youtube' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_youtube'] ); ?>" class="youtube" title="<?php esc_attr_e( 'YouTube', 'woothemes' ); ?>"></a>

		   		<?php } if ( $settings['connect_flickr' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_flickr'] ); ?>" class="flickr" title="<?php esc_attr_e( 'Flickr', 'woothemes' ); ?>"></a>

		   		<?php } if ( $settings['connect_linkedin' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_linkedin'] ); ?>" class="linkedin" title="<?php esc_attr_e( 'LinkedIn', 'woothemes' ); ?>"></a>

		   		<?php } if ( $settings['connect_delicious' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_delicious'] ); ?>" class="delicious" title="<?php esc_attr_e( 'Delicious', 'woothemes' ); ?>"></a>

		   		<?php } if ( $settings['connect_googleplus' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_googleplus'] ); ?>" class="googleplus" title="<?php esc_attr_e( 'Google+', 'woothemes' ); ?>"></a>

				<?php } ?>
			</div>
			<?php endif; ?>

		</div><!-- col-left -->

		<?php if ( $settings['connect_related' ] == "true" AND $related_posts != '' ) : ?>
		<div class="related-posts col-right">
			<h4><?php _e( 'Related Posts:', 'woothemes' ); ?></h4>
			<?php echo $related_posts; ?>
		</div><!-- col-right -->
		<?php wp_reset_query(); endif; ?>

	</aside>
	<?php endif; ?>
<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Comment Form Fields */
/*-----------------------------------------------------------------------------------*/

	add_filter( 'comment_form_default_fields', 'woo_comment_form_fields' );

	if ( ! function_exists( 'woo_comment_form_fields' ) ) {
		function woo_comment_form_fields ( $fields ) {

			$commenter = wp_get_current_commenter();

			$required_text = ' <span class="required">(' . __( 'Required', 'woothemes' ) . ')</span>';

			$req = get_option( 'require_name_email' );
			$aria_req = ( $req ? " aria-required='true'" : '' );
			$fields =  array(
				'author' => '<p class="comment-form-author">' .
							'<input id="author" class="txt" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' .
							'<label for="author">' . __( 'Name' ) . ( $req ? $required_text : '' ) . '</label> ' .
							'</p>',
				'email'  => '<p class="comment-form-email">' .
				            '<input id="email" class="txt" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' .
				            '<label for="email">' . __( 'Email' ) . ( $req ? $required_text : '' ) . '</label> ' .
				            '</p>',
				'url'    => '<p class="comment-form-url">' .
				            '<input id="url" class="txt" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
				            '<label for="url">' . __( 'Website' ) . '</label>' .
				            '</p>',
			);

			return $fields;

		} // End woo_comment_form_fields()
	}

/*-----------------------------------------------------------------------------------*/
/* Comment Form Settings */
/*-----------------------------------------------------------------------------------*/

	add_filter( 'comment_form_defaults', 'woo_comment_form_settings' );

	if ( ! function_exists( 'woo_comment_form_settings' ) ) {
		function woo_comment_form_settings ( $settings ) {

			$settings['comment_notes_before'] = '';
			$settings['comment_notes_after'] = '';
			$settings['label_submit'] = __( 'Submit Comment', 'woothemes' );
			$settings['cancel_reply_link'] = __( 'Click here to cancel reply.', 'woothemes' );

			return $settings;

		} // End woo_comment_form_settings()
	}

	/*-----------------------------------------------------------------------------------*/
	/* Misc back compat */
	/*-----------------------------------------------------------------------------------*/

	// array_fill_keys doesn't exist in PHP < 5.2
	// Can remove this after PHP <  5.2 support is dropped
	if ( !function_exists( 'array_fill_keys' ) ) {
		function array_fill_keys( $keys, $value ) {
			return array_combine( $keys, array_fill( 0, count( $keys ), $value ) );
		}
	}

/*-----------------------------------------------------------------------------------*/
/**
 * woo_archive_description()
 *
 * Display a description, if available, for the archive being viewed (category, tag, other taxonomy).
 *
 * @since V1.0.0
 * @uses do_atomic(), get_queried_object(), term_description()
 * @echo string
 * @filter woo_archive_description
 */

if ( ! function_exists( 'woo_archive_description' ) ) {
	function woo_archive_description ( $echo = true ) {
		do_action( 'woo_archive_description' );
		
		// Archive Description, if one is available.
		$term_obj = get_queried_object();
		$description = term_description( $term_obj->term_id, $term_obj->taxonomy );
		
		if ( $description != '' ) {
			// Allow child themes/plugins to filter here ( 1: text in DIV and paragraph, 2: term object )
			$description = apply_filters( 'woo_archive_description', '<div class="archive-description">' . $description . '</div><!--/.archive-description-->', $term_obj );
		}
		
		if ( $echo != true ) { return $description; }
		
		echo $description;
	} // End woo_archive_description()
}

/*-----------------------------------------------------------------------------------*/
/* WooPagination Markup */
/*-----------------------------------------------------------------------------------*/

add_filter( 'woo_pagination_args', 'woo_pagination_html5_markup', 2 );

function woo_pagination_html5_markup ( $args ) {
	$args['before'] = '<nav class="pagination woo-pagination">';
	$args['after'] = '</nav>';
	
	return $args;
} // End woo_pagination_html5_markup()


/*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Info Boxes */
/*-----------------------------------------------------------------------------------*/

add_action('init', 'woo_add_features');
function woo_add_features() 
{
  $labels = array(
    'name' => _x('Features', 'post type general name', 'woothemes'),
    'singular_name' => _x('Feature', 'post type singular name', 'woothemes'),
    'add_new' => _x('Add New', 'features', 'woothemes'),
    'add_new_item' => __('Add New Feature', 'woothemes'),
    'edit_item' => __('Edit Feature', 'woothemes'),
    'new_item' => __('New Feature', 'woothemes'),
    'view_item' => __('View Feature', 'woothemes'),
    'search_items' => __('Search Features', 'woothemes'),
    'not_found' =>  __('No Features found', 'woothemes'),
    'not_found_in_trash' => __('No Features found in Trash', 'woothemes'), 
    'parent_item_colon' => ''
  );
  
  $features_rewrite = get_option('woo_features_rewrite');
  if(empty($features_rewrite)) $features_rewrite = 'features';
  
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => array('slug'=> $features_rewrite),
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_icon' => get_template_directory_uri() .'/includes/images/box.png',
    'menu_position' => null,
    'has_archive' => true,
    'supports' => array('title','editor','thumbnail'/*'author','excerpt','comments'*/)
  ); 
  register_post_type('features',$args);
}

/*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Slides */
/*-----------------------------------------------------------------------------------*/

add_action('init', 'woo_add_slides');
function woo_add_slides() 
{
  $labels = array(
    'name' => _x('Slides', 'post type general name', 'woothemes', 'woothemes'),
    'singular_name' => _x('Slide', 'post type singular name', 'woothemes'),
    'add_new' => _x('Add New', 'slide', 'woothemes'),
    'add_new_item' => __('Add New Slide', 'woothemes'),
    'edit_item' => __('Edit Slide', 'woothemes'),
    'new_item' => __('New Slide', 'woothemes'),
    'view_item' => __('View Slide', 'woothemes'),
    'search_items' => __('Search Slides', 'woothemes'),
    'not_found' =>  __('No slides found', 'woothemes'),
    'not_found_in_trash' => __('No slides found in Trash', 'woothemes'), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_icon' => get_template_directory_uri() .'/includes/images/slides.png',
    'menu_position' => null,
    'supports' => array('title','editor','thumbnail', /*'author','thumbnail','excerpt','comments'*/)
  ); 
  register_post_type('slide',$args);

  // "Slide Pages" Custom Taxonomy
	$labels = array(
		'name' => _x( 'Slide Groups', 'taxonomy general name', 'woothemes' ),
		'singular_name' => _x( 'Slide Groups', 'taxonomy singular name', 'woothemes' ),
		'search_items' =>  __( 'Search Slide Groups', 'woothemes' ),
		'all_items' => __( 'All Slide Groups', 'woothemes' ),
		'parent_item' => __( 'Parent Slide Group', 'woothemes' ),
		'parent_item_colon' => __( 'Parent Slide Group:', 'woothemes' ),
		'edit_item' => __( 'Edit Slide Group', 'woothemes' ), 
		'update_item' => __( 'Update Slide Group', 'woothemes' ),
		'add_new_item' => __( 'Add New Slide Group', 'woothemes' ),
		'new_item_name' => __( 'New Slide Group Name', 'woothemes' ),
		'menu_name' => __( 'Slide Groups', 'woothemes' )
	); 	

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'slide-page' )
	);

	register_taxonomy( 'slide-page', array( 'slide' ), $args );
} // End woo_add_slides()

/*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Feedback (Feedback Component) */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_add_feedback' ) ) {
	function woo_add_feedback() {
		global $woo_options;
		
		if ( ( isset( $woo_options['woo_feedback_disable'] ) && $woo_options['woo_feedback_disable'] == 'true' ) ) { return; }
		
		$labels = array(
			'name' => _x( 'Feedback', 'post type general name', 'woothemes' ),
			'singular_name' => _x( 'Feedback Item', 'post type singular name', 'woothemes' ),
			'add_new' => _x( 'Add New', 'slide', 'woothemes' ),
			'add_new_item' => __( 'Add New Feedback Item', 'woothemes' ),
			'edit_item' => __( 'Edit Feedback Item', 'woothemes' ),
			'new_item' => __( 'New Feedback Item', 'woothemes' ),
			'view_item' => __( 'View Feedback Item', 'woothemes' ),
			'search_items' => __( 'Search Feedback Items', 'woothemes' ),
			'not_found' =>  __( 'No Feedback Items found', 'woothemes' ),
			'not_found_in_trash' => __( 'No Feedback Items found in Trash', 'woothemes' ), 
			'parent_item_colon' => ''
		);
		
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true, 
			'_builtin' => false,
			'show_ui' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_icon' => get_template_directory_uri() .'/includes/images/feedback.png',
			'menu_position' => null,
			'supports' => array( 'title', 'editor'/*, 'author', 'thumbnail', 'excerpt', 'comments'*/ ),
		);
		
		register_post_type( 'feedback', $args );

	} // End woo_add_feedback()
}

add_action( 'init', 'woo_add_feedback', 10 );

/*-----------------------------------------------------------------------------------*/
/* Woo Feedback, woo_get_feedback_entries() */
/*
/* Get feedback entries.
/*
/* @param array/string $args
/* @since 4.5.0
/* @return array/boolean
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_get_feedback_entries' ) ) {
	function woo_get_feedback_entries ( $args = '' ) {
		$defaults = array(
			'limit' => 5, 
			'orderby' => 'post_date', 
			'order' => 'DESC', 
			'id' => 0
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'woo_get_feedback_args', $args );
		
		// The Query Arguments.
		$query_args = array();
		$query_args['post_type'] = 'feedback';
		$query_args['numberposts'] = $args['limit'];
		$query_args['orderby'] = $args['orderby'];
		$query_args['order'] = $args['order'];
		
		if ( is_numeric( $args['id'] ) && ( intval( $args['id'] ) > 0 ) ) {
			$query_args['p'] = intval( $args['id'] );
		}
		
		// Whitelist checks.
		if ( ! in_array( $query_args['orderby'], array( 'none', 'ID', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order', 'meta_value', 'meta_value_num' ) ) ) {
			$query_args['orderby'] = 'date';
		}
		
		if ( ! in_array( $query_args['order'], array( 'ASC', 'DESC' ) ) ) {
			$query_args['order'] = 'DESC';
		}
		
		if ( ! in_array( $query_args['post_type'], get_post_types() ) ) {
			$query_args['post_type'] = 'feedback';
		}
		
		// The Query.
		$query = get_posts( $query_args );
		
		// The Display.
		if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {} else {
			$query = false;
		}
		
		return $query;
	} // End woo_get_feedback_entries()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Feedback, woo_display_feedback_entries() */
/*
/* Display posts of the "feedback" post type.
/*
/* @param array/string $args
/* @since 4.5.0
/* @return string $html (if "echo" not set to true)
/* @uses woo_get_feedback_entries()
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_display_feedback_entries' ) ) {
	function woo_display_feedback_entries ( $args = '' ) {
		$defaults = array(
			'limit' => 5, 
			'orderby' => 'rand', 
			'order' => 'DESC', 
			'id' => 0, 
			'display_author' => true, 
			'display_url' => true, 
			'effect' => 'fade', // Options: 'fade', 'none'
			'pagination' => false, 
			'echo' => true
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'woo_display_feedback_args', $args );
		
		$html = '';
		
		woo_do_atomic( 'woo_feedback_before', $args );
		
		// The Query.
		$query = woo_get_feedback_entries( $args );
		
		// The Display.
		if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {
			
			if ( $args['effect'] != 'none' ) {
				$effect = ' ' . $args['effect'];
			}
			
			$html .= '<div class="feedback' . $effect . '">' . "\n";
			if ( $args['pagination'] == true && count( $query ) > 1 && $args['effect'] != 'none' ) {
			
				$html .= '<div class="pagination fix">' . "\n";
		        $html .= '<a href="#" class="btn-next">' . apply_filters( 'woo_feedback_next_btn', __( 'Next', 'woothemes' ) . ' &rarr;' ) . '</a>' . "\n";
		        $html .= '<a href="#" class="btn-prev">' . apply_filters( 'woo_feedback_prev_btn', '&larr; ' . __( 'Previous', 'woothemes' ) ) . '</a>' . "\n";
		        $html .= '</div><!--/.pagination-->' . "\n";
			
			}
			$html .= '<div class="feedback-list">' . "\n";
		
			foreach ( $query as $post ) {
				setup_postdata( $post );
				
				$author = '';
				$author_text = '';
				
				// If we need to display either the author, URL or both, get the data.
				if ( $args['display_author'] == true || $args['display_url'] == true ) {
					$meta = get_post_custom( $post->ID );
					
					if ( isset( $meta['feedback_author'] ) && ( $meta['feedback_author'][0] != '' ) && $args['display_author'] == true ) {
						$author .= '<cite class="feedback-author">' . $meta['feedback_author'][0] . '</cite><!--/.feedback-author-->' . "\n";
					}
					
					if ( isset( $meta['feedback_url'] ) && ( $meta['feedback_url'][0] != '' ) && $args['display_url'] == true ) {
						$author .= '<a href="' . $meta['feedback_url'][0] . '" title="' . esc_attr( $author_text ) . '" class="feedback-url">' . $meta['feedback_url'][0] . '</a>';
					}
				}
				
				$html .= '<div id="quote-' . $post->ID . '" class="quote">' . "\n";
					$html .= '<blockquote class="feedback-text">' . get_the_content() . '</blockquote>' . "\n";
					$html .= $author;
				$html .= '</div>' . "\n";
			}
			
			$html .= '</div><!--/.feedback-list-->' . "\n";
			
			$html .= '</div><!--/.feedback-->' . "\n";
		}
		
		// Allow child themes/plugins to filter here.
		$html = apply_filters( 'woo_feedback_html', $html, $query );
		
		if ( $args['echo'] != true ) { return $html; }
		
		// Should only run is "echo" is set to true.
		echo $html;
		
		woo_do_atomic( 'woo_feedback_after', $args ); // Only if "echo" is set to true.
		
		wp_reset_postdata();
	} // End woo_display_feedback_entries()
}

 /*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Portfolio Item (Portfolio Component) */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_add_portfolio' ) ) {
	function woo_add_portfolio() {
	
		global $woo_options;
	
		// "Portfolio Item" Custom Post Type
		$labels = array(
			'name' => _x( 'Portfolio', 'post type general name', 'woothemes' ),
			'singular_name' => _x( 'Portfolio Item', 'post type singular name', 'woothemes' ),
			'add_new' => _x( 'Add New', 'slide', 'woothemes' ),
			'add_new_item' => __( 'Add New Portfolio Item', 'woothemes' ),
			'edit_item' => __( 'Edit Portfolio Item', 'woothemes' ),
			'new_item' => __( 'New Portfolio Item', 'woothemes' ),
			'view_item' => __( 'View Portfolio Item', 'woothemes' ),
			'search_items' => __( 'Search Portfolio Items', 'woothemes' ),
			'not_found' =>  __( 'No portfolio items found', 'woothemes' ),
			'not_found_in_trash' => __( 'No portfolio items found in Trash', 'woothemes' ), 
			'parent_item_colon' => ''
		);
		
		$portfolioitems_rewrite = get_option( 'woo_portfolioitems_rewrite' );
 		if( empty( $portfolioitems_rewrite ) ) { $portfolioitems_rewrite = 'portfolio-items'; }
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'query_var' => true,
			'rewrite' => array( 'slug' => $portfolioitems_rewrite ),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_icon' => get_template_directory_uri() .'/includes/images/portfolio.png',
			'menu_position' => null, 
			'has_archive' => true, 
			'taxonomies' => array( 'portfolio-gallery' ), 
			'supports' => array( 'title','editor','thumbnail', 'comments')
		);
		
		if ( isset( $woo_options['woo_portfolio_excludesearch'] ) && ( $woo_options['woo_portfolio_excludesearch'] == 'true' ) ) {
			$args['exclude_from_search'] = true;
		}
		
		register_post_type( 'portfolio', $args );
		
		// "Portfolio Galleries" Custom Taxonomy
		$labels = array(
			'name' => _x( 'Portfolio Galleries', 'taxonomy general name', 'woothemes' ),
			'singular_name' => _x( 'Portfolio Gallery', 'taxonomy singular name', 'woothemes' ),
			'search_items' =>  __( 'Search Portfolio Galleries', 'woothemes' ),
			'all_items' => __( 'All Portfolio Galleries', 'woothemes' ),
			'parent_item' => __( 'Parent Portfolio Gallery', 'woothemes' ),
			'parent_item_colon' => __( 'Parent Portfolio Gallery:', 'woothemes' ),
			'edit_item' => __( 'Edit Portfolio Gallery', 'woothemes' ), 
			'update_item' => __( 'Update Portfolio Gallery', 'woothemes' ),
			'add_new_item' => __( 'Add New Portfolio Gallery', 'woothemes' ),
			'new_item_name' => __( 'New Portfolio Gallery Name', 'woothemes' ),
			'menu_name' => __( 'Portfolio Galleries', 'woothemes' )
		); 	
		
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'portfolio-gallery' )
		);
		
		register_taxonomy( 'portfolio-gallery', array( 'portfolio' ), $args );
	}
	
	add_action( 'init', 'woo_add_portfolio' );
}

/*-----------------------------------------------------------------------------------*/
/* Woo Portfolio Navigation */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_portfolio_navigation' ) ) {
	function woo_portfolio_navigation ( $galleries, $settings = array(), $toggle_pagination = false ) {

		// Sanity check.
		if ( ! is_array( $galleries ) || ( count( $galleries ) <= 0 ) ) { return; }
		
		global $woo_options, $wp_query;
		
		$defaults = array(
						'id' => 'port-tags', 
						'label' => '', 
						'display_all' => true, 
						'current' => 'all'
						 );
		
		$settings = wp_parse_args( $settings, $defaults );
					 
		$settings = apply_filters( 'woo_portfolio_navigation_args', $settings );
		
		// Prepare the anchor tags of the various gallery items.
		$gallery_anchors = '';
		foreach ( $galleries as $g ) {
			$current_class = '';

			if ( $settings['current'] == $g->term_id ) {
				$current_class = ' current';
			}

$permalink = '#' . $g->slug;
if ( is_tax() || $toggle_pagination == true ) {
	$permalink = get_term_link( $g, 'portfolio-gallery' );
}

$gallery_anchors .= '<a href="' . $permalink . '" rel="' . $g->slug . '" class="navigation-slug-' . $g->slug . ' navigation-id-' . $g->term_id . $current_class . '">' . $g->name . '</a>' . "\n";
		}
		
		$html = '<div id="' . $settings['id'] . '" class="port-tags">' . "\n";
			$html .= '<div class="fl">' . "\n";
				$html .= '<span class="port-cat">' . "\n";
				
				// Display label, if one is set.
				if ( $settings['label'] != '' ) { $html .= $settings['label'] . ' '; }
				
				// Display "All", if set to "true".
				if ( $settings['display_all'] == 'all' ) {
					$all_permalink = '#';
					if ( is_tax() || $toggle_pagination == true ) {
						$all_permalink = get_post_type_archive_link( 'portfolio' );
					}
					
					$all_current = '';
					if ( $settings['current'] == 'all' ) {
						$all_current = ' class="current"';
					}
					$html .= '<a href="' . $all_permalink . '" rel="all"' . $all_current . '>' . __( 'All','woothemes' ) . '</a> ';
				}
				
				// Add the gallery anchors in.
				$html .= $gallery_anchors;
				
				$html .= '</span>' . "\n";
			$html .= '</div><!--/.fl-->' . "\n";
			$html .= '<div class="fix"></div>' . "\n";
		$html .= '</div><!--/#' . $settings['id'] . ' .port-tags-->' . "\n";
		
		
		$html = apply_filters( 'woo_portfolio_navigation', $html );
		
		echo $html;
	
	} // End woo_portfolio_navigation()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Portfolio Item Settings */
/* @uses woo_portfolio_image_dimensions() */
/*-----------------------------------------------------------------------------------*/
 
if ( !function_exists( 'woo_portfolio_item_settings' ) ) {
	function woo_portfolio_item_settings ( $id ) {
		
		global $woo_options;
		
		// Sanity check.
		if ( ! is_numeric( $id ) ) { return; }
		
		$website_layout = 'two-col-left';
		$website_width = '900px';
		
		if ( isset( $woo_options['woo_layout'] ) ) { $website_layout = $woo_options['woo_layout']; }
		if ( isset( $woo_options['woo_layout_width'] ) ) { $website_width = $woo_options['woo_layout_width']; }
		
		$dimensions = woo_portfolio_image_dimensions( $website_layout, $website_width );
		
		$width = $dimensions['width'];
		$height = $dimensions['height'];
		
		
		$settings = array(
							'large' => '', 
							'caption' => '', 
							'rel' => '', 
							'gallery' => array(), 
							'css_classes' => 'group post portfolio-img', 
							'embed' => '', 
							'testimonial' => '', 
							'testimonial_author' => '', 
							'display_url' => '', 
							'width' => $width, 
							'height' => $height
						 );
		
		$meta = get_post_custom( $id );
		
		// Check if there is a gallery in post.
		// woo_get_post_images is offset by 1 by default. Setting to offset by 0 to show all images.
		
		$large = '';
		if ( isset( $meta['portfolio-image'][0] ) )
			$large = $meta['portfolio-image'][0];
			
		$caption = '';
		
		
		$rel = 'rel="lightbox['. $id .']"';

		// Check if there are more than 1 image
    	$gallery = woo_get_post_images( '0' );
    	
	    // If we only have one image, disable the gallery functionality.
	    if ( isset( $gallery ) && is_array( $gallery ) && ( count( $gallery ) <= 1 ) ) {
			$rel = 'rel="lightbox"';
	    }
	    
	    // Check for a post thumbnail, if support for it is enabled.
	    if ( isset( $woo_options['woo_post_image_support'] ) && ( $woo_options['woo_post_image_support'] == 'true' ) && current_theme_supports( 'post-thumbnails' ) ) {
	    	$image_id = get_post_thumbnail_id( $id );
	    	if ( intval( $image_id ) > 0 ) {
	    		$large_data = wp_get_attachment_image_src( $image_id, 'large' );
	    		if ( is_array( $large_data ) ) {
	    			$large = $large_data[0];
	    		}
	    	}
	    }
	    
	    // See if lightbox-url custom field has a value
	    if ( isset( $meta['lightbox-url'] ) && ( $meta['lightbox-url'][0] != '' ) ) {
	    	$large = $meta['lightbox-url'][0];
	    }
	    		
		// Create CSS classes string.
		$css = '';
		$galleries = array();
		$terms = get_the_terms( $id, 'portfolio-gallery' );
		if ( is_array( $terms ) && ( count( $terms ) > 0 ) ) { foreach ( $terms as $t ) { $galleries[] = $t->slug; } }				
		$css = join( ' ', $galleries );
		
		// If on the single item screen, check for a video.
		if ( is_singular() ) { $settings['embed'] = woo_embed( 'width=540' ); }
		
		// Add testimonial information.
		if ( isset( $meta['testimonial'] ) && ( $meta['testimonial'][0] != '' ) ) {
			$settings['testimonial'] = $meta['testimonial'][0];
		}
		
		if ( isset( $meta['testimonial_author'] ) && ( $meta['testimonial_author'][0] != '' ) ) {
			$settings['testimonial_author'] = $meta['testimonial_author'][0];
		}
		
		// Look for a custom display URL of the portfolio item (used if it's a website, for example)
		if ( isset( $meta['url'] ) && ( $meta['url'][0] != '' ) ) {
			$settings['display_url'] = $meta['url'][0];
		}
		
		// Assign the values we have to our array.
		$settings['large'] = $large;
		$settings['caption'] = $caption;

		if ( isset( $woo_options['woo_enable_lightbox'] ) && $woo_options['woo_enable_lightbox'] == 'true' ) {
			$settings['rel'] = $rel;
		}
		if (isset( $gallery )) { $settings['gallery'] = $gallery; } else { $settings['gallery'] = array(); }
		$settings['css_classes'] .= ' ' . $css;
				
		// Check for a custom description.
		$description = get_post_meta( $id, 'lightbox-description', true );
		if ( $description != ''  ) { $settings['caption'] = $description; }
		
		// Allow child themes/plugins to filter these settings.
		$settings = apply_filters( 'woo_portfolio_item_settings', $settings, $id );
		
		return $settings;
	} // End woo_portfolio_item_settings()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Portfolio, show portfolio galleries in portfolio item breadcrumbs */
/* Modify woo_breadcrumbs() Arguments Specific to this Theme */
/*-----------------------------------------------------------------------------------*/

add_filter( 'woo_breadcrumbs_args', 'woo_portfolio_filter_breadcrumbs_args', 10 );

if ( !function_exists( 'woo_portfolio_filter_breadcrumbs_args' ) ) {
	function woo_portfolio_filter_breadcrumbs_args( $args ) {
	
		$args['singular_portfolio_taxonomy'] = 'portfolio-gallery';
	
		return $args;
	
	} // End woo_portfolio_filter_breadcrumbs_args()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Portfolio, get image dimensions based on layout and website width settings. */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'woo_portfolio_image_dimensions' ) ) {
	function woo_portfolio_image_dimensions ( $layout = 'one-col', $width = '960' ) {
		
		$dimensions = array( 'width' => 575, 'height' => 0, 'thumb_width' => 175, 'thumb_height' => 175 );
		
		// Allow child themes/plugins to filter these dimensions.
		$dimensinos = apply_filters( 'woo_portfolio_gallery_dimensions', $dimensions );
	
		return $dimensions;
	
	} // End woo_post_gallery_dimensions()
}

/*-----------------------------------------------------------------------------------*/
/* Get Post image attachments */
/*-----------------------------------------------------------------------------------*/
/* 
Description:

This function will get all the attached post images that have been uploaded via the 
WP post image upload and return them in an array. 

*/
if ( !function_exists( 'woo_get_post_images' ) ) {
	function woo_get_post_images( $offset = 1, $size = 'large' ) {
		
		// Arguments
		$repeat = 100; 				// Number of maximum attachments to get 
		$photo_size = 'large';		// The WP "size" to use for the large image
	
		global $post;
	
		$output = array();
	
		$id = get_the_id();
		$attachments = get_children( array(
		'post_parent' => $id,
		'numberposts' => $repeat,
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'order' => 'ASC', 
		'orderby' => 'menu_order date' )
		);
		if ( !empty($attachments) ) :
			$output = array();
			$count = 0;
			foreach ( $attachments as $att_id => $attachment ) {
				$count++;  
				if ($count <= $offset) continue;
				$url = wp_get_attachment_image_src($att_id, $photo_size, true);	
					$output[] = array( 'url' => $url[0], 'caption' => $attachment->post_excerpt, 'id' => $att_id );
			}  
		endif; 
		return $output;
	} // End woo_get_post_images()
}

/**
 * woo_portfolio_add_post_classes function.
 * 
 * @access public
 * @param array $classes
 * @return array $classes
 */

add_filter( 'post_class', 'woo_portfolio_add_post_classes', 10 );
 
function woo_portfolio_add_post_classes ( $classes ) {
	if ( in_array( 'portfolio', $classes ) ) {
		global $post;
		
		$terms = get_the_terms( $post->ID, 'portfolio-gallery' );

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$classes[] = $t->slug;
			}
		}
		
		if ( ! is_singular() ) {
			foreach ( $classes as $k => $v ) {
				if ( in_array( $v, array( 'hentry', 'portfolio' ) ) ) {
					unset( $classes[$k] );
				}
			}
		}
	}
	return $classes;
} // End woo_portfolio_add_post_classes()


/*-----------------------------------------------------------------------------------*/
/* Google Maps */
/*-----------------------------------------------------------------------------------*/

function woo_maps_contact_output($args){

	$key = get_option('woo_maps_apikey');
	
	// No More API Key needed
	
	if ( !is_array($args) ) 
		parse_str( $args, $args );
		
	extract($args);	
	$mode = '';
	$streetview = 'off';	
	$map_height = get_option('woo_maps_single_height');
	$featured_w = get_option('woo_home_featured_w');
	$featured_h = get_option('woo_home_featured_h');
	$zoom = get_option('woo_maps_default_mapzoom');
	$type = get_option('woo_maps_default_maptype');
	$marker_title = get_option('woo_contact_title');
	if ( $zoom == '' ) { $zoom = 6; }   
	$lang = get_option('woo_maps_directions_locale');
	$locale = '';
	if(!empty($lang)){
		$locale = ',locale :"'.$lang.'"';
	}
	$extra_params = ',{travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true '.$locale.'}';
	
	if(empty($map_height)) { $map_height = 250;}
	
	if(is_home() && !empty($featured_h) && !empty($featured_w)){
	?>
    <div id="single_map_canvas" style="width:<?php echo $featured_w; ?>px; height: <?php echo $featured_h; ?>px"></div>
    <?php } else { ?> 
    <div id="single_map_canvas" style="width:100%; height: <?php echo $map_height; ?>px"></div>
    <?php } ?>
    <script src="<?php echo get_template_directory_uri(); ?>/includes/js/markers.js" type="text/javascript"></script>
    <script type="text/javascript">
		jQuery(document).ready(function(){
			function initialize() {
				
				
			<?php if($streetview == 'on'){ ?>

				
			<?php } else { ?>
				
			  	<?php switch ($type) {
			  			case 'G_NORMAL_MAP':
			  				$type = 'ROADMAP';
			  				break;
			  			case 'G_SATELLITE_MAP':
			  				$type = 'SATELLITE';
			  				break;
			  			case 'G_HYBRID_MAP':
			  				$type = 'HYBRID';
			  				break;
			  			case 'G_PHYSICAL_MAP':
			  				$type = 'TERRAIN';
			  				break;
			  			default:
			  				$type = 'ROADMAP';
			  				break;
			  	} ?>
			  	
			  	var myLatlng = new google.maps.LatLng(<?php echo $geocoords; ?>);
				var myOptions = {
				  zoom: <?php echo $zoom; ?>,
				  center: myLatlng,
				  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
				};
			  	var map = new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);
				<?php if(get_option('woo_maps_scroll') == 'true'){ ?>
			  	map.scrollwheel = false;
			  	<?php } ?>
			  	
				<?php if($mode == 'directions'){ ?>
			  	directionsPanel = document.getElementById("featured-route");
 				directions = new GDirections(map, directionsPanel);
  				directions.load("from: <?php echo $from; ?> to: <?php echo $to; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
			  	<?php
			 	} else { ?>
			 
			  		var point = new google.maps.LatLng(<?php echo $geocoords; ?>);
	  				var root = "<?php echo get_template_directory_uri(); ?>";
	  				var callout = '<?php echo preg_replace("/[\n\r]/","<br/>",get_option('woo_maps_callout_text')); ?>';
	  				var the_link = '<?php echo get_permalink(get_the_id()); ?>';
	  				<?php $title = str_replace(array('&#8220;','&#8221;'),'"', $marker_title); ?>
	  				<?php $title = str_replace('&#8211;','-',$title); ?>
	  				<?php $title = str_replace('&#8217;',"`",$title); ?>
	  				<?php $title = str_replace('&#038;','&',$title); ?>
	  				var the_title = '<?php echo html_entity_decode($title) ?>'; 
	  				
	  			<?php		 	
			 	if(is_page()){ 
			 		$custom = get_option('woo_cat_custom_marker_pages');
					if(!empty($custom)){
						$color = $custom;
					}
					else {
						$color = get_option('woo_cat_colors_pages');
						if (empty($color)) {
							$color = 'red';
						}
					}			 	
			 	?>
			 		var color = '<?php echo $color; ?>';
			 		createMarker(map,point,root,the_link,the_title,color,callout);
			 	<?php } else { ?>
			 		var color = '<?php echo get_option('woo_cat_colors_pages'); ?>';
	  				createMarker(map,point,root,the_link,the_title,color,callout);
				<?php 
				}
					if(isset($_POST['woo_maps_directions_search'])){ ?>
					
					directionsPanel = document.getElementById("featured-route");
 					directions = new GDirections(map, directionsPanel);
  					directions.load("from: <?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?> to: <?php echo $address; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
  					
  					
  					
					directionsDisplay = new google.maps.DirectionsRenderer();
					directionsDisplay.setMap(map);
    				directionsDisplay.setPanel(document.getElementById("featured-route"));
					
					<?php if($walking == 'on'){ ?>
					var travelmodesetting = google.maps.DirectionsTravelMode.WALKING;
					<?php } else { ?>
					var travelmodesetting = google.maps.DirectionsTravelMode.DRIVING;
					<?php } ?>
					var start = '<?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?>';
					var end = '<?php echo $address; ?>';
					var request = {
       					origin:start, 
        				destination:end,
        				travelMode: travelmodesetting
    				};
    				directionsService.route(request, function(response, status) {
      					if (status == google.maps.DirectionsStatus.OK) {
        					directionsDisplay.setDirections(response);
      					}
      				});	
      				
  					<?php } ?>			
				<?php } ?>
			<?php } ?>
			

			  }
			  function handleNoFlash(errorCode) {
				  if (errorCode == FLASH_UNAVAILABLE) {
					alert("Error: Flash doesn't appear to be supported by your browser");
					return;
				  }
				 }

			
		
		initialize();
			
		});
	jQuery(window).load(function(){
			
		var newHeight = jQuery('#featured-content').height();
		newHeight = newHeight - 5;
		if(newHeight > 300){
			jQuery('#single_map_canvas').height(newHeight);
		}
		
	});

	</script>

<?php
}


/*-----------------------------------------------------------------------------------*/
/* Add custom CSS class to the <body> tag if the lightbox option is enabled. */
/*-----------------------------------------------------------------------------------*/

add_filter( 'body_class', 'woo_add_lightbox_body_class', 10 );

function woo_add_lightbox_body_class ( $classes ) {
	global $woo_options;
	
	if ( isset( $woo_options['woo_enable_lightbox'] ) && $woo_options['woo_enable_lightbox'] == 'true' ) {
		$classes[] = 'has-lightbox';
	}
	
	return $classes;
} // End woo_add_lightbox_body_class()

/*-----------------------------------------------------------------------------------*/
/* Load PrettyPhoto JavaScript and CSS if the lightbox option is enabled. */
/*-----------------------------------------------------------------------------------*/

add_action( 'woothemes_add_javascript', 'woo_load_prettyphoto', 10 );
add_action( 'woothemes_add_css', 'woo_load_prettyphoto', 10 );

function woo_load_prettyphoto () {
	global $woo_options;
	
	if ( ! isset( $woo_options['woo_enable_lightbox'] ) || $woo_options['woo_enable_lightbox'] == 'false' ) { return; }
	if ( is_woocommerce_activated() ) {
		if ( is_product() || is_archive(array('product')) ) return; 
	}
	
	// filter woocommerce

	$filter = current_filter();
	
	switch ( $filter ) {
		case 'woothemes_add_javascript':
			wp_enqueue_script( 'enable-lightbox' );
		break;
		
		case 'woothemes_add_css':
			wp_enqueue_style( 'prettyPhoto' );
		break;
		
		default:
		break;
	}
} // End woo_load_prettyphoto()

/*-----------------------------------------------------------------------------------*/
/* Share bar on single posts. */
/*-----------------------------------------------------------------------------------*/

if( ! function_exists( 'woo_single_post_share_bar' ) ) {
	function woo_single_post_share_bar() {
		global $post;
		
		$post_id = get_the_ID();

		//Get & encode post permalink
		$url = get_permalink( $post_id );
		$encoded_url = urlencode( $url );

		//Get & encode site URL
		$site_url = get_site_url();
		$encoded_site_url = urlencode( $site_url );

		//Get & encode post title
		$title = $post->post_title;
		$encoded_title = urlencode( $title );

		//Get & encode post image
		$image = woo_image( 'link=url&return=true' );
		if( isset( $image ) && strlen( $image ) > 0 ) {
			$encoded_image = urlencode( $image );
		}

		//Get & encode post excerpt
		$excerpt = get_the_excerpt( $post_id );
		$encoded_excerpt = urlencode( $excerpt );

		//Get post type label
		$post_type = get_post_type( $post_id );
		$post_type_obj = get_post_type_object( $post_type );
		$post_type_label = $post_type_obj->labels->singular_name;

		$links = array(
			array( 'name' => __( 'Twitter', 'woothemes' ), 'class' => 'twitter', 'url' => 'https://twitter.com/intent/tweet?original_referer=' . $encoded_site_url . '&text=' . $encoded_title . '&url=' . $encoded_url ),
			array( 'name' => __( 'Facebook', 'woothemes' ), 'class' => 'facebook', 'url' => 'http://www.facebook.com/share.php?u=' . $encoded_url ),
			array( 'name' => __( 'Technorati', 'woothemes' ), 'class' => 'technorati', 'url' => 'http://technorati.com/faves?sub=addfavbtn&add=' . $encoded_url ),
			array( 'name' => __( 'Reddit', 'woothemes' ), 'class' => 'reddit', 'url' => 'http://www.reddit.com/submit?url=' . $encoded_url . '&title=' . $encoded_title ),
			array( 'name' => __( 'Delicious', 'woothemes' ), 'class' => 'delicious', 'url' => 'http://delicious.com/post?url=' . $encoded_url )
		);

		//Pinterest requires an image
		if( isset( $encoded_image ) ) {
			$links[] = array( 'name' => __( 'Pinterest', 'woothemes' ), 'class' => 'pinterest' , 'url' => 'http://pinterest.com/pin/create/button/?url=' . $encoded_url . '&media=' . $encoded_image . '&description=' . $encoded_excerpt );
		}

		$links = (array) apply_filters('woo_single_post_share_bar_links', $links);

		$html = '<div class="post-share-bar">
					<ul>
						<li class="share_title">' . apply_filters( 'woo_single_post_share_bar_title' , sprintf( __( 'Share this %s:', 'woothemes' ) , $post_type_label ) ) . ' </li>' . "\n";
		foreach( $links as $link ) {
			$html .= '<li class="' . esc_attr( $link['class'] ) . '"><a target="_blank" title="' . sprintf( esc_attr__( 'Share on %s' , 'woothemes' ) , $link['name'] ) . '" href="' . esc_url( $link['url'] ) . '">' . $link['name'] . '</a></li>' . "\n";
		}

		$html .= '</ul>
				</div>';

		echo apply_filters( 'woo_single_post_share_bar_output' , $html , $links );
	} // End woo_single_post_share_bar()
}

/*-----------------------------------------------------------------------------------*/
/* Determine the number of columns currently displaying on the homepage */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_get_homepage_column_count' ) ) {
	function woo_get_homepage_column_count () {
		$count = 0;
		foreach ( array( 'homepage-1', 'homepage-2', 'homepage-3' ) as $k => $v ) {
			if ( is_active_sidebar( $v ) ) $count++;
		}

		return intval( apply_filters( 'woo_get_homepage_column_count', $count ) );
	} // End woo_get_homepage_column_count()
}

/*-----------------------------------------------------------------------------------*/
/* Check if WooCommerce is activated */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}
}

/*-----------------------------------------------------------------------------------*/
/* Sticky shortcode */
/*-----------------------------------------------------------------------------------*/
function woo_shortcode_sticky( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'class' => '',
      ), $atts ) );
 
   return '<div class="shortcode-sticky ' . esc_attr($class) . '">' . $content . '</div><!--/shortcode-sticky-->';
}

add_shortcode( 'sticky', 'woo_shortcode_sticky' );

/*-----------------------------------------------------------------------------------*/
/* END */
/*-----------------------------------------------------------------------------------*/
?>