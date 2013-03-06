<?php
/**
 * WooThemes SongKick Bridge.
 *
 * Creates a bridge to SongKick.
 *
 * @category Modules
 * @package WordPress
 * @subpackage WooFramework
 * @author Matty at WooThemes
 * @date 2012-02-07.
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
 * - function get_events()
 * - function get_gigography()
 * - function enqueue_scripts()
 * - function api_request()
 * - function refresh_transients()
 * - function display_formatted_date_text()
 * - function display_formatted_event_date_status()
 * - function get_next_weekend_dates()
 * - function get_dates_of_week()
 * - function powered_by()
 * - function generate_events_list_html()
 * - function events_list_shortcode()
 */

class WooThemes_SongKick {

	/**
	 * Variables
	 *
	 * @description Setup of variable placeholders, to be populated when the constructor runs.
	 * @since 1.0.0
	 */
	
	var $token;
	var $artist_id;
	var $dir;
	
	var $api_url;
	var $client_id;
	var $expire_time;
	var $data;
	
	var $template_url;
	
	var $settings;

	/**
	 * WooThemes_SongKick function.
	 *
	 * @description Constructor function. Sets up the class and registers variable action hooks.
	 * @access public
	 * @param string $username
	 * @param array $settings
	 * @return void
	 */
	function WooThemes_SongKick ( $artist_id, $settings = array() ) {
		global $woo_options;
		$this->dir = 'woo-songkick';
		
		$this->token = 'songkick';
		$this->artist_id = sanitize_user( $artist_id );
		
		$this->api_url = 'http://api.songkick.com/api/3.0/';
		$this->expire_time = 60*60*24*7; // One week - 60 seconds * 60 minutes * 24 hours * 7 days
		$this->api_key = 'uZWyr41TTmBXkKii';
		$this->data = array();
		
		$this->template_url = get_template_directory_uri();
		
		$this->settings = $settings;
	} // End WooThemes_SongKick()
	
