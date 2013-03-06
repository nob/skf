<?php
/*-----------------------------------------------------------------------------------*/
/* Load the widgets, with support for overriding the widget via a child theme.
/*-----------------------------------------------------------------------------------*/
$woo_options = get_option( 'woo_options' );

$widgets = array(
				'includes/widgets/widget-woo-tabs.php', 
				'includes/widgets/widget-woo-adspace.php', 
				'includes/widgets/widget-woo-blogauthor.php', 
				'includes/widgets/widget-woo-embed.php', 
				'includes/widgets/widget-woo-flickr.php', 
				'includes/widgets/widget-woo-search.php', 
				'includes/widgets/widget-woo-twitter.php', 
				'includes/widgets/widget-woo-subscribe.php'
				);

// Theme-specific Widgets

$widgets[] = 'includes/widgets/widget-woo-recentnews.php';

// Woo - SongKick
$has_songkick = false;

if ( ! isset( $woo_options['woo_songkick_artist_id'] ) || ( isset( $woo_options['woo_songkick_artist_id'] ) && ( apply_filters( 'woo_songkick_artist_id', $woo_options['woo_songkick_artist_id'] ) != '' ) ) ) {
	$has_songkick = true;
	$widgets[] = 'includes/widgets/widget-woo-songkick-events.php';
}

// Woo - Events
if ( ( $has_songkick == false ) && ( ! isset( $woo_options['woo_enable_events'] ) || ( isset( $woo_options['woo_enable_events'] ) && ( apply_filters( 'woo_enable_events', $woo_options['woo_enable_events'] ) != 'false' ) ) ) ) {
	$widgets[] = 'includes/widgets/widget-woo-events.php';
	$widgets[] = 'includes/widgets/widget-woo-tours.php';
	$widgets[] = 'includes/widgets/widget-woo-tourdates.php';
}

// Woo - Photos
if ( ! isset( $woo_options['woo_enable_photos'] ) || ( isset( $woo_options['woo_enable_photos'] ) && ( apply_filters( 'woo_enable_photos', $woo_options['woo_enable_photos'] ) != 'false' ) ) ) {
	$widgets[] = 'includes/widgets/widget-woo-photos.php';
	$widgets[] = 'includes/widgets/widget-woo-galleries.php';
}

// Woo - Discography
if ( ! isset( $woo_options['woo_enable_discography'] ) || ( isset( $woo_options['woo_enable_discography'] ) && ( apply_filters( 'woo_enable_discography', $woo_options['woo_enable_discography'] ) != 'false' ) ) ) {
	$widgets[] = 'includes/widgets/widget-woo-albumplayer.php';
	$widgets[] = 'includes/widgets/widget-woo-albums.php';
}

// Woo - SoundCloud
if ( ! isset( $woo_options['woo_soundcloud_username'] ) || ( isset( $woo_options['woo_soundcloud_username'] ) && ( apply_filters( 'woo_soundcloud_username', $woo_options['woo_soundcloud_username'] ) != '' ) ) ) {
	$widgets[] = 'includes/widgets/widget-woo-soundcloud-playlist.php';
	$widgets[] = 'includes/widgets/widget-woo-soundcloud-tracks.php';
}

// Woo - Videos
if ( ! isset( $woo_options['woo_enable_videos'] ) || ( isset( $woo_options['woo_enable_videos'] ) && ( apply_filters( 'woo_enable_videos', $woo_options['woo_enable_videos'] ) != 'false' ) ) ) {
	$widgets[] = 'includes/widgets/widget-woo-videos.php';
}

// Woo - Press
if ( ! isset( $woo_options['woo_enable_press'] ) || ( isset( $woo_options['woo_enable_press'] ) && ( apply_filters( 'woo_enable_press', $woo_options['woo_enable_press'] ) != 'false' ) ) ) {
	$widgets[] = 'includes/widgets/widget-woo-pressclippings.php';
}

// Allow child themes/plugins to add widgets to be loaded.
$widgets = apply_filters( 'woo_widgets', $widgets );
				
	foreach ( $widgets as $w ) {
		locate_template( $w, true );
	}

/*---------------------------------------------------------------------------------*/
/* Deregister Default Widgets */
/*---------------------------------------------------------------------------------*/
if (!function_exists( 'woo_deregister_widgets')) {
	function woo_deregister_widgets(){
	    unregister_widget( 'WP_Widget_Search' );         
	}
}
add_action( 'widgets_init', 'woo_deregister_widgets' );  


?>