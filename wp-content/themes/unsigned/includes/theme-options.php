<?php
if ( !function_exists( 'woo_options' ) ) {
	function woo_options() {

		// THEME VARIABLES
		$themename = "Unsigned";
		$themeslug = "unsigned";

		// STANDARD VARIABLES. DO NOT TOUCH!
		$shortname = "woo";
		$manualurl = 'http://www.woothemes.com/support/theme-documentation/'.$themeslug.'/';

		//Access the WordPress Categories via an Array
		$woo_categories = array();
		$woo_categories_obj = get_categories( 'hide_empty=0' );
		foreach ( $woo_categories_obj as $woo_cat ) {
			$woo_categories[$woo_cat->cat_ID] = $woo_cat->cat_name;}
		$categories_tmp = array_unshift( $woo_categories, "Select a category:" );

		//Access the WordPress Pages via an Array
		$woo_pages = array();
		$woo_pages_obj = get_pages( 'sort_column=post_parent,menu_order' );
		foreach ( $woo_pages_obj as $woo_page ) {
			$woo_pages[$woo_page->ID] = $woo_page->post_name; }
		$woo_pages_tmp = array_unshift( $woo_pages, "Select a page:" );

		//Stylesheets Reader
		$alt_stylesheet_path = get_template_directory() . '/styles/';
		$alt_stylesheets = array();
		if ( is_dir( $alt_stylesheet_path ) ) {
			if ( $alt_stylesheet_dir = opendir( $alt_stylesheet_path ) ) {
				while ( ( $alt_stylesheet_file = readdir( $alt_stylesheet_dir ) ) !== false ) {
					if( stristr( $alt_stylesheet_file, ".css" ) !== false ) {
						$alt_stylesheets[] = $alt_stylesheet_file;
					}
				}
			}
		}

		// Setup an array of slide-page terms for a dropdown.
		$args = array( 'echo' => 0, 'hierarchical' => 1, 'taxonomy' => 'slide-page' );
		$cats_dropdown = wp_dropdown_categories( $args );
		$cats = array();

		// Quick string hack to make sure we get the pages with the indents.
		$cats_dropdown = str_replace( "<select name='cat' id='cat' class='postform' >", '', $cats_dropdown );
		$cats_dropdown = str_replace( '</select>', '', $cats_dropdown );
		$cats_split = explode( '</option>', $cats_dropdown );

		$cats[] = __( 'Select a Slide Group:', 'woothemes' );

		foreach ( $cats_split as $k => $v ) {   
		    $id = '';   
		    // Get the ID value.
		    preg_match( '/value="(.*?)"/i', $v, $matches );
		    
		    if ( isset( $matches[1] ) ) {   
		        $id = $matches[1];
		        $cats[$id] = trim( strip_tags( $v ) );
		    }
		}

		$slide_groups = $cats;

		//More Options
		$other_entries = array( "Select a number:", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19" );

		// THIS IS THE DIFFERENT FIELDS
		$options = array();

		// General

		$options[] = array( 'name' => __( 'General Settings', 'woothemes' ),
			'type' => 'heading',
			'icon' => "general" );

		$options[] = array( 'name' => __( 'Theme Stylesheet', 'woothemes' ),
			'desc' => __( 'Select your themes alternative color scheme.', 'woothemes' ),
			'id' => $shortname."_alt_stylesheet",
			'std' => "default.css",
			'type' => 'select',
			'options' => $alt_stylesheets );

		$options[] = array( 'name' => __( 'Custom Logo', 'woothemes' ),
			'desc' => __( 'Upload a logo for your theme, or specify an image URL directly.', 'woothemes' ),
			'id' => $shortname."_logo",
			'std' => '',
			'type' => 'upload' );

		$options[] = array( 'name' => __( 'Text Title', 'woothemes' ),
			'desc' => sprintf( __( 'Enable text-based Site Title and Tagline. Setup title & tagline in %1$s.', 'woothemes' ), '<a href="' . home_url() . '/wp-admin/options-general.php">' . __( 'General Settings', 'woothemes' ) . '</a>' ),
			'id' => $shortname."_texttitle",
			'std' => 'false',
			"class" => "collapsed",
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Site Title', 'woothemes' ),
			'desc' => __( 'Change the site title typography.', 'woothemes' ),
			'id' => $shortname."_font_site_title",
			'std' => array( 'size' => '35', 'unit' => 'px', 'face' => 'Kreon', 'style' => 'normal', 'color' => '#3E3E3E' ),
			"class" => "hidden",
			'type' => 'typography' );

		$options[] = array( 'name' => __( 'Site Description', 'woothemes' ),
			'desc' => __( 'Enable the site description/tagline under site title.', 'woothemes' ),
			'id' => $shortname."_tagline",
			"class" => "hidden",
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Site Description', 'woothemes' ),
			'desc' => __( 'Change the site description typography.', 'woothemes' ),
			'id' => $shortname."_font_tagline",
			'std' => array( 'size' => '12', 'unit' => 'px', 'face' => 'Kreon', 'style' => 'italic', 'color' => '#3E3E3E' ),
			"class" => "hidden last",
			'type' => 'typography' );

		$options[] = array( 'name' => __( 'Custom Favicon', 'woothemes' ),
			'desc' => __( 'Upload a 16px x 16px <a href="http://www.faviconr.com/">ico image</a> that will represent your website\'s favicon.', 'woothemes' ),
			'id' => $shortname."_custom_favicon",
			'std' => '',
			'type' => 'upload' );

		$options[] = array( 'name' => __( 'Tracking Code', 'woothemes' ),
			'desc' => __( 'Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.', 'woothemes' ),
			'id' => $shortname."_google_analytics",
			'std' => '',
			'type' => 'textarea' );

		$options[] = array( 'name' => __( 'RSS URL', 'woothemes' ),
			'desc' => __( 'Enter your preferred RSS URL. (Feedburner or other)', 'woothemes' ),
			'id' => $shortname."_feed_url",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'E-Mail Subscription URL', 'woothemes' ),
			'desc' => __( 'Enter your preferred E-mail subscription URL. (Feedburner or other)', 'woothemes' ),
			'id' => $shortname."_subscribe_email",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Contact Form E-Mail', 'woothemes' ),
			'desc' => __( 'Enter your E-mail address to use on the Contact Form Page Template. Add the contact form by adding a new page and selecting "Contact Form" as page template.', 'woothemes' ),
			'id' => $shortname."_contactform_email",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Custom CSS', 'woothemes' ),
			'desc' => __( 'Quickly add some CSS to your theme by adding it to this block.', 'woothemes' ),
			'id' => $shortname."_custom_css",
			'std' => '',
			'type' => 'textarea' );

		$options[] = array( 'name' => __( 'Post/Page Comments', 'woothemes' ),
			'desc' => __( 'Select if you want to enable/disable comments on posts and/or pages.', 'woothemes' ),
			'id' => $shortname."_comments",
			'std' => "both",
			'type' => 'select2',
			'options' => array( "post" => __( 'Posts Only', 'woothemes' ), "page" => __( 'Pages Only', 'woothemes' ), "both" => __( 'Pages / Posts', 'woothemes' ), "none" => __( 'None', 'woothemes' ) ) );

		$options[] = array( 'name' => __( 'Post Content', 'woothemes' ),
			'desc' => __( 'Select if you want to show the full content or the excerpt on posts.', 'woothemes' ),
			'id' => $shortname."_post_content",
			'type' => 'select2',
			'options' => array( "excerpt" => __( 'The Excerpt', 'woothemes' ), "content" => __( 'Full Content', 'woothemes' ) ) );

		$options[] = array( 'name' => __( 'Post Author Box', 'woothemes' ),
			'desc' => sprintf( __( 'This will enable the post author box on the single posts page. Edit description in %1$s.', 'woothemes' ), '<a href="' . home_url() . '/wp-admin/profile.php">' . __( 'Profile', 'woothemes' ) . '</a>' ),
			'id' => $shortname."_post_author",
			'std' => 'true',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Display Breadcrumbs', 'woothemes' ),
			'desc' => __( 'Display dynamic breadcrumbs on each page of your website.', 'woothemes' ),
			'id' => $shortname."_breadcrumbs_show",
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Display Pagination', 'woothemes' ),
			'desc' => __( 'Display pagination on the blog.', 'woothemes' ),
			'id' => $shortname."_pagenav_show",
			'std' => 'true',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Pagination Style', 'woothemes' ),
			'desc' => __( 'Select the style of pagination you would like to use on the blog.', 'woothemes' ),
			'id' => $shortname."_pagination_type",
			'type' => 'select2',
			'options' => array( "paginated_links" => __( 'Numbers', 'woothemes' ), "simple" => __( 'Next/Previous', 'woothemes' ) ) );

		// Styling
		$options[] = array( 'name' => __( 'Styling Options', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'styling' );

		$options[] = array( 'name' => __( 'Background', 'woothemes' ),
			'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Body Background Color', 'woothemes' ),
			'desc' => __( 'Pick a custom color for background color of the theme e.g. #697e09', 'woothemes' ),
			'id' => "woo_body_color",
			'std' => '',
			'type' => 'color' );

		$options[] = array( 'name' => __( 'Body background image', 'woothemes' ),
			'desc' => __( 'Upload an image for the theme\'s background', 'woothemes' ),
			'id' => $shortname."_body_img",
			'std' => '',
			'type' => 'upload' );

		$options[] = array( 'name' => __( 'Background image repeat', 'woothemes' ),
			'desc' => __( 'Select how you would like to repeat the background-image', 'woothemes' ),
			'id' => $shortname."_body_repeat",
			'std' => "no-repeat",
			'type' => 'select',
			'options' => array( "no-repeat", "repeat-x", "repeat-y", "repeat" ) );

		$options[] = array( 'name' => __( 'Background image position', 'woothemes' ),
			'desc' => __( 'Select how you would like to position the background', 'woothemes' ),
			'id' => $shortname."_body_pos",
			'std' => "top",
			'type' => 'select',
			'options' => array( "top left", "top center", "top right", "center left", "center center", "center right", "bottom left", "bottom center", "bottom right" ) );

		$options[] = array( 'name' => __( 'Links', 'woothemes' ),
			'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Link Color', 'woothemes' ),
			'desc' => __( 'Pick a custom color for links or add a hex color code e.g. #697e09', 'woothemes' ),
			'id' => "woo_link_color",
			'std' => '',
			'type' => 'color' );

		$options[] = array( 'name' =>  __( 'Link Hover Color', 'woothemes' ),
			'desc' => __( 'Pick a custom color for links hover or add a hex color code e.g. #697e09', 'woothemes' ),
			'id' => "woo_link_hover_color",
			'std' => '',
			'type' => 'color' );

		$options[] = array( 'name' =>  __( 'Button Color', 'woothemes' ),
			'desc' => __( 'Pick a custom color for buttons or add a hex color code e.g. #697e09', 'woothemes' ),
			'id' => "woo_button_color",
			'std' => '',
			'type' => 'color' );

		/* Custom Block Transparency Settings */
		
		$options[] = array( 'name' => __( 'Transparency', 'woothemes' ),
			'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Block Transparency', 'woothemes' ),
			'desc' => __( 'Enter your preferred transparency settings (this will be a percentage between 0 and 100) for the various transparent sections of your website.', 'woothemes' ),
			'id' => $shortname . '_transparency',
			'std' => '70',
			'type' => 'text' );

		/* Typography */

		$options[] = array( 'name' => __( 'Typography', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'typography' );

		$options[] = array( 'name' => __( 'Enable Custom Typography', 'woothemes' ) ,
			'desc' => __( 'Enable the use of custom typography for your site. Custom styling will be output in your sites HEAD.', 'woothemes' ) ,
			'id' => $shortname."_typography",
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'General Typography', 'woothemes' ) ,
			'desc' => __( 'Change the general font.', 'woothemes' ) ,
			'id' => $shortname."_font_body",
			'std' => array( 'size' => '12', 'unit' => 'px', 'face' => 'Droid Serif', 'style' => '', 'color' => '#878484' ),
			'type' => 'typography' );

		$options[] = array( 'name' => __( 'Navigation', 'woothemes' ) ,
			'desc' => __( 'Change the navigation font.', 'woothemes' ),
			'id' => $shortname."_font_nav",
			'std' => array( 'size' => '1.3', 'unit' => 'em', 'face' => 'Kreon', 'style' => '', 'color' => '#B2B0B0' ),
			'type' => 'typography' );

		$options[] = array( 'name' => __( 'Page Title', 'woothemes' ) ,
			'desc' => __( 'Change the page title.', 'woothemes' ) ,
			'id' => $shortname."_font_page_title",
			'std' => array( 'size' => '2', 'unit' => 'em', 'face' => 'Kreon', 'style' => '', 'color' => '#FFFFFF' ),
			'type' => 'typography' );

		$options[] = array( 'name' => __( 'Post Title', 'woothemes' ) ,
			'desc' => __( 'Change the post title.', 'woothemes' ) ,
			'id' => $shortname."_font_post_title",
			'std' => array( 'size' => '2', 'unit' => 'em', 'face' => 'Kreon', 'style' => 'bold', 'color' => '#FFFFFF' ),
			'type' => 'typography' );

		$options[] = array( 'name' => __( 'Post Meta', 'woothemes' ),
			'desc' => __( 'Change the post meta.', 'woothemes' ) ,
			'id' => $shortname."_font_post_meta",
			'std' => array( 'size' => '12', 'unit' => 'px', 'face' => 'Droid Serif', 'style' => '', 'color' => '#878484' ),
			'type' => 'typography' );

		$options[] = array( 'name' => __( 'Post Entry', 'woothemes' ) ,
			'desc' => __( 'Change the post entry.', 'woothemes' ) ,
			'id' => $shortname."_font_post_entry",
			'std' => array( 'size' => '12', 'unit' => 'px', 'face' => 'Droid Serif', 'style' => '', 'color' => '#878484' ),
			'type' => 'typography' );

		$options[] = array( 'name' => __( 'Widget Titles', 'woothemes' ) ,
			'desc' => __( 'Change the widget titles.', 'woothemes' ) ,
			'id' => $shortname."_font_widget_titles",
			'std' => array( 'size' => '2', 'unit' => 'em', 'face' => 'Kreon', 'style' => '', 'color' => '#FFFFFF' ),
			'type' => 'typography' );

		/* Layout */

		$options[] = array( 'name' => __( 'Layout Options', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'layout' );

		$url =  get_template_directory_uri() . '/functions/images/';
		$options[] = array( 'name' => __( 'Main Layout', 'woothemes' ),
			'desc' => __( 'Select which layout you want for your site.', 'woothemes' ),
			'id' => $shortname."_site_layout",
			'std' => "layout-right-content",
			'type' => 'images',
			'options' => array(
				'layout-left-content' => $url . '2cl.png',
				'layout-right-content' => $url . '2cr.png' )
		);

		$options[] = array( 'name' => __( 'Category Exclude - Homepage', 'woothemes' ),
			'desc' => __( 'Specify a comma seperated list of category IDs or slugs that you\'d like to exclude from your homepage (eg: uncategorized).', 'woothemes' ),
			'id' => $shortname."_exclude_cats_home",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Category Exclude - Blog Page Template', 'woothemes' ),
			'desc' => __( 'Specify a comma seperated list of category IDs or slugs that you\'d like to exclude from your \'Blog\' page template (eg: uncategorized).', 'woothemes' ),
			'id' => $shortname."_exclude_cats_blog",
			'std' => '',
			'type' => 'text' );

		/* Dynamic Images */
		$options[] = array( 'name' => __( 'Dynamic Images', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'image' );

		$options[] = array( 'name' => __( 'Resizer Settings', 'woothemes' ),
			'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Dynamic Image Resizing', 'woothemes' ),
			'desc' => '',
			'id' => $shortname."_wpthumb_notice",
			'std' => __( 'There are two alternative methods of dynamically resizing the thumbnails in the theme, <strong>WP Post Thumbnail</strong> or <strong>TimThumb - Custom Settings panel</strong>. We recommend using WP Post Thumbnail option.', 'woothemes' ),
			'type' => "info" );

		$options[] = array( 'name' => __( 'WP Post Thumbnail', 'woothemes' ),
			'desc' => __( 'Use WordPress post thumbnail to assign a post thumbnail. Will enable the <strong>Featured Image panel</strong> in your post sidebar where you can assign a post thumbnail.', 'woothemes' ),
			'id' => $shortname."_post_image_support",
			'std' => 'true',
			"class" => "collapsed",
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'WP Post Thumbnail - Dynamic Image Resizing', 'woothemes' ),
			'desc' => __( 'The post thumbnail will be dynamically resized using native WP resize functionality. <em>(Requires PHP 5.2+)</em>', 'woothemes' ),
			'id' => $shortname."_pis_resize",
			'std' => 'true',
			"class" => "hidden",
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'WP Post Thumbnail - Hard Crop', 'woothemes' ),
			'desc' => __( 'The post thumbnail will be cropped to match the target aspect ratio (only used if "Dynamic Image Resizing" is enabled).', 'woothemes' ),
			'id' => $shortname."_pis_hard_crop",
			'std' => 'true',
			"class" => "hidden last",
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'TimThumb - Custom Settings Panel', 'woothemes' ),
			'desc' => sprintf( __( 'This will enable the %1$s (thumb.php) script which dynamically resizes images added through the <strong>custom settings panel below the post</strong>. Make sure your themes <em>cache</em> folder is writable. %2$s', 'woothemes' ), '<a href="http://code.google.com/p/timthumb/">TimThumb</a>', '<a href="http://www.woothemes.com/2008/10/troubleshooting-image-resizer-thumbphp/">Need help?</a>' ),
			'id' => $shortname."_resize",
			'std' => 'true',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Automatic Image Thumbnail', 'woothemes' ),
			'desc' => __( 'If no thumbnail is specifified then the first uploaded image in the post is used.', 'woothemes' ),
			'id' => $shortname."_auto_img",
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Thumbnail Settings', 'woothemes' ),
			'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Thumbnail Image Dimensions', 'woothemes' ),
			'desc' => __( 'Enter an integer value i.e. 250 for the desired size which will be used when dynamically creating the images.', 'woothemes' ),
			'id' => $shortname . '_image_dimensions',
			'std' => '',
			'type' => array(
				array(  'id' => $shortname . '_thumb_w',
					'type' => 'text',
					'std' => 100,
					'meta' => __( 'Width', 'woothemes' ) ),
				array(  'id' => $shortname . '_thumb_h',
					'type' => 'text',
					'std' => 100,
					'meta' => __( 'Height', 'woothemes' ) )
			) );

		$options[] = array( 'name' => __( 'Thumbnail Alignment', 'woothemes' ),
			'desc' => __( 'Select how to align your thumbnails with posts.', 'woothemes' ),
			'id' => $shortname . '_thumb_align',
			'std' => 'alignleft',
			'type' => 'select2',
			'options' => array( 'alignleft' => __( 'Left', 'woothemes' ), 'alignright' => __( 'Right', 'woothemes' ), 'aligncenter' => __( 'Center', 'woothemes' ) ) );

		$options[] = array( 'name' => 'Single Post - Show Thumbnail',
			'desc' => __( 'Show the thumbnail in the single post page.', 'woothemes' ),
			'id' => $shortname . '_thumb_single',
			"class" => 'collapsed',
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Single Post - Thumbnail Dimensions', 'woothemes' ),
			'desc' => __( 'Enter an integer value i.e. 250 for the image size. Max width is 576.', 'woothemes' ),
			'id' => $shortname . '_image_dimensions',
			'std' => '',
			"class" => 'hidden last',
			'type' => array(
				array(  'id' => $shortname . '_single_w',
					'type' => 'text',
					'std' => 200,
					'meta' => __( 'Width', 'woothemes' ) ),
				array(  'id' => $shortname . '_single_h',
					'type' => 'text',
					'std' => 200,
					'meta' => __( 'Height', 'woothemes' ) )
			) );

		$options[] = array( 'name' => __( 'Single Post - Thumbnail Alignment', 'woothemes' ),
			'desc' => __( 'Select how to align your thumbnail with single posts.', 'woothemes' ),
			'id' => $shortname . '_thumb_single_align',
			'std' => 'alignright',
			'type' => 'select2',
			"class" => 'hidden',
			'options' => array( 'alignleft' => __( 'Left', 'woothemes' ), 'alignright' => __( 'Right', 'woothemes' ), 'aligncenter' => __( 'Center', 'woothemes' ) ) );

		$options[] = array( 'name' => __( 'Add thumbnail to RSS feed', 'woothemes' ),
			'desc' => __( 'Add the the image uploaded via your Custom Settings panel to your RSS feed', 'woothemes' ),
			'id' => $shortname . '_rss_thumb',
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Enable Lightbox', 'woothemes' ),
					'desc' => __( 'Enable the PrettyPhoto lighbox script on images within your website\'s content.', 'woothemes' ),
					'id' => $shortname . '_enable_lightbox',
					'std' => 'false',
					'type' => 'checkbox' );

		/* Footer */
		$options[] = array( 'name' => __( 'Footer Customization', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'footer' );

		$url =  get_template_directory_uri() . '/functions/images/';
		$options[] = array( 'name' => __( 'Footer Widget Areas', 'woothemes' ),
			'desc' => __( 'Select how many footer widget areas you want to display.', 'woothemes' ),
			'id' => $shortname."_footer_sidebars",
			'std' => "4",
			'type' => 'images',
			'options' => array(
				'0' => $url . 'layout-off.png',
				'1' => $url . 'footer-widgets-1.png',
				'2' => $url . 'footer-widgets-2.png',
				'3' => $url . 'footer-widgets-3.png',
				'4' => $url . 'footer-widgets-4.png' )
		);

		$options[] = array( 'name' => __( 'Custom Affiliate Link', 'woothemes' ),
			'desc' => __( 'Add an affiliate link to the WooThemes logo in the footer of the theme.', 'woothemes' ),
			'id' => $shortname."_footer_aff_link",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Enable Custom Footer (Left)', 'woothemes' ),
			'desc' => __( 'Activate to add the custom text below to the theme footer.', 'woothemes' ),
			'id' => $shortname."_footer_left",
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Custom Text (Left)', 'woothemes' ),
			'desc' => __( 'Custom HTML and Text that will appear in the footer of your theme.', 'woothemes' ),
			'id' => $shortname."_footer_left_text",
			'std' => '',
			'type' => 'textarea' );

		$options[] = array( 'name' => __( 'Enable Custom Footer (Right)', 'woothemes' ),
			'desc' => __( 'Activate to add the custom text below to the theme footer.', 'woothemes' ),
			'id' => $shortname."_footer_right",
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Custom Text (Right)', 'woothemes' ),
			'desc' => __( 'Custom HTML and Text that will appear in the footer of your theme.', 'woothemes' ),
			'id' => $shortname."_footer_right_text",
			'std' => '',
			'type' => 'textarea' );

		/* Subscribe & Connect */
		$options[] = array( 'name' => __( 'Subscribe & Connect', 'woothemes' ),
			'type' => 'heading',
			'icon' => "connect" );

		$options[] = array( 'name' => __( 'Enable Subscribe & Connect - Single Post', 'woothemes' ),
			'desc' => sprintf( __( 'Enable the subscribe & connect area on single posts. You can also add this as a %1$s in your sidebar.', 'woothemes' ), '<a href="' . home_url() . '/wp-admin/widgets.php">widget</a>' ),
			'id' => $shortname."_connect",
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Subscribe Title', 'woothemes' ),
			'desc' => __( 'Enter the title to show in your subscribe & connect area.', 'woothemes' ),
			'id' => $shortname."_connect_title",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Text', 'woothemes' ),
			'desc' => __( 'Change the default text in this area.', 'woothemes' ),
			'id' => $shortname."_connect_content",
			'std' => '',
			'type' => 'textarea' );

		$options[] = array( 'name' => __( 'Subscribe By E-mail ID (Feedburner)', 'woothemes' ),
			'desc' => __( 'Enter your <a href="http://www.woothemes.com/tutorials/how-to-find-your-feedburner-id-for-email-subscription/">Feedburner ID</a> for the e-mail subscription form.', 'woothemes' ),
			'id' => $shortname."_connect_newsletter_id",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Subscribe By E-mail to MailChimp', 'woothemes', 'woothemes' ),
			'desc' => __( 'If you have a MailChimp account you can enter the <a href="http://woochimp.heroku.com" target="_blank">MailChimp List Subscribe URL</a> to allow your users to subscribe to a MailChimp List.', 'woothemes' ),
			'id' => $shortname."_connect_mailchimp_list_url",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Enable RSS', 'woothemes' ),
			'desc' => __( 'Enable the subscribe and RSS icon.', 'woothemes' ),
			'id' => $shortname."_connect_rss",
			'std' => 'true',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Twitter URL', 'woothemes' ),
			'desc' => __( 'Enter your  <a href="http://www.twitter.com/">Twitter</a> URL e.g. http://www.twitter.com/woothemes', 'woothemes' ),
			'id' => $shortname."_connect_twitter",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Facebook URL', 'woothemes' ),
			'desc' => __( 'Enter your  <a href="http://www.facebook.com/">Facebook</a> URL e.g. http://www.facebook.com/woothemes', 'woothemes' ),
			'id' => $shortname."_connect_facebook",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'YouTube URL', 'woothemes' ),
			'desc' => __( 'Enter your  <a href="http://www.youtube.com/">YouTube</a> URL e.g. http://www.youtube.com/woothemes', 'woothemes' ),
			'id' => $shortname."_connect_youtube",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Flickr URL', 'woothemes' ),
			'desc' => __( 'Enter your  <a href="http://www.flickr.com/">Flickr</a> URL e.g. http://www.flickr.com/woothemes', 'woothemes' ),
			'id' => $shortname."_connect_flickr",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'LinkedIn URL', 'woothemes' ),
			'desc' => __( 'Enter your  <a href="http://www.www.linkedin.com.com/">LinkedIn</a> URL e.g. http://www.linkedin.com/in/woothemes', 'woothemes' ),
			'id' => $shortname."_connect_linkedin",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Delicious URL', 'woothemes' ),
			'desc' => __( 'Enter your <a href="http://www.delicious.com/">Delicious</a> URL e.g. http://www.delicious.com/woothemes', 'woothemes' ),
			'id' => $shortname."_connect_delicious",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Google+ URL', 'woothemes' ),
			'desc' => __( 'Enter your <a href="http://plus.google.com/">Google+</a> URL e.g. https://plus.google.com/104560124403688998123/', 'woothemes' ),
			'id' => $shortname."_connect_googleplus",
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Enable Related Posts', 'woothemes' ),
			'desc' => __( 'Enable related posts in the subscribe area. Uses posts with the same <strong>tags</strong> to find related posts. Note: Will not show in the Subscribe widget.', 'woothemes' ),
			'id' => $shortname."_connect_related",
			'std' => 'true',
			'type' => 'checkbox' );

		/* Advertising */
		$options[] = array( 'name' => __( 'Advertising', 'woothemes' ),
			'type' => 'heading',
			'icon' => "ads" );

		$options[] = array( 'name' => __( 'Top Ad (468x60px)', 'woothemes' ),
			'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Enable Ad', 'woothemes' ),
			'desc' => __( 'Enable the ad space', 'woothemes' ),
			'id' => $shortname."_ad_top",
			'std' => 'false',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Adsense code', 'woothemes' ),
			'desc' => __( 'Enter your adsense code (or other ad network code) here.', 'woothemes' ),
			'id' => $shortname."_ad_top_adsense",
			'std' => '',
			'type' => 'textarea' );

		$options[] = array( 'name' => __( 'Image Location', 'woothemes' ),
			'desc' => __( 'Enter the URL to the banner ad image location.', 'woothemes' ),
			'id' => $shortname."_ad_top_image",
			'std' => "http://www.woothemes.com/ads/468x60b.jpg",
			'type' => 'upload' );

		$options[] = array( 'name' => __( 'Destination URL', 'woothemes' ),
			'desc' => __( 'Enter the URL where this banner ad points to.', 'woothemes' ),
			'id' => $shortname."_ad_top_url",
			'std' => "http://www.woothemes.com",
			'type' => 'text' );

		/* Slider */
		$options[] = array( 'name' => __( 'Slider', 'woothemes' ),
			'icon' => 'slider',
			'type' => 'heading' );

		$options[] = array( 'name' => __( 'Slider Setup', 'woothemes' ),
			'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Hover Pause', 'woothemes' ),
			'desc' => __( 'Hovering over a slider will pause it.', 'woothemes' ),
			'id' => $shortname.'_slider_hover',
			'std' => 'true',
			'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Auto Fade Interval', 'woothemes' ),
			'desc' => __( 'The time in <strong>seconds</strong> each slide pauses for, before transitioning to the next.', 'woothemes' ),
			'id' => $shortname.'_slider_speed',
			'std' => '7',
			'type' => 'select',
			'options' => array( 'Off', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ) );

		$options[] = array( 'name' => __( 'Animation Speed', 'woothemes' ),
			'desc' => __( 'The time in <strong>seconds</strong> the animation between slides will take.', 'woothemes' ),
			'id' => $shortname.'_slider_animation_speed',
			'std' => '0.6',
			'type' => 'select',
			'options' => array( '0.0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0' ) );

		$options[] = array( 'name' => __( 'Homepage Slider', 'woothemes' ),
			'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Enable Homepage Slider', 'woothemes' ),
			'desc' => __( 'Enable the slider on the homepage (will also disable the body background image on the homepage).', 'woothemes' ),
			'id' => $shortname.'_enable_slides',
			'std' => 'true',
			'type' => 'checkbox' );

		$per_page = array();
		for ( $i = 1; $i <= 20; $i++ ) {
			$per_page[] = $i;
		}

		$options[] = array( 'name' => __( 'Number of Slides To Display', 'woothemes' ),
			'desc' => __( 'The number of slides to display on the homepage.', 'woothemes' ),
			'id' => $shortname . '_featured_limit',
			'std' => '6',
			'type' => 'select',
			'options' => $per_page );

		$options[] = array( 'name' => __( 'Slide Group', 'woothemes' ),
                    'desc' => __( 'Optionally choose to display only slides from a specific slide group.', 'woothemes' ),
                    'id' => $shortname . '_featured_slide_group',
                    'std' => '0',
                    'type' => 'select2',
                    'options' => $slide_groups );

		$options[] = array( 'name' => __( 'Slide Image Scrolling', 'woothemes' ),
			'desc' => __( 'Should the slide image stay fixed in position, or scroll with the rest of the page?', 'woothemes' ),
			'id' => $shortname . '_slider_scroll',
			'std' => 'fixed',
			'type' => 'select2',
			'options' => array( 'fixed' => __( 'Stay Fixed in Position', 'woothemes' ), 'scroll' => __( 'Scroll with the Page', 'woothemes' ) ) );

		/* Events */
		$options[] = array( 'name' => __( 'Events', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'events' );

		$options[] = array( 'name' => __( 'Connect your Songkick Events', 'woothemes' ),
				'desc' => '',
				'id' => $shortname . '_songkick_info_notice',
				'std' => __( 'Unsigned makes integrating your Songkick events a breeze. When Songkick is active, it overrides the default Woo - Events module and displays your events from Songkick instead.', 'woothemes' ),
				'type' => 'info' );

		$options[] = array( 'name' => __( 'Enable The Events Manager', 'woothemes' ),
			'desc' => __( 'Enable the ability to manage events.', 'woothemes' ),
			'id' => $shortname . '_enable_events',
			'std' => 'true',
			'type' => 'checkbox' );

		/* Discography */
		$options[] = array( 'name' => __( 'Discography', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'discography' );

		$options[] = array( 'name' => __( 'Enable The Discography Manager', 'woothemes' ),
			'desc' => __( 'Enable the ability to manage album releases.', 'woothemes' ),
			'id' => $shortname . '_enable_discography',
			'std' => 'true',
			'type' => 'checkbox' );

		/* Photo Albums */
		$options[] = array( 'name' => __( 'Photo Galleries', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'photo-albums' );

		$options[] = array( 'name' => __( 'Enable The Photo Galleries Manager', 'woothemes' ),
			'desc' => __( 'Enable the ability to manage photo galleries.', 'woothemes' ),
			'id' => $shortname . '_enable_photo_albums',
			'std' => 'true',
			'type' => 'checkbox' );

		/* Videos */
		$options[] = array( 'name' => __( 'Videos', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'media' );

		$options[] = array( 'name' => __( 'Enable The Video Manager', 'woothemes' ),
			'desc' => __( 'Enable the ability to manage videos.', 'woothemes' ),
			'id' => $shortname . '_enable_videos',
			'std' => 'true',
			'type' => 'checkbox' );

		/* Band Members */
		$options[] = array( 'name' => __( 'Band Members', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'band' );

		$options[] = array( 'name' => __( 'Enable The Band Members Manager', 'woothemes' ),
			'desc' => __( 'Enable the ability to manage band member information.', 'woothemes' ),
			'id' => $shortname . '_enable_bandmembers',
			'std' => 'true',
			'type' => 'checkbox' );

		/* Press */
		$options[] = array( 'name' => __( 'Press', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'press' );
			
		$options[] = array( 'name' => __( 'Enable The Press Clippings Manager', 'woothemes' ),
			'desc' => __( 'Enable the ability to manage press clippings.', 'woothemes' ),
			'id' => $shortname . '_enable_press',
			'std' => 'true',
			'type' => 'checkbox' );

		/* SoundCloud */
		$options[] = array( 'name' => __( 'SoundCloud', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'soundcloud' );

		$options[] = array( 'name' => __( 'SoundCloud Username', 'woothemes' ),
			'desc' => __( 'Add your SoundCloud username here.', 'woothemes' ),
			'id' => $shortname . '_soundcloud_username',
			'std' => '',
			'type' => 'text' );

		$options[] = array( 'name' => __( 'Player Colour', 'woothemes' ),
			'desc' => __( 'Choose a colour for your SoundCloud players.', 'woothemes' ),
			'id' => $shortname . '_soundcloud_colour',
			'std' => '#FF7700',
			'type' => 'color' );

		if ( get_option( 'woo_soundcloud_username' ) != '' ) {

			$options[] = array( 'name' => __( 'Refresh SoundCloud Data', 'woothemes' ),
				'desc' => '',
				'id' => $shortname . '_soundcloud_refresh',
				'std' => sprintf( __( 'In order to speed up your website, we check your SoundCloud account for updated information once a month. It is, however, possible to force this check to happen if you\'ve updated your SoundCloud profile.%s', 'woothemes' ), '<br /><br /><a href="#" class="soundcloud-refresh button">' . __( 'Refresh SoundCloud Data', 'woothemes' ) . '</a> <small>(' . __( 'This may take a few seconds', 'woothemes' ) . ')</small><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="ajax-loading" id="ajax-loading" alt="' . __( 'Loading', 'woothemes' ) . '" />' ),
				'type' => 'info' );

		}
		
		/* SongKick */
		$options[] = array( 'name' => __( 'Songkick', 'woothemes' ),
			'type' => 'heading',
			'icon' => 'songkick' );

		$options[] = array( 'name' => __( 'Connect your Songkick Events', 'woothemes' ),
				'desc' => '',
				'id' => $shortname . '_songkick_info',
				'std' => __( 'Unsigned makes integrating your Songkick events a breeze. When Songkick is active, it overrides the default Woo - Events module and displays your events from Songkick instead.', 'woothemes' ),
				'type' => 'info' );

		$options[] = array( 'name' => __( 'Songkick Artist ID', 'woothemes' ),
			'desc' => __( 'Add your Songkick artist ID here.', 'woothemes' ) . '<br /><br />' . __( 'Your artist ID is the following:', 'woothemes' ) . '<br />http://www.songkick.com/artists/<strong>artist-id</strong>',
			'id' => $shortname . '_songkick_artist_id',
			'std' => '',
			'type' => 'text' );

		if ( get_option( 'woo_songkick_artist_id' ) != '' ) {

			$options[] = array( 'name' => __( 'Refresh Songkick Data', 'woothemes' ),
				'desc' => '',
				'id' => $shortname . '_songkick_refresh',
				'std' => sprintf( __( 'In order to speed up your website, we check your Songkick account for updated information once a week. It is, however, possible to force this check to happen if you\'ve updated your Songkick profile.%s', 'woothemes' ), '<br /><br /><a href="#" class="songkick-refresh button">' . __( 'Refresh Songkick Data', 'woothemes' ) . '</a> <small>(' . __( 'This may take a few seconds', 'woothemes' ) . ')</small><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="ajax-loading" id="ajax-loading" alt="' . __( 'Loading', 'woothemes' ) . '" />' ),
				'type' => 'info' );

		}

		// Add extra options through function
		if ( function_exists( 'woo_options_add' ) )
			$options = woo_options_add( $options );

		if ( get_option( 'woo_template' ) != $options ) update_option( 'woo_template', $options );
		if ( get_option( 'woo_themename' ) != $themename ) update_option( 'woo_themename', $themename );
		if ( get_option( 'woo_shortname' ) != $shortname ) update_option( 'woo_shortname', $shortname );
		if ( get_option( 'woo_manual' ) != $manualurl ) update_option( 'woo_manual', $manualurl );

		// Woo Metabox Options
		// Start name with underscore to hide custom key from the user
		$woo_metaboxes = array();

		global $post;

		// Events Custom Fields
		if ( ( get_post_type() == 'event' ) || ( ! get_post_type() ) ) {

			// Start
			$woo_metaboxes[] = array ( 'name' => '_event_start',
				'std' => '',
				'label' => __( 'Start Date/Time', 'woothemes' ),
				'type' => 'timestamp',
				'desc' => __( 'When does this event start?', 'woothemes' )
			);

			// End
			$woo_metaboxes[] = array ( 'name' => '_event_end',
				'std' => '',
				'label' => __( 'End Date/Time', 'woothemes' ),
				'type' => 'timestamp',
				'desc' => __( 'When does this event end?', 'woothemes' )
			);

			// Venue
			$woo_metaboxes[] = array ( 'name' => '_event_venue',
				'std' => '',
				'label' => __( 'Venue', 'woothemes' ),
				'type' => 'text',
				'desc' => __( 'Where is the event taking place?', 'woothemes' )
			);

			// Ticket Sales URL
			$woo_metaboxes[] = array ( 'name' => '_tickets_url',
				'std' => '',
				'label' => __( 'Ticket Sales URL', 'woothemes' ),
				'type' => 'text',
				'desc' => __( 'The link to where tickets are sold online.', 'woothemes' )
			);

			// Ticket Sales Text
			$woo_metaboxes[] = array ( 'name' => '_tickets_text',
				'std' => '',
				'label' => __( 'Ticket Sales Link Text', 'woothemes' ),
				'type' => 'text',
				'desc' => __( 'The text on the link to where tickets are sold online.', 'woothemes' )
			);

			// Price
			$woo_metaboxes[] = array ( 'name' => '_ticket_price',
				'std' => '',
				'label' => __( 'Ticket Price', 'woothemes' ),
				'type' => 'text',
				'desc' => __( 'The ticket price for this event (optional).', 'woothemes' )
			);
		}

		// Album Custom Fields
		if ( ( get_post_type() == 'album' ) || ( ! get_post_type() ) ) {

			// Release Date
			$woo_metaboxes[] = array ( 'name' => '_release_date',
				'std' => '',
				'label' => __( 'Release Date', 'woothemes' ),
				'type' => 'timestamp',
				'desc' => __( 'When was this album released?', 'woothemes' )
			);

			// Unique ID
			$woo_metaboxes[] = array ( 'name' => '_catalog_id',
				'std' => '',
				'label' => __( 'Catalog ID', 'woothemes' ),
				'type' => 'text',
				'desc' => __( 'The unique ID used to identify this album in your catalog.', 'woothemes' )
			);

			/* WooCommerce Integration with Discography */
			if ( class_exists( 'woocommerce' ) ) {
				global $woocommerce;

				$options = array();
				$args = array(
					'post_type' => 'product',
					'post_status' => 'publish',
					'orderby' => 'title',
					'order' => 'ASC',
					'posts_per_page' => -1
				);
				
				$qry = new WP_Query( $args );

				if( $qry->have_posts() ) {
					while( $qry->have_posts() ) { $qry->the_post();
						$options[ get_the_ID() ] = get_the_title();
					}

					// Unique ID
					$woo_metaboxes[] = array ( 'name' => '_product_id',
						'std' => '',
						'label' => __( 'Product', 'woothemes' ),
						'type' => 'select2',
						'options' => $options,
						'desc' => __( 'Link this album to a product in your catalog.', 'woothemes' )
					);
				}
			}
		}

		// Video Custom Fields
		if ( ( get_post_type() == 'video' ) || ( ! get_post_type() ) ) {

			// Video Embed Code
			$woo_metaboxes[] = array ( 'name' => 'embed',
				'std' => '',
				'label' => __( 'Embed Code', 'woothemes' ),
				'type' => 'textarea',
				'desc' => __( 'Enter the video embed code for your video (YouTube, Vimeo or similar).', 'woothemes' )
			);
		}

		// Band Member Custom Fields
		if ( ( get_post_type() == 'band_member' ) || ( ! get_post_type() ) ) {

			// Role
			$woo_metaboxes[] = array ( 'name' => '_role',
				'std' => '',
				'label' => __( 'Role', 'woothemes' ),
				'type' => 'text',
				'desc' => __( 'This member\'s role in the band.', 'woothemes' )
			);
		}

		if ( ( get_post_type() == 'post' ) || ( !get_post_type() ) ) {

			$woo_metaboxes[] = array ( 'name' => 'image',
				'label' => __( 'Image', 'woothemes' ),
				'type' => 'upload',
				'desc' => __( 'Upload an image or enter an image URL.', 'woothemes' ) );

			if ( get_option( 'woo_resize' ) == 'true' ) {
				$woo_metaboxes[] = array ( 'name' => '_image_alignment',
					'std' => 'c',
					'label' => __( 'Image Crop Alignment', 'woothemes' ),
					'type' => 'select2',
					'desc' => __( 'Select crop alignment for resized image', 'woothemes' ),
					'options' => array( 'c' => __( 'Center', 'woothemes' ),
						't' => __( 'Top', 'woothemes' ),
						'b' => __( 'Bottom', 'woothemes' ),
						'l' => __( 'Left', 'woothemes' ),
						'r' => __( 'Right', 'woothemes' ) ) );
			}

			// Video Embed Code
			$woo_metaboxes[] = array ( 'name' => 'embed',
				'std' => '',
				'label' => __( 'Embed Code', 'woothemes' ),
				'type' => 'textarea',
				'desc' => __( 'Enter the video embed code for your video (YouTube, Vimeo or similar).', 'woothemes' )
			);

		} // End post

		// Press Clippings custom fields.
		if ( ( get_post_type() == 'press' ) || ( !get_post_type() ) ) {

			// Video Embed Code
			$woo_metaboxes[] = array ( 'name' => 'embed',
				'std' => '',
				'label' => __( 'Embed Code', 'woothemes' ),
				'type' => 'textarea',
				'desc' => __( 'Enter the video embed code for your video (YouTube, Vimeo or similar).', 'woothemes' )
			);
			
			// Publication Date
			$woo_metaboxes[] = array ( 'name' => '_publication_date',
				'std' => '',
				'label' => __( 'Publication Date', 'woothemes' ),
				'type' => 'timestamp',
				'desc' => __( 'When was this press clipping published in the media?', 'woothemes' )
			);
			
			// Media Portal
			$woo_metaboxes[] = array ( 'name' => '_media_portal',
				'std' => '',
				'label' => __( 'Media', 'woothemes' ),
				'type' => 'text',
				'desc' => __( 'By which publication was this clipping published?', 'woothemes' )
			);

		} // End press

		if ( ( get_post_type() != 'slide' ) ) {

			$woo_metaboxes[] = array ( 'name' => "_layout",
				'std' => 'layout-default',
				'label' => __( 'Layout', 'woothemes' ),
				'type' => 'images',
				'desc' => __( 'Select the layout you want on this specific entry.', 'woothemes' ),
				'options' => array(
					'layout-default' => $url . 'layout-off.png',
					'layout-full' => get_template_directory_uri() . '/functions/images/' . '1c.png',
					'layout-left-content' => get_template_directory_uri() . '/functions/images/' . '2cl.png',
					'layout-right-content' => get_template_directory_uri() . '/functions/images/' . '2cr.png' ) );

		}

		// Add extra metaboxes through function
		if ( function_exists( "woo_metaboxes_add" ) )
			$woo_metaboxes = woo_metaboxes_add( $woo_metaboxes );

		if ( get_option( 'woo_custom_template' ) != $woo_metaboxes ) update_option( 'woo_custom_template', $woo_metaboxes );

	} // END woo_options()
} // END function_exists()

// Add options to admin_head
add_action( 'admin_head', 'woo_options' );

//Enable WooSEO on these Post types
$seo_post_types = array( 'post', 'page' );
define( "SEOPOSTTYPES", serialize( $seo_post_types ) );

//Global options setup
add_action( 'init', 'woo_global_options' );
function woo_global_options(){
	// Populate WooThemes option in array for use in theme
	global $woo_options;
	$woo_options = get_option( 'woo_options' );
}
?>