	/**
	 * init function.
	 *
	 * @description This guy runs the show. Rocket boosters... engage!
	 * @access public
	 * @return void
	 */
	function init() {
		
		add_action( 'wp_ajax_woo_songkick_refresh', array( &$this, 'refresh_transients' ) );
		add_action( 'wp_ajax_nopriv_woo_songkick_refresh', array( &$this, 'refresh_transients' ) );
		
		add_shortcode( 'woo_songkick_events', array( &$this, 'events_list_shortcode' ) );
		
		if ( is_admin() ) {
			global $pagenow;

			if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'woothemes' ) || $pagenow == 'admin.php' ) {
				add_action( 'admin_print_scripts', array( &$this, 'enqueue_scripts' ), 10 );
			}
		}

	} // End init()
	
	/**
	 * get_events function.
	 * 
	 * @access public
	 * @return array $data
	 */
	function get_events () {
		$transient_key = 'woo_' . $this->token . '_events';
		
		if ( false === ( $data = get_transient( $transient_key ) ) ) {
			$data = $this->api_request( array(), 'artists', 'calendar.json' );

			if ( isset( $data->resultsPage->results->event ) ) {
				$events = $data->resultsPage->results->event;
				set_transient( $transient_key, $events, $this->expire_time );
			}
		}
		
		return $data;
	} // End get_events()
	
	/**
	 * get_gigography function.
	 * 
	 * @access public
	 * @return array $data
	 */
	function get_gigography () {
		$transient_key = 'woo_' . $this->token . '_gigography';
		
		if ( false === ( $data = get_transient( $transient_key ) ) ) {
			$data = $this->api_request( array(), 'artists', 'gigography.json' );

			if ( isset( $data->resultsPage->results->event ) ) {
				$events = $data->resultsPage->results->event;
				set_transient( $transient_key, $events, $this->expire_time );
			}
		}
		
		return $data;
	} // End get_gigography()
	
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
		
		$ajax_vars = array( 'songkick_refresh_nonce' => wp_create_nonce( 'songkick_refresh_nonce' ) );

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
	 * @param string $type (default: 'artists')
	 * @param string $endpoint (default: 'calendar.json')
	 * @return string $data
	 */
	function api_request ( $params = array(), $type = 'artists', $endpoint = 'calendar.json' ) {
		$data = '';
		
		$url = trailingslashit( $this->api_url ) . $type . '/' . $this->artist_id . '/' . $endpoint . '?';
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
		
		$url .= '&apikey=' . $this->api_key;

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
		$nonce = $_POST['songkick_refresh_nonce'];
			
		//Add nonce security to the request
		if ( ! wp_verify_nonce( $nonce, 'songkick_refresh_nonce' ) ) {
			die();
		}
		$refreshed = false;
		
		delete_transient( 'woo_' . $this->token . '_events' );
		delete_transient( 'woo_' . $this->token . '_gigography' );
		
		$events = $this->get_events();
		$gigography = $this->get_gigography();
		
		if ( $events && $gigography ) {
			$refreshed = true;
		}
		
		echo $refreshed;
		die(); // WordPress may print out a spurious zero without this can be particularly bad if using JSON
	} // End refresh_transients()
	
	/**
	 * display_formatted_date_text function.
	 * 
	 * @access public
	 * @param int $start (timestamp)
	 * @param int $end (timestamp)
	 * @return string $text
	 */
	function display_formatted_date_text ( $start, $end ) {
		$text = '';
		
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		
		$text = '<strong>' . __( 'Start:', 'woothemes' ) . '</strong> ' . date_i18n( $date_format, $start ) . ' ' . __( 'at', 'woothemes' ) . ' ' . date_i18n( $time_format, $start );
		
		if ( $end > $start ) {
			$text .= '<br /><strong>' . __( 'End:', 'woothemes' ) . '</strong> ';
			$text .= date_i18n( $date_format, $end ) . ' ' . __( 'at', 'woothemes' ) . ' ' . date_i18n( $time_format, $end );
		}
		
		return $text;
	} // End display_formatted_date_text()
	
	/**
	 * display_formatted_event_date_status function.
	 *
	 * @description Is the event in the past, present or future? Is it happening right now?
	 * @access public
	 * @param int $start (timestamp)
	 * @param int $end (timestamp)
	 * @return string $text
	 */
	function display_formatted_event_date_status ( $start, $end ) {
		$text = '';
		$class = 'event-timeframe';
		
		// Upcoming Events
		if ( $start > time() ) {
			$text = __( 'Upcoming', 'woothemes' );
			$class .= ' upcoming';
		}
		
		// Past Events
		if ( $end < time() ) {
			$text = __( 'Past', 'woothemes' );
			$class .= ' past';
		}
		
		// Happening Right Now
		if ( ( time() > $start ) && ( time() < $end ) ) {
			$text = __( 'Right Now', 'woothemes' );
			$class .= ' right-now';
		}
		
		return '<span class="' . $class . '">' . $text . '</span>';
	} // End display_formatted_event_date_status()
	
	/**
	 * get_next_weekend_dates function.
	 * 
	 * @access public
	 * @param int $now (timestamp)
	 * @return array $weekend_dates
	 */
	function get_next_weekend_dates ( $now ) {
		$end_date = strtotime( '+1 week' );
		$weekend_dates = array();
		$stored_indecies = array();
		
		while ( date( 'Y-m-d', $now ) != date( 'Y-m-d', $end_date ) ) {
		    $day_index = date( 'w', $now );
		    if ( in_array( $day_index, array( 0, 5, 6 ) ) && ! in_array( $day_index, $stored_indecies ) ) { // Sunday, Friday or Saturday
		        $weekend_dates[] = $now;
		    }
		    $now = strtotime( date( 'Y-m-d', $now ) . '+1 day' );
		}
		
		return $weekend_dates;
	} // End get_next_weekend_dates()
	
	/**
	 * get_dates_of_week function.
	 *
	 * @description Get the timestamps for each day of the current day's week.
	 * @access public
	 * @param int $now (timestamp)
	 * @return array $dates
	 */
	function get_dates_of_week ( $now ) {
		$dates = array();
		
		$i = date( 'w', $now );

		// Reset the date to the closest Sunday past.
		if ( $i > 0 ) {
			$type = 'days';
			if ( $i == 1 ) { $type = 'day'; }
			$now = strtotime( date( 'Y-m-d', $now ) . '-' . $i . ' ' . $type );
			$i = 0;
		}
		
		while ( $i <= 6 ) {
			$dates[] = $now;

			$now = strtotime( date( 'Y-m-d', $now ) . '+1 day' );

			$i++;
		}

		return $dates;
	} // End get_dates_of_week()
	
	/**
	 * powered_by function.
	 *
	 * @description Display "Powered By" text.
	 * @access public
	 * @since 1.2.0
	 * @return void
	 */
	function powered_by () {
		return '<p class="powered-by"><a href="http://songkick.com/" class="powered-by-' . $this->token . '" target="_blank">' . __( 'Powered by SongKick', 'woothemes' ) . '</a></p>';
	} // End powered_by()
	
	/**
	 * generate_events_list_html function.
	 *
	 * @description Generate a list of events and return the HTML.
	 * @access public
	 * @param array $args
	 * @return string $html
	 */
	function generate_events_list_html ( $args ) {
		$defaults = array( 'display_type' => 'upcoming', 'limit' => 5 );
		
		$args = wp_parse_args( $args, $defaults );
		
		if ( $args['display_type'] == 'past' ) {
			$events = $this->get_gigography();
		} else {
			$events = $this->get_events();
		}

		if ( count( $events ) > $args['limit'] ) {
			$events = array_slice( $events, 0, $args['limit'] );
		}

		$html = '';
		
		if ( count( $events ) > 0 ) {
			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );
			
			$html .= '<ul>' . "\n";
				foreach ( $events as $k => $v ) {
					$start_date = strtotime( $v->start->date );
					$start_time = strtotime( $v->start->time );

					$html .= '<li>' . "\n";
						$html .= '<h4 class="event-title"><a href="' . esc_attr( $v->uri ) . '" title="' . esc_attr( $v->displayName ) . '" target="_blank">' . esc_attr( $v->displayName ) . '</a></h4>' . "\n";
						$html .= '<p class="date"><strong class="label">' . __( 'Date:', 'woothemes' ) . '</strong> ' . date_i18n( $date_format, $start_date );
						if ( $start_time ) { $html .= ' @ ' . date_i18n( $time_format, $start_time ); }
						$html .= '</p>' . "\n";
					
						$html .= '<p class="venue"><strong class="label">' . __( 'Venue:', 'woothemes' ) . '</strong> <a href="' . esc_url( $v->venue->uri ) . '" target="_blank">' . $v->venue->displayName . '</a>, ' . $v->venue->metroArea->displayName . '</p>' . "\n";
						
					$html .= '</li>' . "\n";
				}
			$html .= '</ul>' . "\n";
			
			// Add the "Powered By" text.
			$html .= $this->powered_by();
			
			$html .= '<div class="fix"></div>' . "\n";
			
		} else {
			$html = '<p>' . __( 'No events are currently listed.', 'woothemes' ) . '</p>' . "\n";
		}
		
		return $html;
	} // End generate_events_list_html()
	
	/**
	 * events_list_shortcode function.
	 *
	 * @description Generate the HTML output for the [woo_songkick_events] shortcode.
	 * @access public
	 * @param array $atts
	 * @param string $content (default: null)
	 * @return string $html
	 */
	function events_list_shortcode ( $atts, $content = null ) {
		$defaults = array( 'display_type' => 'upcoming', 'limit' => 5 );
		$atts = shortcode_atts( $defaults, $atts );
		
		$html = '<div class="woo-songkick-events">' . "\n" . $this->generate_events_list_html( $atts ) . "\n" . '</div>' . "\n";
		
		return apply_filters( 'woo_songkick_events_shortcode', $html, $atts );
	} // End events_list_shortcode()
} // End Class
?>