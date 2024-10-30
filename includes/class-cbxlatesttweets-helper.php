<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class CBXLatestTweetsHelper
 *c
 */
class CBXLatestTweetsHelper {

	/**
	 * Get avaliable layouts
	 *
	 * @return mixed|void
	 */
	public static function get_layouts() {
		$layouts = array(
			'basic' => array(
				'title'        => esc_html__( 'Basic', 'cbxlatesttweets' ),
				'template_dir' => cbxlatesttweets_locate_template( 'basic.php' )
			),
			'grid' => array(
				'title'        => esc_html__( 'Grid', 'cbxlatesttweets' ),
				'template_dir' => cbxlatesttweets_locate_template( 'grid.php' )
			),
		);

		return  apply_filters( 'cbxlatesttweets_layouts', $layouts );
	}//end method get_layouts

	/**
	 * Get available layouts - plain
	 *
	 * @return mixed|void
	 */
	public static function get_layouts_p() {
		$layouts   = CBXLatestTweetsHelper::get_layouts();
		$layouts_p = array();

		foreach ( $layouts as $key => $value ) {
			$layouts_p[$key] = $value['title'];
		}

		return ( apply_filters( 'get_layouts_p', $layouts_p ) );
	}//end method get_layouts

	/**
	 * Get available layouts reverser and plain
	 *
	 * @return mixed|void
	 */
	public static function get_layouts_r() {
		$layouts   = CBXLatestTweetsHelper::get_layouts();
		$layouts_r = array();

		foreach ( $layouts as $key => $value ) {
			$layouts_r[ $value['title'] ] = $key;
		}

		return ( apply_filters( 'cbxlatesttweets_layouts_r', $layouts_r ) );
	}//end method get_layouts

	/**
	 * call twitter to get tweets
	 *
	 * @param string $username
	 * @param int    $limit
	 * @param int    $include_rts
	 * @param int    $exclude_replies
	 *
	 * @return array|mixed|object|string
	 */
	public static function getLatestTweets( $username = '', $limit = 10, $include_rts = 0, $exclude_replies = 1 ) {

		$result = '';

		$settings_api = new CBXLatestTweets_Settings_API();

		///$default_date_time_format = $settings_api->get_option( 'date_time_format', 'cbxlatesttweets_config', CBXLatestTweetsHelper::getGlobalDateTimeFormat() );
		//	$date_time_format = ($date_time_format == '')? $default_date_time_format: $date_time_format;

		//twitter api config
		$consumer_key        = $settings_api->get_option( 'consumer_key', 'cbxlatesttweets_api_config', '' );
		$consumer_secret     = $settings_api->get_option( 'consumer_secret', 'cbxlatesttweets_api_config', '' );
		$access_token        = $settings_api->get_option( 'access_token', 'cbxlatesttweets_api_config', '' );
		$access_token_secret = $settings_api->get_option( 'access_token_secret', 'cbxlatesttweets_api_config', '' );

		//tweet config
		$enable_cache = $settings_api->get_option( 'enable_cache', 'cbxlatesttweets_config', 1 );
		$cache_time   = intval( $settings_api->get_option( 'cache_time', 'cbxlatesttweets_config', 2 ) );

		$limit = ( intval( $limit ) > 20 ) ? 20 : $limit; //maximum 20 tweets per page


		if ( $username == '' ) {
			return esc_html__( 'Twitter username missing', 'cbxlatesttweets' );
		} elseif ( $consumer_key == '' || $consumer_secret == '' && $access_token == '' && $access_token_secret == '' ) {
			return esc_html__( 'Twitter api config incorrect', 'cbxlatesttweets' );
		} else {

			//$username_t = str_replace('#', '', $username);
			//$username_t = str_replace('?', '', $username_t);

			$username   = str_replace( ' ', '', $username );    //remove any space from the username
			$username   = str_replace( '@', '', $username );    //remove @ if used in twitter username
			$username_t = str_replace( ',', '-', $username ); //only multiple username


			$cache_key = 'cbxlatesttweets-' . strtolower( $username_t ) . '-' . $limit . '-' . $include_rts . '-' . $exclude_replies;

			//check for cache enable or not
			if ( $enable_cache ) {
				// Get any existing copy of our transient data
				if ( false === ( $result = get_transient( $cache_key ) ) ) {
					// It wasn't there, so regenerate the data and save the transient
					$result = self::getLatestTweetsData( $username,
						$limit,
						$include_rts,
						$exclude_replies,
						$consumer_key,
						$consumer_secret,
						$access_token,
						$access_token_secret );
					set_transient( $cache_key, $result, HOUR_IN_SECONDS * $cache_time );
				}
			} else {
				delete_transient( $cache_key );
				$result = self::getLatestTweetsData( $username,
					$limit,
					$include_rts,
					$exclude_replies,
					$consumer_key,
					$consumer_secret,
					$access_token,
					$access_token_secret );
			}

		}

		return $result;
	}

