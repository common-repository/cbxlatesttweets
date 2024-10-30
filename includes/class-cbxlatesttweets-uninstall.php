<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Fired during plugin uninstall
 *
 * @link       codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's uninstallation.
 *
 * @since      1.0.0
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/includes
 * @author     CBX Team  <info@codeboxr.com>
 */
class CBXLatestTweets_Uninstall {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		$settings = new CBXLatestTweets_Settings_API();

		$delete_global_config = $settings->get_option( 'delete_global_config', 'cbxlatesttweets_tools', 'no' );

		if ( $delete_global_config == 'yes' ) {

			$option_prefix = 'cbxlatesttweets_';

			//delete plugin global options

			CBXLatestTweetsHelper::deleteAllOptionNames();
			CBXLatestTweetsHelper::deleteAllTransientsNames();

			do_action( 'cbxlatesttweets_plugin_uninstall', $option_prefix );
		}
	}//end uninstall
}//end class CBXLatestTweets_Uninstall
