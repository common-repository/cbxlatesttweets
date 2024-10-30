<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/public
 * @author     Codeboxr <info@codeboxr.com>
 */
class CBXLatestTweets_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
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
	 * Init all shortcodes
	 */
	public function init_shortcodes() {
		add_shortcode( 'cbxlatesttweets', array( $this, 'cbxlatesttweets_shortcode' ) );
	}

	/**
	 * Shortcode callback for latest tweets
	 *
	 * @param $atts
	 *
	 * @return false|string
	 */
	public function cbxlatesttweets_shortcode( $atts ) {

		//get the template defined
		$layouts = CBXLatestTweetsHelper::get_layouts();

		$include_rts      = $this->settings_api->get_option( 'include_rts', 'cbxlatesttweets_config', 0 );
		$exclude_replies  = $this->settings_api->get_option( 'exclude_replies', 'cbxlatesttweets_config', 1 );
		$time_format      = $this->settings_api->get_option( 'time_format', 'cbxlatesttweets_config', 1 );
		$date_time_format = $this->settings_api->get_option( 'date_time_format',	'cbxlatesttweets_config', CBXLatestTweetsHelper::getGlobalDateTimeFormat() );


		$atts = shortcode_atts(
			array(
				'scope'            => 'shortcode',
				'limit'            => 10,
				'username'         => '',
				'include_rts'      => $include_rts,
				'exclude_replies'  => $exclude_replies,
				'time_format'      => $time_format,
				'date_time_format' => $date_time_format,
				'layout'           => 'basic',
			),
			$atts,
			'cbxlatesttweets' );


		$layout          = ( isset( $atts['layout'] ) && $atts['layout'] != '' ) ? esc_attr( $atts['layout'] ) : 'basic';
		$scope           = ( isset( $atts['scope'] ) && $atts['scope'] != '' ) ? esc_attr( $atts['scope'] ) : 'shortcode';
		$limit           = intval( $atts['limit'] );
		$username        = esc_attr( $atts['username'] );
		$include_rts     = intval( $atts['include_rts'] );
		$exclude_replies = intval( $atts['exclude_replies'] );

		$time_format      = intval( $atts['time_format'] );
		$date_time_format = $atts['date_time_format'];

		$limit = ( intval( $limit ) > 20 ) ? 20 : $limit; //maximum 20 tweets per page

		$latest_tweets = CBXLatestTweetsHelper::getLatestTweets( $username, $limit, $include_rts, $exclude_replies );

		CBXLatestTweetsHelper::enqueue_styles( $layout );
		$layouts_p_keys = array_keys(CBXLatestTweetsHelper::get_layouts_p());

		// Display the most rated post link
		ob_start();
		//load the template
		if(!in_array($layout, $layouts_p_keys)) $layout = 'basic';
		$layout_include_url = $layouts[ $layout ]['template_dir'];
		include( $layout_include_url );

		$content = ob_get_contents();
		ob_end_clean();

		return $content;

	}//end method cbxlatesttweets_shortcode


	/**
	 * Register Widget
	 */
	public function register_widget() {
		register_widget( "CBXLatestTweetsWidget" ); //form widget
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style( 'cbxlatesttweets-grid',
			plugin_dir_url( __FILE__ ) . '../assets/css/cbxlatesttweets-grid.css',
			array(),
			$this->version,
			'all' );

		wp_register_style( 'cbxlatesttweets-public',
			plugin_dir_url( __FILE__ ) . '../assets/css/cbxlatesttweets-public.css',
			array( 'cbxlatesttweets-grid' ),
			$this->version,
			'all' );
		
		do_action('cbxlatesttweets_enqueue_pro_style');

		do_action('cbxlatesttweets_early_enqueue_style');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		do_action('cbxlatesttweets_enqueue_pro_script');

		do_action('cbxlatesttweets_early_enqueue_script');
	}

	/**
	 * Init elementor widget
	 *
	 * @throws Exception
	 */
	public function init_elementor_widgets() {

		//include the file
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/elementor-elements/class-cbxlatesttweets-elemwidget.php';

		//register the widget
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new CBXLatestTweetsElemWidget\Widgets\CBXLatestTweets_ElemWidget() );
	}//end method widgets_registered

	/**
	 * Add new category to elementor
	 *
	 * @param $elements_manager
	 */
	public function add_elementor_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'codeboxr',
			array(
				'title' => esc_html__( 'Codeboxr Widgets', 'cbxlatesttweets' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}//end method add_elementor_widget_categories

	/**
	 * Load Elementor custom scripts
	 */
	function elementor_before_enqueue_scripts() {
		//register
		//$this->enqueue_scripts();

		//enqueue
		/*
		wp_enqueue_script( 'jquery-newsticker' );
		wp_enqueue_script( 'cbxlatesttweets-vertical-scroll' );


		wp_enqueue_script( 'owl-carousel' );
		wp_enqueue_script( 'cbxlatesttweets-owl-carousel');
		*/

	}//end method elementor_before_enqueue_scripts

	/**
	 * Load Elementor custom scripts
	 */
	function elementor_before_enqueue_styles() {
		wp_register_style( 'cbxlatesttweets_elementor_icon',
			CBXLATESTTWEETS_ROOT_URL . 'widgets/elementor-elements/elementor-icon/icon.css',
			false,
			'1.0.0' );
		wp_enqueue_style( 'cbxlatesttweets_elementor_icon' );

		//register
		//$this->enqueue_styles();

		//enqueue all directly for admin
		/*wp_enqueue_style( 'cbxlatesttweets-grid' );
		wp_enqueue_style( 'cbxlatesttweets-public' );

		wp_enqueue_style( 'cbxlatesttweets-vertical-timeline' );
		wp_enqueue_style( 'cbxlatesttweets-vertical-scroll' );

		wp_enqueue_style( 'owl-carousel' );
		wp_enqueue_style( 'owl-theme-default' );
		wp_enqueue_style( 'cbxlatesttweets-owl-carousel');*/

	}//end method elementor_before_enqueue_styles

	/**
	 * Enqueue css early
	 */
	public function elementor_early_enqueue_style(){
		$elementor_preview = isset($_REQUEST['elementor-preview'])? intval($_REQUEST['elementor-preview']) : 0;
		if($elementor_preview > 0  || (is_admin() && CBXLatestTweetsHelper::is_gutenberg_page())){
			wp_enqueue_style( 'cbxlatesttweets-grid' );
			wp_enqueue_style( 'cbxlatesttweets-public' );
		}
	}//end elementor_early_enqueue_style

	/**
	 *  Enqueue js early
	 */
	public function elementor_early_enqueue_script(){
		$elementor_preview = isset($_REQUEST['elementor-preview'])? intval($_REQUEST['elementor-preview']) : 0;
		if($elementor_preview > 0  || (is_admin() && CBXLatestTweetsHelper::is_gutenberg_page())){
			//
		}
	}//end elementor_early_enqueue_script

	/**
	 * // Before VC Init
	 */
	public function vc_before_init_actions() {
		if ( ! class_exists( 'CBXLatestTweets_WPBWidget' ) ) {
			require_once CBXLATESTTWEETS_ROOT_PATH . 'widgets/vc-elements/class-cbxlatesttweets-wpbwidget.php';
		}
	}// end method vc_before_init_actions

}//end class CBXLatestTweets_Public