	/**
	 * Get latest tweets using api for multiple(using comma ,) username
	 *
	 * @param string $username
	 * @param int    $limit
	 * @param int    $include_rts
	 * @param int    $exclude_replies
	 * @param        $consumer_key
	 * @param        $consumer_secret
	 * @param        $access_token
	 * @param        $access_token_secret
	 *
	 * @return array|object
	 */
	public static function getLatestTweetsData($username = '', $limit = 10, $include_rts = 0, $exclude_replies = 1, $consumer_key, $consumer_secret, $access_token,	$access_token_secret) {
		require CBXLATESTTWEETS_ROOT_PATH."vendor/autoload.php";

		$connection = new Abraham\TwitterOAuth\TwitterOAuth( $consumer_key, $consumer_secret, $access_token,
			$access_token_secret );

		$username_temp_arr = array(); //handling multiple twitter user name comma separated
		$user_saerchapi    = false;
		$username_temp_arr = explode( ',', $username );
		if ( sizeof( $username_temp_arr ) > 1 ) {
			$user_saerchapi = true;
		}


		if ( $user_saerchapi ) {
			//var_dump($username);
			//q multiple user account search format 'from:user1+OR+from:user2'
			$q = '';
			foreach ( $username_temp_arr as $username_temp ) {
				if ( $q != '' ) {
					$q .= '+OR+';
				}
				$q .= 'from:' . $username_temp;
			}


			$statuses = $connection->get( "search/tweets",
				[
					'q'                => $q,
					"result_type"      => 'recent',
					"count"            => $limit,
					"include_entities" => true,
					//"exclude_replies" => $include_rts
				] );
		} else {
			$statuses = $connection->get( "statuses/user_timeline",
				[
					'screen_name'     => $username,
					"count"           => $limit,
					"include_rts"     => $include_rts,
					"exclude_replies" => $exclude_replies,
				] );
		}


		if ( isset( $statuses->errors ) ) {
			return $statuses->errors[0]->message;
		}

		if ( isset( $statuses->statuses ) ) {
			return $statuses->statuses;
		}

		return $statuses;
	}


	/**
	 * List all global option name with prefix cbxlatesttweets_
	 */
	public static function getAllOptionNames() {
		global $wpdb;

		$prefix       = 'cbxlatesttweets_';
		$option_names = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'",
			ARRAY_A );

