<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/includes
 * @author     Codeboxr <info@codeboxr.com>
 */
class CBXLatestTweets {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      CBXLatestTweets_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->version     = CBXLATESTTWEETS_PLUGIN_VERSION;
		$this->plugin_name = CBXLATESTTWEETS_PLUGIN_NAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - CBXLatestTweets_Loader. Orchestrates the hooks of the plugin.
	 * - CBXLatestTweets_i18n. Defines internationalization functionality.
	 * - CBXLatestTweets_Admin. Defines all hooks for the admin area.
	 * - CBXLatestTweets_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxlatesttweets-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxlatesttweets-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxlatesttweets-settings.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cbxlatesttweets-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxlatesttweets-helper.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cbxlatesttweets-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cbxlatesttweets-public.php';


		/**
		 * Widgets
		 * of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/cbxlatesttweets-widget/cbxlatesttweets-widget.php';


		$this->loader = new CBXLatestTweets_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the CBXLatestTweets_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new CBXLatestTweets_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
        global $wp_version;

		$plugin_admin = new CBXLatestTweets_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'plugin_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'setting_init' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//full reset of plugin
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'cbxlatesttweetssettings' && isset( $_REQUEST['cbxlatesttweets_fullreset'] ) && $_REQUEST['cbxlatesttweets_fullreset'] == 1 ) {
			$this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_fullreset' );
		}

		//full reset of transient cache data
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'cbxlatesttweetssettings' && isset( $_REQUEST['cbxlatesttweets_cachereset'] ) && $_REQUEST['cbxlatesttweets_cachereset'] == 1 ) {
			$this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_cachereset' );
		}

		//add plugin settings link in plugin listing
		$this->loader->add_filter( 'plugin_action_links_' . CBXLATESTTWEETS_BASE_NAME, $plugin_admin, 'plugin_action_links' );
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'plugin_row_meta',10, 4 );
		$this->loader->add_action( 'upgrader_process_complete', $plugin_admin, 'plugin_upgrader_process_complete', 10, 2 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'plugin_activate_upgrade_notices' );

		//gutenberg
		$this->loader->add_action( 'init', $plugin_admin, 'gutenberg_blocks' );

		//$this->loader->add_filter( 'block_categories', $plugin_admin, 'gutenberg_block_categories', 10, 2 );
        //gutenberg blocks
        if ( version_compare($wp_version,'5.8') >= 0) {
            $this->loader->add_filter( 'block_categories_all', $plugin_admin, 'gutenberg_block_categories', 10, 2 );
        }
        else{
            $this->loader->add_filter( 'block_categories', $plugin_admin, 'gutenberg_block_categories', 10, 2 );
        }

		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_block_editor_assets' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
        global $wp_version;

		$plugin_public = new CBXLatestTweets_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'init_shortcodes' ); //shortcode init

		//widget
		$this->loader->add_action( 'widgets_init', $plugin_public, 'register_widget' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		//elementor
		$this->loader->add_action( 'elementor/widgets/widgets_registered', $plugin_public, 'init_elementor_widgets' );
		$this->loader->add_action( 'elementor/elements/categories_registered', $plugin_public, 'add_elementor_widget_categories' );
		$this->loader->add_action( 'elementor/editor/before_enqueue_scripts', $plugin_public, 'elementor_before_enqueue_scripts', 99999 );
		$this->loader->add_action( 'elementor/editor/before_enqueue_styles', $plugin_public, 'elementor_before_enqueue_styles', 99999 );


		$this->loader->add_action( 'cbxlatesttweets_early_enqueue_style', $plugin_public, 'elementor_early_enqueue_style', 99999 );
		$this->loader->add_action( 'cbxlatesttweets_early_enqueue_script', $plugin_public, 'elementor_early_enqueue_script', 99999 );

		//visual composer widget
		$this->loader->add_action( 'vc_before_init', $plugin_public, 'vc_before_init_actions' );


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    CBXLatestTweets_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}//end class CBXLatestTweets