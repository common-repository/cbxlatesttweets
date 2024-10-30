<?php
// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}
	
	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @link       https://www.codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXLatestTweets
	 * @subpackage CBXLatestTweets/admin
	 */
	
	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    CBXLatestTweets
	 * @subpackage CBXLatestTweets/admin
	 * @author     Codeboxr <info@codeboxr.com>
	 */
	class CBXLatestTweets_Admin {
		
		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $plugin_name The ID of this plugin.
		 */
		private $plugin_name;
		
		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $version The current version of this plugin.
		 */
		private $version;
		
		private $settings_api;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param string $plugin_name The name of this plugin.
		 * @param string $version The version of this plugin.
		 *
		 * @since    1.0.0
		 *
		 */
		public function __construct( $plugin_name, $version ) {
			
			$this->plugin_name = $plugin_name;
			$this->version     = $version;
			
			//get instance of setting api
			$this->settings_api = new CBXLatestTweets_Settings_API();
			
		}
		
		/**
		 * Initialize setting
		 */
		public function setting_init() {
			//set the settings
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );
			//initialize settings
			$this->settings_api->admin_init();
		}
		
		
		/**
		 * Global Setting Sections and titles
		 *
		 * @return type
		 */
		public function get_settings_sections() {
			$settings_sections = array(
				
				array(
					'id'    => 'cbxlatesttweets_api_config',
					'title' => esc_html__( 'Twitter API Config', 'cbxlatesttweets' ),
				),
				
				array(
					'id'    => 'cbxlatesttweets_config',
					'title' => esc_html__( 'Default Tweets Config', 'cbxlatesttweets' ),
				),
				array(
					'id'    => 'cbxlatesttweets_tools',
					'title' => esc_html__( 'Tools', 'cbxlatesttweets' ),
				),
			
			);
			
			return apply_filters( 'cbxlatesttweets_setting_sections', $settings_sections );
		}
		
		/**
		 * Global Setting Fields
		 *
		 * @return array
		 */
		public function get_settings_fields() {
			
			global $wpdb;
			
			$reset_data_link  = add_query_arg( 'cbxlatesttweets_fullreset', 1,
				admin_url( 'options-general.php?page=cbxlatesttweetssettings' ) );
			$reset_cache_link = add_query_arg( 'cbxlatesttweets_cachereset', 1,
				admin_url( 'options-general.php?page=cbxlatesttweetssettings' ) );
			
			$option_values     = CBXLatestTweetsHelper::getAllOptionNames();
			$transients_values = CBXLatestTweetsHelper::getAllTransients();
			
			$table_data_html = $table_cache_html = '';

			$table_data_html .= '<p style="margin-bottom: 20px;"><a class="button button-primary" id="cbxlatesttweets_info_trig" href="#">' . esc_html__( 'Show/hide details',	'cbxlatesttweets' ) . '</a></p>';
			$table_data_html .= '<div id="cbxlatesttweets_resetinfo" style="display: none;">';
			
			$table_data_html .= '<p style="margin-bottom: 15px;" id="cbxlatesttweets_tool_fullreset"><strong>' . esc_html__( 'Following option values created by this plugin(including addon)','cbxlatesttweets' ) . '</strong></p>';
			
			$table_data_html .= '<table class="widefat widethin" id="cbxlatesttweets_table_data">
	<thead>
	<tr>
		<th class="row-title">' . esc_attr__( 'Option Name', 'cbxlatesttweets' ) . '</th>
		<th>' . esc_attr__( 'Option ID', 'cbxlatesttweets' ) . '</th>
		<th>' . esc_attr__( 'Data', 'cbxlatesttweets' ) . '</th>
	</tr>
	</thead>
	<tbody>';
			
			
			$i = 0;
			foreach ( $option_values as $key => $value ) {
				$alternate_class = ( $i % 2 == 0 ) ? 'alternate' : '';
				$i ++;
				//$table_data_html .= '<p>' .  $value['option_name'] . ' - ' . $value['option_id'] . ' - (<code style="overflow-wrap: break-word; word-break: break-all;">' . $value['option_value'] . '</code>)</p>';
				
				$table_data_html .= '<tr class="' . esc_attr( $alternate_class ) . '">
									<td class="row-title"><label for="tablecell">' . esc_attr( $value['option_name'] ) . '</label></td>
									<td>' . esc_attr( $value['option_id'] ) . '</td>
									<td><code style="overflow-wrap: break-word; word-break: break-all;">' . $value['option_value'] . '</code></td>
								</tr>';
				
			}
			
			$table_data_html .= '</tbody>
	<tfoot>
	<tr>
		<th class="row-title">' . esc_attr__( 'Option Name', 'cbxlatesttweets' ) . '</th>
		<th>' . esc_attr__( 'Option ID', 'cbxlatesttweets' ) . '</th>
		<th>' . esc_attr__( 'Data', 'cbxlatesttweets' ) . '</th>
	</tr>
	</tfoot>
</table>';
			
			$table_cache_html .= '<p id="cbxlatesttweets_tool_cachereset"><strong>' . esc_html__( 'Following cache values created by this plugin(including addon)',
					'cbxlatesttweets' ) . '</strong></p>';
			
			
			$table_counter = 1;
			foreach ( $transients_values as $value ) {
				$table_cache_html .= '<p>' . str_pad( $table_counter, 2, '0',
						STR_PAD_LEFT ) . '. ' . $value['option_id'] . ' - <code>' . $value['option_name'] . '</code></p>';
				
				$table_counter ++;
			}

			$table_data_html .= '</div>';
			
			$settings_builtin_fields =
				array(
					'cbxlatesttweets_api_config' => array(
						'api_help'            => array(
							'name'    => 'api_help',
							'label'   => esc_html__( 'Api Help', 'cbxlatesttweets' ),
							'desc'    => __( 'Please create app in <a href="https://apps.twitter.com/" target="_blank">Twitter Apps</a> and check <strong>Keys and Access Tokens</strong> tab for necessary api keys',
								'cbxlatesttweets' ),
							'type'    => 'heading',
							'default' => '',
						),
						'consumer_key'        => array(
							'name'    => 'consumer_key',
							'label'   => esc_html__( 'Consumer Key', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Please put Consumer Key here, required field.',
								'cbxlatesttweets' ),
							'type'    => 'text',
							'default' => '',
						),
						'consumer_secret'     => array(
							'name'    => 'consumer_secret',
							'label'   => esc_html__( 'Consumer Secret', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Please put Consumer Secret here, required field.',
								'cbxlatesttweets' ),
							'type'    => 'text',
							'default' => '',
						),
						'access_token'        => array(
							'name'    => 'access_token',
							'label'   => esc_html__( ' Access Token', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Please put  Access Token here, required field.',
								'cbxlatesttweets' ),
							'type'    => 'text',
							'default' => '',
						),
						'access_token_secret' => array(
							'name'    => 'access_token_secret',
							'label'   => esc_html__( ' Access Token Secret', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Please put  Access Token Secret here, required field.',
								'cbxlatesttweets' ),
							'type'    => 'text',
							'default' => '',
						),
					),
					'cbxlatesttweets_config'     => array(
						'include_rts'      => array(
							'name'    => 'include_rts',
							'label'   => esc_html__( 'Include Retweets', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Will retweets be included when query tweets?',
								'cbxlatesttweets' ),
							'type'    => 'radio',
							'default' => 0,
							'options' => array(
								1 => esc_html__( 'Yes', 'cbxlatesttweets' ),
								0 => esc_html__( 'No', 'cbxlatesttweets' ),
							),
						
						),
						'exclude_replies'  => array(
							'name'    => 'exclude_replies',
							'label'   => esc_html__( 'Exclude Replies', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Will replies be included when query tweets?', 'cbxlatesttweets' ),
							'type'    => 'radio',
							'default' => 1,
							'options' => array(
								1 => esc_html__( 'Yes', 'cbxlatesttweets' ),
								0 => esc_html__( 'No', 'cbxlatesttweets' ),
							),
						
						),
						'time_format'      => array(
							'name'    => 'time_format',
							'label'   => esc_html__( 'Default time format', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'What will be the time format to show tweet?', 'cbxlatesttweets' ),
							'type'    => 'radio',
							'options' => array(
								1 => esc_html__( 'Relative (Example: 5 mins ago)', 'cbxlatesttweets' ),
								0 => esc_html__( 'Regular (Example: dd-mm-yyyy)', 'cbxlatesttweets' ),
							),
							'default' => 1,
						),
						'date_time_format' => array(
							'name'    => 'date_time_format',
							'label'   => esc_html__( 'Date time display format', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'If time format is regular then date time display format.',
									'cbxlatesttweets' ) . ' <a  target="_blank" href="https://codex.wordpress.org/Formatting_Date_and_Time">' . esc_html__( 'Documentation on date and time formatting',
									'cbxlatesttweets' ) . '</a>',
							'type'    => 'text',
							//'default'  => get_option('date_format'). ' '.get_option('time_format')
							'default' => CBXLatestTweetsHelper::getGlobalDateTimeFormat(),
						),
						'linkify_link'     => array(
							'name'    => 'linkify_link',
							'label'   => esc_html__( 'Linkify Link', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Linkify any link found in tweet text', 'cbxlatesttweets' ),
							'type'    => 'radio',
							'options' => array(
								1 => esc_html__( 'Yes', 'cbxlatesttweets' ),
								0 => esc_html__( 'No', 'cbxlatesttweets' ),
							),
							'default' => 1,
						),
						'linkify_mention'  => array(
							'name'    => 'linkify_mention',
							'label'   => esc_html__( 'Linkify mention', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Linkify mention of the tweet.', 'cbxlatesttweets' ),
							'type'    => 'radio',
							'options' => array(
								1 => esc_html__( 'Yes', 'cbxlatesttweets' ),
								0 => esc_html__( 'No', 'cbxlatesttweets' ),
							),
							'default' => 1,
						),
						'linkify_hashtag'  => array(
							'name'    => 'linkify_hashtag',
							'label'   => esc_html__( 'Linkify hashtag', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Linkify hashtag of the tweet.', 'cbxlatesttweets' ),
							'type'    => 'radio',
							'options' => array(
								1 => esc_html__( 'Yes', 'cbxlatesttweets' ),
								0 => esc_html__( 'No', 'cbxlatesttweets' ),
							),
							'default' => 1,
						),
						'enable_cache'     => array(
							'name'    => 'enable_cache',
							'label'   => esc_html__( 'Enable cache', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'Enable caching of tweets', 'cbxlatesttweets' ),
							'type'    => 'radio',
							'options' => array(
								1 => esc_html__( 'Yes', 'cbxlatesttweets' ),
								0 => esc_html__( 'No', 'cbxlatesttweets' ),
							),
							'default' => 1,
						),
						'cache_time'       => array(
							'name'    => 'cache_time',
							'label'   => esc_html__( 'Default cache time (hour)', 'cbxlatesttweets' ),
							'desc'    => esc_html__( 'How many hour tweets wil be cached?', 'cbxlatesttweets' ),
							'type'    => 'number',
							'default' => 2,
						),
					
					),
					'cbxlatesttweets_tools'      => array(
						'delete_global_config' => array(
							'name'    => 'delete_global_config',
							'label'   => esc_html__( 'On Uninstall delete plugin data', 'cbxlatesttweets' ),
							'desc'    => '<p>' . __( 'Delete Global Config data created by this plugin on uninstall.',
									'cbxlatesttweets' ) . ' ' . __( 'Details <a data-target="cbxlatesttweets_tool_fullreset" class="cbxlatesttweets_jump" href="#">option values</a> and <a data-target="cbxlatesttweets_tool_cachereset" class="cbxlatesttweets_jump" href="#">transient values</a> ',
									'cbxlatesttweets' ) . '</p>' . '<p>' . __( '<strong>Please note that this process can not be undone and it is recommended to keep full database backup before doing this.</strong>',
									'cbxlatesttweets' ) . '</p>',
							'type'    => 'radio',
							'options' => array(
								'yes' => esc_html__( 'Yes', 'cbxlatesttweets' ),
								'no'  => esc_html__( 'No', 'cbxlatesttweets' ),
							),
							'default' => 'no',
						),
						'reset_data'           => array(
							'name'    => 'reset_data',
							'label'   => esc_html__( 'Reset all data', 'cbxlatesttweets' ),
							'desc'    => sprintf( __( 'Reset option values created by this plugin.
<a class="button button-primary" onclick="return confirm(\'%s\')" href="%s">Reset Data</a>',
									'cbxlatesttweets' ),
									esc_html__( 'Are you sure to reset all data, this process can not be undone?',
										'cbxlatesttweets' ),
									$reset_data_link ) . $table_data_html,
							'type'    => 'html',
							'default' => 'off',
						),
						'reset_transients'     => array(
							'name'    => 'reset_transients',
							'label'   => esc_html__( 'Reset all cache data', 'cbxlatesttweets' ),
							'desc'    => sprintf( __( 'Reset cache data created created by this plugin.
<a class="button button-primary" onclick="return confirm(\'%s\')" href="%s">Reset Cache</a>',
									'cbxlatesttweets' ),
									esc_html__( 'Are you sure to reset all data, this process can not be undone?',
										'cbxlatesttweets' ),
									$reset_cache_link ) . $table_cache_html,
							'type'    => 'html',
							'default' => 'off',
						),
					
					
					),
				);
			
			$settings_fields = array(); //final setting array that will be passed to different filters
			
			$sections = $this->get_settings_sections();
			
			
			foreach ( $sections as $section ) {
				if ( ! isset( $settings_builtin_fields[ $section['id'] ] ) ) {
					$settings_builtin_fields[ $section['id'] ] = array();
				}
			}
			
			foreach ( $sections as $section ) {
				$settings_builtin_fields_section_id = $settings_builtin_fields[ $section['id'] ];
				$settings_fields[ $section['id'] ]  = apply_filters( 'cbxlatesttweets_global_' . $section['id'] . '_fields',
					$settings_builtin_fields_section_id );
			}
			
			
			$settings_fields = apply_filters( 'cbxlatesttweets_global_fields',
				$settings_fields ); //final filter if need
			
			return $settings_fields;
		}
		
		/**
		 * Register the administration menu for this plugin into the WordPress Dashboard menu.
		 */
		public function plugin_admin_menu() {
			
			add_options_page( esc_html__( 'CBX Latest Tweets Configuration', 'cbxlatesttweets' ),
				esc_html__( 'CBX Latest Tweets', 'cbxlatesttweets' ),
				'manage_options',
				'cbxlatesttweetssettings',
				array(
					$this,
					'display_plugin_admin_page',
				) );
		}
		
		/**
		 * Render the settings page for this plugin.
		 *
		 * @since    1.0.0
		 */
		public function display_plugin_admin_page() {
			$doc = isset($_REQUEST['cbxlatesttweets-help-support'])? absint($_REQUEST['cbxlatesttweets-help-support']) : 0;

			if($doc){
				include( 'templates/dashboard.php' );
			}
			else{
				include( 'templates/settings-display.php' );
			}
		}
		
		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles( $hook) {
			$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';
			if ( $page == 'cbxlatesttweetssettings' ) {
				wp_register_style( 'select2', plugin_dir_url( __FILE__ ) . '../assets/js/select2/css/select2.min.css',
					array(), $this->version );
				wp_register_style( 'cbxlatesttweets-setting',
					plugin_dir_url( __FILE__ ) . '../assets/css/cbxlatesttweets-setting.css',
					array( 'select2', 'wp-color-picker' ), $this->version, 'all' );
				wp_register_style( 'cbxlatesttweets-admin',
					plugin_dir_url( __FILE__ ) . '../assets/css/cbxlatesttweets-admin.css', array(), $this->version,
					'all' );
				
				
				wp_enqueue_style( 'select2' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'cbxlatesttweets-setting' );
				wp_enqueue_style( 'cbxlatesttweets-admin' );
				
			}
			if ( $page == 'cbxlatesttweetssettings' || $page == 'cbxlatesttweetssettings&doc=1') {
				wp_register_style( 'cbxlatesttweets-branding', plugin_dir_url( __FILE__ ) . '../assets/css/cbxlatesttweets-branding.css',
					array(),
					$this->version );
				wp_enqueue_style( 'cbxlatesttweets-branding' );
			}
			
		}//end method enqueue_styles
		
		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts( $hook = '' ) {
			if ( $hook == 'settings_page_cbxlatesttweetssettings' ) {
				wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . '../assets/js/select2/js/select2.min.js',
					array( 'jquery' ), $this->version, true );
				wp_register_script( 'cbxlatesttweets-setting',
					plugin_dir_url( __FILE__ ) . '../assets/js/cbxlatesttweets-setting.js',
					array( 'jquery', 'select2' ), $this->version, true );
				
				$cbxlatesttweets_setting_js_vars = apply_filters( 'cbxlatesttweets_setting_js_vars',
					array(
						'please_select' => esc_html__( 'Please Select', 'cbxlatesttweets' ),
						'upload_title'  => esc_html__( 'Window Title', 'cbxlatesttweets' ),
					) );
				wp_localize_script( 'cbxlatesttweets-setting', 'cbxlatesttweets_setting',
					$cbxlatesttweets_setting_js_vars );
				
				wp_enqueue_media();
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'select2' );
				
				wp_enqueue_script( 'cbxlatesttweets-setting' );
				
			}
			
			
		}//end method enqueue_scripts
		
		/**
		 * Full plugin reset and redirect
		 */
		public function plugin_fullreset() {
			
			$option_prefix = 'cbxlatesttweets_';
			
			CBXLatestTweetsHelper::deleteAllOptionNames();
			CBXLatestTweetsHelper::deleteAllTransientsNames();
			
			
			// create plugin's core table tables
			activate_cbxlatesttweets();
			
			
			//3rd party plugin's table creation
			do_action( 'cbxlatesttweets_plugin_reset', $option_prefix );
			
			
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );
			$this->settings_api->admin_init();
			
			wp_safe_redirect( admin_url( 'options-general.php?page=cbxlatesttweetssettings#cbxlatesttweets_tools' ) );
			exit();
		}//end method plugin_fullreset
		
		/**
		 * Reset cache or transient caches
		 */
		public function plugin_cachereset() {
			CBXLatestTweetsHelper::deleteAllTransientsNames();
			
			wp_safe_redirect( admin_url( 'options-general.php?page=cbxlatesttweetssettings#cbxlatesttweets_tools' ) );
			exit();
		}//end method plugin_cachereset
		
		
		/**
		 * If we need to do something in upgrader process is completed
		 *
		 * @param $upgrader_object
		 * @param $options
		 */
		public function plugin_upgrader_process_complete( $upgrader_object, $options ) {
			if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
				foreach ( $options['plugins'] as $each_plugin ) {
					if ( $each_plugin == CBXLATESTTWEETS_BASE_NAME ) {
						set_transient( 'cbxlatesttweets_upgraded_notice', 1 );
						break;
					}
				}
			}
			
		}//end plugin_upgrader_process_complete
		
		/**
		 * Show a notice to anyone who has just installed the plugin for the first time
		 * This notice shouldn't display to anyone who has just updated this plugin
		 */
		public function plugin_activate_upgrade_notices() {
			// Check the transient to see if we've just activated the plugin
			if ( get_transient( 'cbxlatesttweets_activated_notice' ) ) {
				echo '<div style="border-left-color:#02b7ea;" class="notice notice-success is-dismissible">';
				echo '<p><img style="float: left; display: inline-block; margin-right: 15px;" src="' . CBXLATESTTWEETS_ROOT_URL . 'assets/images/icon_48.png' . '"/>' . sprintf( __( 'Thanks for installing/deactivating <strong>CBX Latest Tweets</strong> V%s - Codeboxr Team',
						'cbxlatesttweets' ), CBXLATESTTWEETS_PLUGIN_VERSION ) . '</p>';
				echo '<p>' . sprintf( __( 'Check <a href="%s">Plugin Setting</a> | <a href="%s" target="_blank"><span class="dashicons dashicons-external"></span> Documentation</a>',
						'cbxlatesttweets' ), admin_url( 'options-general.php?page=cbxlatesttweetssettings' ),
						'https://codeboxr.com/product/cbx-flexible-event-countdown-for-wordpress/' ) . '</p>';
				echo '</div>';
				// Delete the transient so we don't keep displaying the activation message
				delete_transient( 'cbxlatesttweets_activated_notice' );
				
			}
			
			// Check the transient to see if we've just activated the plugin
			if ( get_transient( 'cbxlatesttweets_upgraded_notice' ) ) {
				echo '<div style="border-left-color:#02b7ea;" class="notice notice-success is-dismissible">';
				echo '<p><img style="float: left; display: inline-block; margin-right: 15px;" src="' . CBXLATESTTWEETS_ROOT_URL . 'assets/images/icon_48.png' . '"/>' . sprintf( __( 'Thanks for upgrading <strong>CBX Latest Tweets</strong> V%s , enjoy the new features and bug fixes - Codeboxr Team',
						'cbxlatesttweets' ), CBXLATESTTWEETS_PLUGIN_VERSION ) . '</p>';
				echo '<p>' . sprintf( __( 'Check <a href="%s">Plugin Setting</a> | <a href="%s" target="_blank"><span class="dashicons dashicons-external"></span> Documentation</a>',
						'cbxlatesttweets' ), admin_url( 'options-general.php?page=cbxlatesttweetssettings' ),
						'https://codeboxr.com/product/cbx-flexible-event-countdown-for-wordpress/' ) . '</p>';
				echo '</div>';
				// Delete the transient so we don't keep displaying the activation message
				delete_transient( 'cbxlatesttweets_upgraded_notice' );
			}
		}//end plugin_activate_upgrade_notices
		
		/**
		 * Init all gutenberg blocks
		 */
		public function gutenberg_blocks() {
			
			$include_rts     = intval( $this->settings_api->get_option( 'include_rts', 'cbxlatesttweets_config', 0 ) );
			$exclude_replies = intval( $this->settings_api->get_option( 'exclude_replies', 'cbxlatesttweets_config',
				1 ) );
			
			$time_format      = intval( $this->settings_api->get_option( 'time_format', 'cbxlatesttweets_config', 1 ) );
			$date_time_format = $this->settings_api->get_option( 'date_time_format', 'cbxlatesttweets_config',
				CBXLatestTweetsHelper::getGlobalDateTimeFormat() );
			
			$include_rts     = ( $include_rts == 1 ) ? true : false;
			$exclude_replies = ( $exclude_replies == 1 ) ? true : false;
			
			
			$layouts         = CBXLatestTweetsHelper::get_layouts_p();
			$layouts_options = array();
			foreach ( $layouts as $key => $value ) {
				$layouts_options[] = array(
					'label' => esc_attr( $value ),
					'value' => esc_attr( $key ),
				);
			}
			
			$time_formats   = array();
			$time_formats[] = array(
				'label' => esc_html__( 'Relative (Example: 5 mins ago)', 'cbxlatesttweets' ),
				'value' => 1,
			);
			$time_formats[] = array(
				'label' => esc_html__( 'Regular (Example: dd-mm-yyyy)', 'cbxlatesttweets' ),
				'value' => 0,
			);
			
			
			wp_register_script( 'cbxlatesttweets-block',
				plugin_dir_url( __FILE__ ) . '../assets/js/cbxlatesttweets-block.js',
				array(
					'wp-blocks',
					'wp-element',
					'wp-components',
					'wp-editor',
				),
				filemtime( plugin_dir_path( __FILE__ ) . '../assets/js/cbxlatesttweets-block.js' ) );
			
			/*wp_register_style( 'cbxlatesttweets-grid',
				plugin_dir_url( __FILE__ ) . '../assets/css/cbxlatesttweets-grid.css', array(), $this->version, 'all' );
			wp_register_style( 'cbxlatesttweets-public',
				plugin_dir_url( __FILE__ ) . '../assets/css/cbxlatesttweets-public.css',
				array( 'cbxlatesttweets-grid' ), $this->version, 'all' );*/
			
			wp_register_style( 'cbxlatesttweets-block',
				plugin_dir_url( __FILE__ ) . '../assets/css/cbxlatesttweets-block.css',
				array( 'cbxlatesttweets-grid', 'cbxlatesttweets-public' ),
				filemtime( plugin_dir_path( __FILE__ ) . '../assets/css/cbxlatesttweets-block.css' ) );
			
			$js_vars = apply_filters( 'cbxlatesttweets_block_js_vars',
				array(
					'block_title'      => esc_html__( 'CBX Latest Tweets', 'cbxlatesttweets' ),
					'block_category'   => 'codeboxr',
					'block_icon'       => 'universal-access-alt',
					'general_settings' => array(
						'title'                    => esc_html__( 'CBX Latest Tweets Settings', 'cbxlatesttweets' ),
						'username'                 => esc_html__( 'Twitter Username', 'cbxlatesttweets' ),
						'layout'                   => esc_html__( 'Select Layout', 'cbxlatesttweets' ),
						'layout_options'           => $layouts_options,
						'limit'                    => esc_html__( 'Number of Tweets', 'cbxlatesttweets' ),
						'include_rts'              => esc_html__( 'Include Retweets', 'cbxlatesttweets' ),
						'exclude_replies'          => esc_html__( 'Exclude Replies', 'cbxlatesttweets' ),
						'time_format'              => esc_html__( 'Time Format', 'cbxlatesttweets' ),
						'time_format_options'      => $time_formats,
						'date_time_format'         => esc_html__( 'Date Time Format', 'cbxlatesttweets' ),
						'date_time_format_default' => $date_time_format,
					),
				) );
			
			wp_localize_script( 'cbxlatesttweets-block', 'cbxlatesttweets_block', $js_vars );
			
			register_block_type( 'codeboxr/cbxlatesttweets',
				array(
					'editor_script'   => 'cbxlatesttweets-block',
					'editor_style'    => 'cbxlatesttweets-block',
					'attributes'      => apply_filters( 'cbxlatesttweets_block_attributes',
						array(
							//general
							'username'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'layout'           => array(
								'type'    => 'string',
								'default' => 'basic',
							),
							'limit'            => array(
								'type'    => 'integer',
								'default' => 10,
							),
							'include_rts'      => array(
								'type'    => 'boolean',
								'default' => $include_rts,
							),
							'exclude_replies'  => array(
								'type'    => 'boolean',
								'default' => $exclude_replies,
							),
							'time_format'      => array(
								'type'    => 'integer',
								'default' => $time_format,
							),
							'date_time_format' => array(
								'type'    => 'string',
								'default' => $date_time_format,
							),
						) ),
					'render_callback' => array( $this, 'cbxlatesttweets_block_render' ),
				) );
			
		}//end method gutenberg_blocks
		
		/**
		 * Getenberg server side render
		 *
		 * @param $settings
		 *
		 * @return string
		 */
		public function cbxlatesttweets_block_render( $attributes ) {
			$include_rts     = $this->settings_api->get_option( 'include_rts', 'cbxlatesttweets_config', 0 );
			$exclude_replies = $this->settings_api->get_option( 'exclude_replies', 'cbxlatesttweets_config', 1 );
			
			$time_format      = $this->settings_api->get_option( 'time_format', 'cbxlatesttweets_config', 1 );
			$date_time_format = $this->settings_api->get_option( 'date_time_format', 'cbxlatesttweets_config', CBXLatestTweetsHelper::getGlobalDateTimeFormat() );
			
			
			//convert to boolean
			$include_rts     = ( $include_rts == 1 ) ? 'true' : 'false';
			$exclude_replies = ( $exclude_replies == 1 ) ? 'true' : 'false';
			
			
			$attr = array();
			
			
			$attr['username'] = isset( $attributes['username'] ) ? sanitize_text_field( $attributes['username'] ) : '';
			$attr['layout']   = isset( $attributes['layout'] ) ? sanitize_text_field( $attributes['layout'] ) : 'basic';
			$attr['limit']    = isset( $attributes['limit'] ) ? intval( $attributes['limit'] ) : 10;
			
			//boolean handle
			$attr['include_rts']     = isset( $attributes['include_rts'] ) ? sanitize_text_field( $attributes['include_rts'] ) : $include_rts;
			$attr['exclude_replies'] = isset( $attributes['exclude_replies'] ) ? sanitize_text_field( $attributes['exclude_replies'] ) : $exclude_replies;
			
			$attr['time_format']      = isset( $attributes['time_format'] ) ? intval( $attributes['time_format'] ) : $time_format;
			$attr['date_time_format'] = isset( $attributes['date_time_format'] ) ? sanitize_text_field( $attributes['date_time_format'] ) : $date_time_format;
			
			
			//convert to boolean
			$attr['include_rts']     = ( $attr['include_rts'] == 'true' ) ? 1 : 0;
			$attr['exclude_replies'] = ( $attr['exclude_replies'] == 'true' ) ? 1 : 0;
			
			$attr['scope'] = 'block';
			
			
			$attr = apply_filters( 'cbxlatesttweets_block_shortcode_builder_attr', $attr, $attributes );
			
			$attr_html = '';
			
			foreach ( $attr as $key => $value ) {
				$attr_html .= ' ' . $key . '="' . $value . '" ';
			}

			//we are sure that we are in rest api call
			$do_shortcode = apply_filters('cbxlatesttweets_block_do_shortcode', true, $attr['layout']);

			return ($do_shortcode)? do_shortcode( '[cbxlatesttweets ' . $attr_html . ']' ) : '[cbxlatesttweets ' . $attr_html . ']';
			//return '[cbxlatesttweets ' . $attr_html . ']';
		}//end method cbxlatesttweets_block_render
		
		/**
		 * Register New Gutenberg block Category if need
		 *
		 * @param $categories
		 * @param $post
		 *
		 * @return mixed
		 */
		public function gutenberg_block_categories( $categories, $post ) {
			$found = false;
			
			foreach ( $categories as $category ) {
				if ( $category['slug'] == 'codeboxr' ) {
					$found = true;
					break;
				}
			}
			
			if ( ! $found ) {
				return array_merge(
					$categories,
					array(
						array(
							'slug'  => 'codeboxr',
							'title' => esc_html__( 'CBX Blocks', 'cbxlatesttweets' ),
							//'icon'  => 'wordpress',
						),
					)
				);
			}
			
			return $categories;
		}//end method gutenberg_block_categories
		
		
		/**
		 * Enqueue style for block editor
		 */
		public function enqueue_block_editor_assets() {
			$plugin_public = new CBXLatestTweets_Public( $this->plugin_name, $this->version );

			$plugin_public->enqueue_styles();


			$plugin_public->enqueue_scripts();
		}//end method enqueue_block_editor_assets
		
		
		/**
		 * Show action links on the plugin screen.
		 *
		 * @param   mixed $links Plugin Action links.
		 *
		 * @return  array
		 */
		public function plugin_action_links( $links ) {
			$action_links = array(
				'settings' => '<a style="color: #673ab7 !important;" href="' . admin_url( 'options-general.php?page=cbxlatesttweetssettings' ) . '" aria-label="' . esc_attr__( 'View settings',
						'cbxlatesttweets' ) . '">' . esc_html__( 'Settings', 'cbxlatesttweets' ) . '</a>',
			);
			
			return array_merge( $action_links, $links );
		}//end plugin_action_links
		
		/**
		 * Filters the array of row meta for each/specific plugin in the Plugins list table.
		 * Appends additional links below each/specific plugin on the plugins page.
		 *
		 * @access  public
		 *
		 * @param   array $links_array An array of the plugin's metadata
		 * @param   string $plugin_file_name Path to the plugin file
		 * @param   array $plugin_data An array of plugin data
		 * @param   string $status Status of the plugin
		 *
		 * @return  array       $links_array
		 */
		public function plugin_row_meta( $links_array, $plugin_file_name, $plugin_data, $status ) {
			if ( strpos( $plugin_file_name, CBXLATESTTWEETS_BASE_NAME ) !== false ) {
				$links_array[] = '<a target="_blank" style="color:#673ab7 !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-latest-tweets-for-wordpress/" aria-label="' . esc_attr__( 'Try Pro',
						'cbxlatesttweets' ) . '">' . esc_html__( 'Try Pro', 'cbxlatesttweets' ) . '</a>';
			}
			
			return $links_array;
		}//end plugin_row_meta
		
	}//end method CBXLatestTweets_Admin