		return apply_filters( 'cbxlatesttweets_option_names', $option_names );
	}//end method getAllOptionNames

	/**
	 * List all transient option name with prefix '_transient_cbxlatesttweets-'
	 */
	public static function getAllTransients() {
		global $wpdb;

		$prefix       = '_transient_cbxlatesttweets-';
		$option_names = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'",
			ARRAY_A );

		return apply_filters( 'cbxlatesttweets_transients_names', $option_names );
	}//end method getAllTransients

	/**
	 * Linkify twitter status text
	 *
	 * @param string $status_text
	 *
	 * @return mixed|string
	 */
	public static function linkifyTwitterStatus( $status_text = '' ) {

		$settings_api = new CBXLatestTweets_Settings_API();

		$linkify_link    = intval( $settings_api->get_option( 'linkify_link', 'cbxlatesttweets_config', 1 ) );
		$linkify_mention = intval( $settings_api->get_option( 'linkify_mention', 'cbxlatesttweets_config', 1 ) );
		$linkify_hashtag = intval( $settings_api->get_option( 'linkify_hashtag', 'cbxlatesttweets_config', 1 ) );

		if ( $linkify_link ) {
			$link_target = ' target="_blank"';
			// linkify URLs
			$status_text = preg_replace(
				'/(https?:\/\/\S+)/',
				'&lt;a class="linkify_link" href="\1"' . $link_target . '&gt;\1&lt;/a&gt;',
				$status_text
			);
		}


		if ( $linkify_mention ) {
			// linkify twitter users
			$status_text = preg_replace(
				'/(^|\s)@(\w+)/',
				'\1@&lt;a class="linkify_mention" href="http://twitter.com/\2"' . $link_target . '&gt;\2&lt;/a&gt;',
				$status_text
			);
		}

		if ( $linkify_hashtag ) {
			// linkify tags
			$status_text = preg_replace(
				'/(^|\s)#(\w+)/',
				'\1&lt;a class="linkify_hashtag" href="https://twitter.com/search?q=%23\2&src=hash"' . $link_target . '&gt;#\2&lt;/a&gt;',
				$status_text
			);
		}

		$status_text = html_entity_decode( $status_text, ENT_QUOTES, 'UTF-8' );

		return $status_text;
	}//end method linkifyTwitterStatus


	/**
	 * Returns formatted tweet time
	 *
	 * @param     $utimestamp
	 * @param int $type
	 * @param     $date_time_format
	 *
	 * @return false|string
	 */
	public static function getTweetTime( $utimestamp, $type = 1, $date_time_format ) {

		$formatted_time = '';

		if ( $type == 1 ) {
			$formatted_time = self::getTweetHumanReadableTime( $utimestamp );
		} else {
			$formatted_time = self::getTweetRegularTime( $utimestamp, $date_time_format );
		}

		return $formatted_time;
	}//end method getTweetTime

	/**
	 * Get Related time from a timestamp (wordpress has a similar function human_time_diff())
	 *
	 * @param $utimestamp
	 * @param $format
	 *
	 * @return string
	 */
	public static function getTweetHumanReadableTime( $utimestamp ) {
		return sprintf( _x( '%s ago', '%s = human-readable time difference', 'cbxlatesttweets' ),
			human_time_diff( $utimestamp, time() ) );
	}

	/**
	 * Get Related time from a timestamp (wordpress has a similar function human_time_diff())
	 *
	 * @param $utimestamp
	 * @param $format
	 *
	 * @return string
	 */
	public static function getTweetRelativeTime( $utimestamp ) {
		$reltime = '';
		if ( isset( $utimestamp ) && $utimestamp !== false ) {

			$duration = Array( 60, 60, 24, 7, 4.35, 12, 10 );
			$gap      = ( time() - $utimestamp );
			if ( $gap > 0 ) {

				$end = esc_html__( 'ago', 'cbxlatesttweets' );
			} else {
				$gap = - $gap;
				$end = esc_html__( 'to go', 'cbxlatesttweets' );
			}
			for ( $i = 0; $gap >= $duration[ $i ]; $i ++ ) {
				if ( $duration[ $i ] == 0 ) {
					$gap = 0;
					break;
					//return '';
				} else {
					$gap /= $duration[ $i ];
				}

				$gap = round( $gap );
			}

			switch ( $i ) {
				case 0:
					$reltime = sprintf( _nx( '%s second',
							'%s seconds',
							$gap,
							'relative time - x min ago',
							'cbxlatesttweets' ),
							$gap ) . ' ' . $end;
					break;

				case 1:
					$reltime = sprintf( _nx( '%s minute',
							'%s minutes',
							$gap,
							'relative time - x min ago',
							'cbxlatesttweets' ),
							$gap ) . ' ' . $end;
					break;

				case 2:
					$reltime = sprintf( _nx( '%s hour',
							'%s hours',
							$gap,
							'relative time - x min ago',
							'cbxlatesttweets' ),
							$gap ) . ' ' . $end;
					break;
				case 3:
					$reltime = sprintf( _nx( '%s day',
							'%s days',
							$gap,
							'relative time - x min ago',
							'cbxlatesttweets' ),
							$gap ) . ' ' . $end;
					break;

				case 4:
					$reltime = sprintf( _nx( '%s week',
							'%s weeks',
							$gap,
							'relative time - x min ago',
							'cbxlatesttweets' ),
							$gap ) . ' ' . $end;
					break;

				case 5:
					$reltime = sprintf( _nx( '%s month',
							'%s months',
							$gap,
							'relative time - x min ago',
							'cbxlatesttweets' ),
							$gap ) . ' ' . $end;
					break;
				case 6:
					$reltime = sprintf( _nx( '%s year',
							'%s years',
							$gap,
							'relative time - x min ago',
							'cbxlatesttweets' ),
							$gap ) . ' ' . $end;
					break;

				case 7:
					$reltime = sprintf( _nx( '%s decade',
							'%s decades',
							$gap,
							'relative time - x min ago',
							'cbxlatesttweets' ),
							$gap ) . ' ' . $end;
					break;
			}

			return $reltime;
		} else {
			return esc_html__( 'ago', 'cbxlatesttweets' );
		}
	}//end method getTweetRelativeTime

	/**
	 * Returns wordpress admin set date and time format
	 *
	 * @param $utimestamp
	 * @param $date_time_format
	 *
	 * @return false|string
	 */
	public static function getTweetRegularTime( $utimestamp, $date_time_format ) {
		if ( $date_time_format == '' ) {
			$date_time_format = get_option( 'date_format' ) . '' . get_option( 'time_format' );
		}


		return date( $date_time_format, $utimestamp );
	}//end method getTweetRegularTime

	/**
	 * Return date time format from global setting
	 *
	 * @return string
	 */
	public static function getGlobalDateTimeFormat() {
		return get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	}//end method getGlobalDateTimeFormat

	/**
	 * Delete all options created by this plugin
	 */
	public static function deleteAllOptionNames() {
		$option_prefix = 'cbxlatesttweets_';

		$option_values = CBXLatestTweetsHelper::getAllOptionNames();

		foreach ( $option_values as $key => $accounting_option_value ) {
			delete_option( $accounting_option_value['option_name'] );
		}

		do_action( 'cbxlatesttweets_plugin_option_delete' );
	}//end method deleteAllOptionNames

	/**
	 * Delete all transient caches
	 */
	public static function deleteAllTransientsNames() {
		$transients_values = CBXLatestTweetsHelper::getAllTransients();
		foreach ( $transients_values as $transients_value ) {
			$option_name = $transients_value['option_name'];

			//example:  transient option name '_transient_cbxlatesttweets-codeboxr-10-0'
			$cache_key = str_replace( "_transient_", "", $option_name );
			delete_transient( $cache_key );

		}
		do_action( 'cbxlatesttweets_plugin_transients_delete' );
	}//end method deleteAllTransientsNames

	/**
	 * Enqueue the style
	 */
	public static function enqueue_styles( $layout = 'basic' ) {
		wp_enqueue_style( 'cbxlatesttweets-grid' );
		wp_enqueue_style( 'cbxlatesttweets-public' );
		
		do_action('cbxlatesttweets_enqueue_per_layout', $layout);

	}//end method enqueue_styles


	/**
	 * Display tweets using custom function
	 *
	 * @param array $attr
	 */
	public static function cbxlatesttweets_display( $attr = array() ) {
		if ( is_array( $attr ) && sizeof( $attr ) > 0 ) {
			if ( ! isset( $attr['scope'] ) ) {
				$attr['scope'] = 'directfn';
			}


			$attr = apply_filters( 'cbxbusinesshours_shortcode_builder_attr', $attr );

			$attr_html = '';

			foreach ( $attr as $key => $value ) {
				$attr_html .= ' ' . $key . '="' . $value . '" ';
			}

			echo do_shortcode( '[cbxlatesttweets ' . $attr_html . ']' );
		}
	}//end method cbxlatesttweets_display

	/**
	 * Is gutenberg edit page
	 *
	 * @return bool
	 */
	public static function is_gutenberg_page() {
		//if(!is_admin()) return false;
		if ( function_exists( 'is_gutenberg_page' ) &&
		     is_gutenberg_page()
		) {
			// The Gutenberg plugin is on.
			return true;
		}

		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) &&    $current_screen->is_block_editor()) {
			// Gutenberg page on 5+.
			return true;
		}
		return false;
	}//end is_gutenberg_page

	/**
	 * Checks if the current request is a WP REST API request.
	 *
	 * Case #1: After WP_REST_Request initialisation
	 * Case #2: Support "plain" permalink settings
	 * Case #3: It can happen that WP_Rewrite is not yet initialized,
	 *          so do this (wp-settings.php)
	 * Case #4: URL Path begins with wp-json/ (your REST prefix)
	 *          Also supports WP installations in subfolders
	 *
	 * @returns boolean
	 * @author matzeeable
	 */
	public static function is_rest() {
		$prefix = rest_get_url_prefix( );
		if (defined('REST_REQUEST') && REST_REQUEST // (#1)
		    || isset($_GET['rest_route']) // (#2)
		       && strpos( trim( $_GET['rest_route'], '\\/' ), $prefix , 0 ) === 0)
			return true;
		// (#3)
		global $wp_rewrite;
		if ($wp_rewrite === null) $wp_rewrite = new WP_Rewrite();

		// (#4)
		$rest_url = wp_parse_url( trailingslashit( rest_url( ) ) );
		$current_url = wp_parse_url( add_query_arg( array( ) ) );
		return strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
	}//end is_rest

	/**
	 * Add utm params to any url
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function url_utmy( $url = '' ) {
		if ( $url == '' ) {
			return $url;
		}

		$url = add_query_arg( array(
			'utm_source'   => 'plgsidebarinfo',
			'utm_medium'   => 'plgsidebar',
			'utm_campaign' => 'wpfreemium',
		), $url );

		return $url;
	}//end url_utmy

}//end method CBXLatestTweetsHelper