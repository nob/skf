<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Exclude categories from displaying on the "Blog" page template.
- Exclude categories from displaying on the homepage.
- Register WP Menus
- Page navigation
- Post Meta (Modified to reroute for custom post types)
- Post Meta For Custom Post Types
- Subscribe & Connect
- Comment Form Fields
- Comment Form Settings
- Archive Description
- WooPagination markup
- Load Theme Options
- Load custom "Theme Options" CSS on the "Theme Options" screen
- SongKick - Instantiate the WooThemes_SongKick Class (Halts Woo - Events If On)
- Events - Instantiate the WooThemes_Events Class
- Discography - Instantiate the WooThemes_Discography Class
- Photo Albums - Instantiate the WooThemes_Photos Class
- Videos - Instantiate the WooThemes_Videos Class
- Slides - Instantiate the WooThemes_Slides Class
- Band Members - Instantiate the WooThemes_BandMembers Class
- Re-order "Media" so it's below "Band Members" in the Admin Menu
- SoundCloud - Instantiate the WooThemes_SoundCloud Class
- Press Clippings - Instantiate the WooThemes_Press Class
- Add custom CSS class to the <body> tag if the lightbox option is enabled.

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
/* WooTabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'woo_tabs_popular' ) ) {
	function woo_tabs_popular( $posts = 5, $size = 45 ) {
		global $post;
		$popular = get_posts( 'ignore_sticky_posts=1&orderby=comment_count&showposts='.$posts);
		foreach($popular as $post) :
			setup_postdata($post);
	?>
	<li class="fix">
		<?php if ($size <> 0) woo_image( 'height='.$size.'&width='.$size.'&class=thumbnail&single=true' ); ?>
		<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
	</li>
	<?php endforeach;
	}
}


/*-----------------------------------------------------------------------------------*/
/* Post Meta (Modified to reroute for custom post types) */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_post_meta' ) ) {
	function woo_post_meta() {
		if ( ! in_array( get_post_type(), array( 'post', 'page' ) ) ) {
			woo_post_meta_cpt();
		} else {
?>
<aside class="post-meta">
	<ul>
		<li class="post-date">
			<span class="small"><?php _e( 'Posted on', 'woothemes' ) ?></span>
			<?php the_time( get_option( 'date_format' ) ); ?>
		</li>
		<li class="post-author">
			<span class="small"><?php _e( 'by', 'woothemes' ) ?></span>
			<?php the_author_posts_link(); ?>
		</li>
		<li class="post-category">
			<span class="small"><?php _e( 'in', 'woothemes' ) ?></span>
			<?php the_category( ', ') ?>
		</li>
		<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<li class="edit">', '</li>' ); ?>
	</ul>
</aside>
<?php
		}
	}
}

