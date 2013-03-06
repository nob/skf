<?php
/**
 * WooThemes SoundCloud Bridge.
 *
 * Creates a bridge to SoundCloud.
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
 * - var $username
 * - var $dir
 *
 * - var $api_url
 * - var $client_id
 * - var $expire_time
 * - var $data
 *
 * - var $template_url
 *
 * - var $settings

 * - Constructor Function
 * - function init()
 * - function get_tracks()
 * - function get_playlists()
 * - function generate_player()
 * - function enqueue_scripts()
 * - function api_request()
 * - function refresh_transients()
 */

class WooThemes_SoundCloud {

	/**
	 * Variables
	 *
	 * @description Setup of variable placeholders, to be populated when the constructor runs.
	 * @since 1.0.0
	 */
	
	var $token;
	var $username;
	var $dir;
	
	var $api_url;
	var $client_id;
	var $expire_time;
	var $data;
	
	var $template_url;
	
	var $settings;

	/**
	 * WooThemes_SoundCloud function.
	 *
	 * @description Constructor function. Sets up the class and registers variable action hooks.
	 * @access public
	 * @param string $username
	 * @param array $settings
	 * @return void
	 */
	function WooThemes_SoundCloud ( $username, $settings = array() ) {
		global $woo_options;
		$this->dir = 'woo-soundcloud';
		
		$this->token = 'soundcloud';
		$this->username = sanitize_user( $username );
		
		$this->api_url = 'http://api.soundcloud.com/users/';
		$this->expire_time = 60*60*24*30; // One month - 60 seconds * 60 minutes * 24 hours * 30 days
		$this->client_id = 'b1f0e2f51c638d3251a4dc7e04511e01';
		$this->data = array();
		
		$this->template_url = get_template_directory_uri();
		
		$this->settings = $settings;
	} // End WooThemes_SoundCloud()
	
