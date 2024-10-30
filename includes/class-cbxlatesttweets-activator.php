<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/includes
 * @author     Codeboxr <info@codeboxr.com>
 */
class CBXLatestTweets_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		set_transient( 'cbxlatesttweets_activated_notice', 1 );
	}

}//end class CBXLatestTweets_Activator