/*-----------------------------------------------------------------------------------*/
/* Post Meta For Custom Post Types */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_post_meta_cpt' ) ) {
	function woo_post_meta_cpt() {
		$meta = get_post_custom( get_the_ID() );
?>
<aside class="post-meta">
	<ul>
<?php
	switch ( get_post_type() ) {
		// Band Members
		case 'band_member':
			if ( isset( $meta['_role'] ) && ( $meta['_role'][0] != '' ) ) {
?>
		<li class="role">
			<?php echo $meta['_role'][0]; ?>
		</li>
<?php
			}
		break;
		
		// Albums
		case 'album':
			if ( isset( $meta['_release_date'] ) && ( $meta['_release_date'][0] != '' ) ) {
?>
		<li class="release-date">
			<span class="small"><?php _e( 'Released on ', 'woothemes' ); ?></span>
			<?php echo date_i18n( get_option( 'date_format' ), $meta['_release_date'][0] ); ?>
		</li>
<?php
			}
			
			if ( isset( $meta['_catalog_id'] ) && ( $meta['_catalog_id'][0] != '' ) ) {
?>
		<li class="catalog-id">
			<?php echo $meta['_catalog_id'][0]; ?>
		</li>
<?php
			}
			
			$terms = get_the_terms( get_the_ID(), 'album_category' );
			if ( ! is_wp_error( $terms ) && ( $terms != false ) ) {
				$term_links = array();
				foreach ( $terms as $t ) {
					$term_links[] = '<a href="' . get_term_link( $t, 'album_category' ) . '">' . $t->name . '</a>';
				}
?>
		<li class="category">
			<span class="small"><?php _e( 'Listed under', 'woothemes' ); ?></span> 
			<?php echo join( ', ', $term_links ); ?>
		</li>
<?php
			}
		break;
		
		// Videos
		case 'video':
			$terms = get_the_terms( get_the_ID(), 'video_category' );
			if ( ! is_wp_error( $terms ) && ( $terms != false ) ) {
				$term_links = array();
				foreach ( $terms as $t ) {
					$term_links[] = '<a href="' . get_term_link( $t, 'video_category' ) . '">' . $t->name . '</a>';
				}
?>
		<li class="category">
			<span class="small"><?php _e( 'Posted in', 'woothemes' ); ?></span> 
			<?php echo join( ', ', $term_links ); ?>
		</li>
<?php
			}
		break;
		
		// Photo Galleries
		case 'gallery':
			$terms = get_the_terms( get_the_ID(), 'gallery_category' );
			if ( ! is_wp_error( $terms ) && ( $terms != false ) ) {
				$term_links = array();
				foreach ( $terms as $t ) {
					$term_links[] = '<a href="' . get_term_link( $t, 'gallery_category' ) . '">' . $t->name . '</a>';
				}
?>
		<li class="category">
			<span class="small"><?php _e( 'Posted in', 'woothemes' ); ?></span> 
			<?php echo join( ', ', $term_links ); ?>
		</li>
<?php
			}
		break;
		
		// Press Clippings
		case 'press':
		if ( isset( $meta['_publication_date'] ) && ( $meta['_publication_date'][0] != '' ) ) {
?>
		<li class="release-date">
			<span class="small"><?php _e( 'Published on ', 'woothemes' ); ?></span>
			<?php echo date_i18n( get_option( 'date_format' ), $meta['_publication_date'][0] ); ?>
			<?php
				if ( isset( $meta['_media_portal'] ) && ( $meta['_media_portal'][0] != '' ) ) {
					echo ' ' . __( 'by', 'woothemes' ) . ' ' . esc_attr( $meta['_media_portal'][0] ) . '.';
				} else {
					echo '.'; // Just a full-stop, to end the sentence.
				}
			?>
		</li>
<?php
			}
			$terms = get_the_terms( get_the_ID(), 'press_category' );
			if ( ! is_wp_error( $terms ) && ( $terms != false ) ) {
				$term_links = array();
				foreach ( $terms as $t ) {
					$term_links[] = '<a href="' . get_term_link( $t, 'press_category' ) . '">' . $t->name . '</a>';
				}
?>
		<li class="category">
			<span class="small"><?php _e( 'Posted in', 'woothemes' ); ?></span> 
			<?php echo join( ', ', $term_links ); ?>
		</li>
<?php
			}
		break;
		
		// Products
		case 'product':
			$terms = get_the_terms( get_the_ID(), 'product_cat' );
			if ( ! is_wp_error( $terms ) && ( $terms != false ) ) {
				$term_links = array();
				foreach ( $terms as $t ) {
					$term_links[] = '<a href="' . get_term_link( $t, 'product_cat' ) . '">' . $t->name . '</a>';
				}
?>
		<li class="category">
			<span class="small"><?php _e( 'Listed in', 'woothemes' ); ?></span> 
			<?php echo join( ', ', $term_links ); ?>
		</li>
<?php
			}
		break;
		
		// Events
		case 'event':
			$terms = get_the_terms( get_the_ID(), 'event_category' );
			if ( ! is_wp_error( $terms ) && ( $terms != false ) ) {
				$term_links = array();
				foreach ( $terms as $t ) {
					$term_links[] = '<a href="' . get_term_link( $t, 'event_category' ) . '">' . $t->name . '</a>';
				}
?>
		<li class="category">
			<span class="small"><?php _e( 'Listed in', 'woothemes' ); ?></span> 
			<?php echo join( ', ', $term_links ); ?>
		</li>
<?php
			}
			
			if ( isset( $meta['_event_start'] ) && ( $meta['_event_start'][0] != '' ) ) {
?>
		<li class="start-date">
			<?php echo date_i18n( get_option( 'date_format' ), $meta['_event_start'][0] ) . ' ' . __( '@', 'woothemes' ) . ' ' . date_i18n( get_option( 'time_format' ), $meta['_event_start'][0] ); ?>
			<?php echo ' ' . __( 'to', 'woothemes' ) . ' '; ?>
			<?php echo date_i18n( get_option( 'date_format' ), $meta['_event_end'][0] ) . ' ' . __( '@', 'woothemes' ) . ' ' . date_i18n( get_option( 'time_format' ), $meta['_event_end'][0] ); ?>
		</li>
<?php
			}
			
			if ( isset( $meta['_event_venue'] ) && ( $meta['_event_venue'][0] != '' ) ) {
?>
		<li class="event-venue">
			<?php echo __( 'at', 'woothemes' ) . ' ' . $meta['_event_venue'][0]; ?>
		</li>
<?php
			}
		break;
	}
?>
		<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<li class="edit">', '</li>' ); ?>
	</ul>
</aside>
<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* Subscribe / Connect */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_subscribe_connect')) {
	function woo_subscribe_connect($widget = 'false', $title = '', $form = '', $social = '') {

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
	<aside id="connect" class="widget">
		<h3><?php if ( $title ) echo apply_filters( 'widget_title', $title ); else _e('Subscribe','woothemes'); ?></h3>

		<div class="container">
		
		<div <?php if ( $related_posts != '' ) echo 'class="col-left"'; ?>>
			<p><?php if ($settings[ 'connect_content' ] != '') echo stripslashes($settings[ 'connect_content' ]); ?></p>

			<?php if ( $settings[ 'connect_newsletter_id' ] != "" AND $form != 'on' ) : ?>
			<form class="newsletter-form" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open( 'http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $settings[ 'connect_newsletter_id' ]; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520' );return true">
				<input class="email" type="text" name="email" value="<?php esc_attr_e( 'E-mail', 'woothemes' ); ?>" onfocus="if (this.value == '<?php _e( 'E-mail', 'woothemes' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'E-mail', 'woothemes' ); ?>';}" />
				<input type="hidden" value="<?php echo $settings[ 'connect_newsletter_id' ]; ?>" name="uri"/>
				<input type="hidden" value="<?php bloginfo( 'name' ); ?>" name="title"/>
				<input type="hidden" name="loc" value="en_US"/>
				<input class="submit" type="submit" name="submit" value="<?php _e( 'Submit', 'woothemes' ); ?>" />
			</form>
			<?php endif; ?>

			<?php if ( $settings['connect_mailchimp_list_url'] != "" AND $form != 'on' AND $settings['connect_newsletter_id'] == "" ) : ?>
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
			<div class="social">
		   		<?php if ( $settings['connect_rss' ] == "true" ) { ?>
		   		<li><a href="<?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" title="RSS"><?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?></a></li>

		   		<?php } if ( $settings['connect_twitter' ] != "" ) { ?>
		   		<li><a href="<?php echo esc_url( $settings['connect_twitter'] ); ?>" class="twitter" title="Twitter"><?php echo esc_url( $settings['connect_twitter'] ); ?></a></li>

		   		<?php } if ( $settings['connect_facebook' ] != "" ) { ?>
		   		<li><a href="<?php echo esc_url( $settings['connect_facebook'] ); ?>" class="facebook" title="Facebook"><?php echo esc_url( $settings['connect_facebook'] ); ?></a></li>

		   		<?php } if ( $settings['connect_youtube' ] != "" ) { ?>
		   		<li><a href="<?php echo esc_url( $settings['connect_youtube'] ); ?>" class="youtube" title="YouTube"><?php echo esc_url( $settings['connect_youtube'] ); ?></a></li>

		   		<?php } if ( $settings['connect_flickr' ] != "" ) { ?>
		   		<li><a href="<?php echo esc_url( $settings['connect_flickr'] ); ?>" class="flickr" title="Flickr"><?php echo esc_url( $settings['connect_flickr'] ); ?></a></li>

		   		<?php } if ( $settings['connect_linkedin' ] != "" ) { ?>
		   		<li><a href="<?php echo esc_url( $settings['connect_linkedin'] ); ?>" class="linkedin" title="LinkedIn"><?php echo esc_url( $settings['connect_linkedin'] ); ?></a></li>

		   		<?php } if ( $settings['connect_delicious' ] != "" ) { ?>
		   		<li><a href="<?php echo esc_url( $settings['connect_delicious'] ); ?>" class="delicious" title="Delicious"><?php echo esc_url( $settings['connect_delicious'] ); ?></a></li>

		   		<?php } if ( $settings['connect_googleplus' ] != "" ) { ?>
		   		<li><a href="<?php echo esc_url( $settings['connect_googleplus'] ); ?>" class="googleplus" title="Google+"><?php echo esc_url( $settings['connect_googleplus'] ); ?></a></li>

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
		<div class="fix"></div>
		</div><!-- .container -->
		
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
							'<label for="author">' . __( 'Name', 'woothemes' ) . ( $req ? $required_text : '' ) . '</label> ' .
							'</p>',
				'email'  => '<p class="comment-form-email">' .
				            '<input id="email" class="txt" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' .
				            '<label for="email">' . __( 'Email', 'woothemes' ) . ( $req ? $required_text : '' ) . '</label> ' .
				            '</p>',
				'url'    => '<p class="comment-form-url">' .
				            '<input id="url" class="txt" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
				            '<label for="url">' . __( 'Website', 'woothemes' ) . '</label>' .
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
/* Load Theme Options */
/*-----------------------------------------------------------------------------------*/

$woo_options = get_option( 'woo_options' );

/*-----------------------------------------------------------------------------------*/
/* Load custom "Theme Options" CSS on the "Theme Options" screen */
/*-----------------------------------------------------------------------------------*/

add_action( 'admin_print_styles', 'woo_load_custom_theme_options_css', 10 );

/**
 * woo_load_custom_theme_options_css function.
 * 
 * @access public
 * @return void
 */
if ( ! function_exists( 'woo_load_custom_theme_options_css' ) ) {
	function woo_load_custom_theme_options_css () {
		global $pagenow;
		
		wp_register_style( 'woo-custom-theme-options', get_template_directory_uri() . '/includes/css/woo-custom-theme-options.css' );
		
		if ( ( $pagenow == 'admin.php' ) && ( isset( $_GET['page'] ) ) && ( $_GET['page'] == 'woothemes' ) ) {
			wp_enqueue_style( 'woo-custom-theme-options' );
		}
	} // End woo_load_custom_theme_options_css()
}

/*-----------------------------------------------------------------------------------*/
/* SongKick - Instantiate the WooThemes_SongKick Class (Halts Woo - Events If On) */
/*-----------------------------------------------------------------------------------*/

$has_songkick = false;

if ( ! isset( $woo_options['woo_songkick_artist_id'] ) || ( isset( $woo_options['woo_songkick_artist_id'] ) && ( apply_filters( 'woo_songkick_artist_id', $woo_options['woo_songkick_artist_id'] ) != '' ) ) ) {
	locate_template( 'includes/woo-songkick/woo-songkick.php', true, true );
	
	if ( class_exists( 'WooThemes_SongKick' ) ) {
		global $woothemes_songkick;
		
		$settings = array();
		
		$woothemes_songkick = new WooThemes_SongKick( $woo_options['woo_songkick_artist_id'], $settings );
		$woothemes_songkick->init();
		
		$has_songkick = true;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Events - Instantiate the WooThemes_Events Class */
/*-----------------------------------------------------------------------------------*/

if ( ( $has_songkick == false ) && ( ! isset( $woo_options['woo_enable_events'] ) || ( isset( $woo_options['woo_enable_events'] ) && ( apply_filters( 'woo_enable_events', $woo_options['woo_enable_events'] ) != 'false' ) ) ) ) {
	locate_template( 'includes/woo-events/woo-events.php', true, true );
	
	if ( class_exists( 'WooThemes_Events' ) ) {
		global $woothemes_events;
		$woothemes_events = new WooThemes_Events;
		$woothemes_events->init();
	}
}

/*-----------------------------------------------------------------------------------*/
/* Discography - Instantiate the WooThemes_Discography Class */
/*-----------------------------------------------------------------------------------*/

if ( ! isset( $woo_options['woo_enable_discography'] ) || ( isset( $woo_options['woo_enable_discography'] ) && ( apply_filters( 'woo_enable_discography', $woo_options['woo_enable_discography'] ) != 'false' ) ) ) {
	locate_template( 'includes/woo-discography/woo-discography.php', true, true );
	
	if ( class_exists( 'WooThemes_Discography' ) ) {
		global $woothemes_discography;
		$woothemes_discography = new WooThemes_Discography;
		$woothemes_discography->init();
	}
}

/*-----------------------------------------------------------------------------------*/
/* Photo Albums - Instantiate the WooThemes_Photos Class */
/*-----------------------------------------------------------------------------------*/

if ( ! isset( $woo_options['woo_enable_photos'] ) || ( isset( $woo_options['woo_enable_photo_albums'] ) && ( apply_filters( 'woo_enable_photo_albums', $woo_options['woo_enable_photo_albums'] ) != 'false' ) ) ) {
	locate_template( 'includes/woo-photos/woo-photos.php', true, true );
	
	if ( class_exists( 'WooThemes_Photos' ) ) {
		global $woothemes_photos;
		$woothemes_photos = new WooThemes_Photos;
		$woothemes_photos->init();
	}
}

/*-----------------------------------------------------------------------------------*/
/* Videos - Instantiate the WooThemes_Videos Class */
/*-----------------------------------------------------------------------------------*/

if ( ! isset( $woo_options['woo_enable_videos'] ) || ( isset( $woo_options['woo_enable_videos'] ) && ( apply_filters( 'woo_enable_videos', $woo_options['woo_enable_videos'] ) != 'false' ) ) ) {
	locate_template( 'includes/woo-videos/woo-videos.php', true, true );
	
	if ( class_exists( 'WooThemes_Videos' ) ) {
		global $woothemes_videos;
		$woothemes_videos = new WooThemes_Videos;
		$woothemes_videos->init();
	}
}

/*-----------------------------------------------------------------------------------*/
/* Slides - Instantiate the WooThemes_Slides Class */
/*-----------------------------------------------------------------------------------*/

if ( ! isset( $woo_options['woo_enable_slides'] ) || ( isset( $woo_options['woo_enable_slides'] ) && ( apply_filters( 'woo_enable_slides', $woo_options['woo_enable_slides'] ) != 'false' ) ) ) {
	locate_template( 'includes/woo-slides/woo-slides.php', true, true );
	
	if ( class_exists( 'WooThemes_Slides' ) ) {
		global $woothemes_slides;
		$woothemes_slides = new WooThemes_Slides;
		$woothemes_slides->init();
	}
}

/*-----------------------------------------------------------------------------------*/
/* Band Members - Instantiate the WooThemes_BandMembers Class */
/*-----------------------------------------------------------------------------------*/

if ( ! isset( $woo_options['woo_enable_bandmembers'] ) || ( isset( $woo_options['woo_enable_bandmembers'] ) && ( apply_filters( 'woo_enable_bandmembers', $woo_options['woo_enable_bandmembers'] ) != 'false' ) ) ) {
	locate_template( 'includes/woo-bandmembers/woo-bandmembers.php', true, true );
	
	if ( class_exists( 'WooThemes_BandMembers' ) ) {
		global $woothemes_bandmembers;
		$woothemes_bandmembers = new WooThemes_BandMembers;
		$woothemes_bandmembers->init();
	}
}

/*-----------------------------------------------------------------------------------*/
/* SoundCloud - Instantiate the WooThemes_SoundCloud Class */
/*-----------------------------------------------------------------------------------*/

if ( ! isset( $woo_options['woo_soundcloud_username'] ) || ( isset( $woo_options['woo_soundcloud_username'] ) && ( apply_filters( 'woo_soundcloud_username', $woo_options['woo_soundcloud_username'] ) != '' ) ) ) {
	locate_template( 'includes/woo-soundcloud/woo-soundcloud.php', true, true );
	
	if ( class_exists( 'WooThemes_SoundCloud' ) ) {
		global $woothemes_soundcloud;
		if ( isset( $woo_options['woo_soundcloud_colour'] ) ) {
			$settings = array( 'colour' => $woo_options['woo_soundcloud_colour'] );
		}
		
		$woothemes_soundcloud = new WooThemes_SoundCloud( $woo_options['woo_soundcloud_username'], $settings );
		$woothemes_soundcloud->init();
	}
}

/*-----------------------------------------------------------------------------------*/
/* Press Clippings - Instantiate the WooThemes_Press Class */
/*-----------------------------------------------------------------------------------*/

if ( ! isset( $woo_options['woo_enable_press'] ) || ( isset( $woo_options['woo_enable_press'] ) && ( apply_filters( 'woo_enable_press', $woo_options['woo_enable_press'] ) != 'false' ) ) ) {
	locate_template( 'includes/woo-press/woo-press.php', true, true );
	
	if ( class_exists( 'WooThemes_Press' ) ) {
		global $woothemes_press;
		$woothemes_press = new WooThemes_Press;
		$woothemes_press->init();
	}
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
/* END */
/*-----------------------------------------------------------------------------------*/
?>