	/**
	 * init function.
	 *
	 * @description This guy runs the show. Rocket boosters... engage!
	 * @access public
	 * @return void
	 */
	function init() {
		
		add_action( 'wp_ajax_woo_soundcloud_refresh', array( &$this, 'refresh_transients' ) );
		add_action( 'wp_ajax_nopriv_woo_soundcloud_refresh', array( &$this, 'refresh_transients' ) );
		
		if ( is_admin() ) {
			global $pagenow;

			if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'woothemes' ) || $pagenow == 'admin.php' ) {
				add_action( 'admin_print_scripts', array( &$this, 'enqueue_scripts' ), 10 );
			}
		}
	} // End init()
	
	/**
	 * get_tracks function.
	 * 
	 * @access public
	 * @return array $data
	 */
	function get_tracks () {
		$transient_key = 'woo_' . $this->token . '_tracks';
		
		if ( false === ( $data = get_transient( $transient_key ) ) ) {
			$data = $this->api_request( array(), '/tracks.json' );
			
			if ( is_array( $data ) ) {
				set_transient( $transient_key, $data, $this->expire_time );
			}
		}
		
		return $data;
	} // End get_tracks()
	
	/**
	 * get_playlists function.
	 * 
	 * @access public
	 * @return array $data
	 */
	function get_playlists () {
		$transient_key = 'woo_' . $this->token . '_playlists';
		
		if ( false === ( $data = get_transient( $transient_key ) ) ) {
			$data = $this->api_request( array(), '/playlists.json' );
			
			if ( is_array( $data ) ) {
				set_transient( $transient_key, $data, $this->expire_time );
			}
		}
		
		return $data;
	} // End get_playlists()
	
	/**
	 * generate_player function.
	 * 
	 * @access public
	 * @param int $id
	 * @param string $format
	 * @param string $type
	 * @param array $args
	 * @return string $player
	 */
	function generate_player ( $id, $format = 'tracks', $args = array() ) {
		$defaults = array(
			'type' => 'standard', 
			'width' => '100%', 
			'height' => '300', 
			'colour' => $this->settings['colour']
			);
		
		$args = wp_parse_args( (array) $args, $defaults );
		
		$player = '';
		
		$colour = str_replace( '#', '', $args['colour'] );
		$show_artwork = 'true';
		if ( $format == 'tracks' ) {
			$show_artwork = 'false';
		}

		$player .= '<div id="soundcloud-player-' . $id . '" class="soundcloud-player">' . "\n";

		if ( $args['type'] == 'html5' ) {
			$player .= '<iframe width="' . $args['width'] . '" height="' . $args['height'] . '" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2F' . $format . '%2F' . $id . '&amp;auto_play=' . 'false' . '&amp;show_artwork=' . $show_artwork . '&amp;color=' . $colour . '"></iframe>' . "\n";
		} else {
			$player .= '<object width="' . $args['width'] . '" height="' . $args['height'] . '">';
			$player .= '<param name="movie" value="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2F' . $format . '%2F' . $id . '&amp;auto_play=' . 'false' . '&amp;player_type=' . $args['type'] . '&amp;show_comments=' . 'false' . '&amp;color=' . $colour . '"></param>';
			$player .= '<param name="allowscriptaccess" value="always"></param>';
			$player .= '<param name="wmode" value="transparent"></param>';
			$player .= '<embed wmode="transparent" allowscriptaccess="always" src="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2F' . $format . '%2F' . $id . '&amp;auto_play=' . 'false' . '&amp;player_type=' . $args['type'] . '&amp;show_comments=' . 'false' . '&amp;color=' . $colour . '" type="application/x-shockwave-flash" width="' . $args['width'] . '" height="' . $args['height'] . '"></embed>';
			$player .= '</object>' . "\n";
		}
		
		$player .= '</div><!--/.soundcloud-player-->' . "\n";
		
		return $player;
	} // End generate_player()
	
	/**
	 * enqueue_scripts function.
	 * 
	 * @access public
	 * @return void
	 */
	function enqueue_scripts () {
		wp_register_script( 'woo-' . $this->token . '-admininterface', $this->template_url . '/includes/' . $this->dir . '/assets/js/functions.js', '', '1.0.0' );
		wp_enqueue_script( 'woo-' . $this->token . '-admininterface' );
		
		$translation_strings = array();
		
		$ajax_vars = array( 'soundcloud_refresh_nonce' => wp_create_nonce( 'soundcloud_refresh_nonce' ) );

		$data = array_merge( $translation_strings, $ajax_vars );

		/* Specify variables to be made available to the fuctions.js file. */
		wp_localize_script( 'woo-' . $this->token . '-admininterface', 'woo_localized_data', $data );
	} // End enqueue_scripts()
	
	/**
	 * api_request function.
	 *
	 * @description Return the contents of a URL using wp_remote_post().
	 * @access public
	 * @param array $params (default: array())
	 * @param string $endpoint (default: '.xml')
	 * @return string $data
	 */
	function api_request ( $params = array(), $endpoint = '.json' ) {
		$data = '';
		
		$url = $this->api_url . $this->username . $endpoint . '?';
		if ( count( $params ) > 0 ) {
			$count = 0;
			foreach ( $params as $k => $v ) {
				$count++;
				if ( $count > 1 ) {
					$url .= '&';
				}
				$url .= $k . '=' . $v;
			}
		}
		
		$url .= '&client_id=' . $this->client_id;

		$response = wp_remote_get( $url, array(
			'method' => 'GET',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'cookies' => array()
		    )
		);
		
		if( is_wp_error( $response ) ) {
		  $data = new StdClass();
		  $data = json_decode( $data );
		} else {
			$data = $response['body'];
			$data = json_decode( $data );
		}
		return $data;
	} // End api_request()
	
	/**
	 * refresh_transients function.
	 * 
	 * @access public
	 * @return void
	 */
	function refresh_transients () {
		$nonce = $_POST['soundcloud_refresh_nonce'];
			
		//Add nonce security to the request
		if ( ! wp_verify_nonce( $nonce, 'soundcloud_refresh_nonce' ) ) {
			die();
		}
		$refreshed = false;
		
		delete_transient( 'woo_' . $this->token . '_tracks' );
		delete_transient( 'woo_' . $this->token . '_playlists' );
		
		$tracks = $this->get_tracks();
		$playlists = $this->get_playlists();
		
		if ( $tracks && $playlists ) {
			$refreshed = true;
		}
		
		echo $refreshed;
		die(); // WordPress may print out a spurious zero without this can be particularly bad if using JSON
	} // End refresh_transients()
} // End Class
?>