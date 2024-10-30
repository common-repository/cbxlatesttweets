<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.codeboxr.com
 * @since             1.0.0
 * @package           CBXLatestTweets
 *
 * @wordpress-plugin
 * Plugin Name:       CBX Latest Tweets
 * Plugin URI:        https://codeboxr.com/product/cbx-latest-tweets-for-wordpress/
 * Description:       This plugin shows latest tweets from multiple twitter account
 * Version:           1.0.7
 * Author:            Codeboxr
 * Author URI:        https://www.codeboxr.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cbxlatesttweets
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
defined( 'CBXLATESTTWEETS_PLUGIN_NAME' ) or define( 'CBXLATESTTWEETS_PLUGIN_NAME', 'cbxlatesttweets' );
defined( 'CBXLATESTTWEETS_PLUGIN_VERSION' ) or define( 'CBXLATESTTWEETS_PLUGIN_VERSION', '1.0.7' );
defined( 'CBXLATESTTWEETS_BASE_NAME' ) or define( 'CBXLATESTTWEETS_BASE_NAME', plugin_basename( __FILE__ ) );
defined( 'CBXLATESTTWEETS_ROOT_PATH' ) or define( 'CBXLATESTTWEETS_ROOT_PATH', plugin_dir_path( __FILE__ ) );
defined( 'CBXLATESTTWEETS_ROOT_URL' ) or define( 'CBXLATESTTWEETS_ROOT_URL', plugin_dir_url( __FILE__ ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cbxlatesttweets-activator.php
 */
function activate_cbxlatesttweets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxlatesttweets-activator.php';
	CBXLatestTweets_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cbxlatesttweets-deactivator.php
 */
function deactivate_cbxlatesttweets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxlatesttweets-deactivator.php';
	CBXLatestTweets_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-CBXLatestTweets-uninstall.php
 */
function uninstall_cbxlatesttweets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cbxlatesttweets-uninstall.php';
	CBXLatestTweets_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_cbxlatesttweets' );
register_deactivation_hook( __FILE__, 'deactivate_cbxlatesttweets' );
register_uninstall_hook( __FILE__, 'uninstall_cbxlatesttweets' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cbxlatesttweets.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cbxlatesttweets() {

	$plugin = new CBXLatestTweets();
	$plugin->run();

}

run_cbxlatesttweets();