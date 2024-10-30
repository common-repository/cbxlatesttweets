<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CBXLatestTweetsWidget extends WP_Widget {

	/**
	 * Unique identifier for your widget.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * widget file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $widget_slug = 'cbxlatesttweets-widget'; //main parent plugin's language file
	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		parent::__construct(
			$this->get_widget_slug(),
			esc_html__( 'CBX Latest Tweets', 'cbxlatesttweets' ),
			array(
				'classname'   => 'widget-cbxlatesttweets',
				'description' => esc_html__( 'Latest Tweets by twitter handle', 'cbxlatesttweets' ),
			)
		);

		$this->settings_api = new CBXLatestTweets_Settings_API();

	} // end constructor

	/**
	 * Return the widget slug.
	 *
	 * @return    Plugin slug variable.
	 * @since    1.0.0
	 *
	 */
	public function get_widget_slug() {
		return $this->widget_slug;
	}

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'CBX Latest Tweets', 'cbxlatesttweets' ) : $instance['title'], $instance, $this->id_base );
		// Defining the Widget Title
		if ( $title ) {
			$widget_string .= $args['before_title'] . $title . $args['after_title'];
		} else {
			$widget_string .= $args['before_title'] . $args['after_title'];
		}

		ob_start();

		$instance = apply_filters( 'cbxlatesttweets_widget', $instance );


		$settings_api = $this->settings_api;

		$default_include_rts      = $settings_api->get_option( 'include_rts', 'cbxlatesttweets_config', 0 );
		$default_exclude_replies  = $settings_api->get_option( 'exclude_replies', 'cbxlatesttweets_config', 1 );
		$default_time_format      = intval( $settings_api->get_option( 'time_format', 'cbxlatesttweets_config', 1 ) );
		$default_date_time_format = $settings_api->get_option( 'date_time_format', 'cbxlatesttweets_config', CBXLatestTweetsHelper::getGlobalDateTimeFormat() );


		$limit            = isset( $instance['limit'] ) ? intval( $instance['limit'] ) : 10;
		$username         = isset( $instance['username'] ) ? sanitize_text_field( $instance['username'] ) : '';
		$layout           = isset( $instance['layout'] ) ? sanitize_text_field( $instance['layout'] ) : 'basic';
		$include_rts      = isset( $instance['include_rts'] ) ? intval( $instance['include_rts'] ) : $default_include_rts;
		$exclude_replies  = isset( $instance['exclude_replies'] ) ? intval( $instance['exclude_replies'] ) : $default_exclude_replies;
		$time_format      = isset( $instance['time_format'] ) ? intval( $instance['time_format'] ) : $default_time_format;
		$date_time_format = isset( $instance['date_time_format'] ) ? $instance['date_time_format'] : $default_date_time_format;

		$limit = ( intval( $limit ) > 20 ) ? 20 : $limit; //maximum 20 tweets per page

		extract( $instance, EXTR_SKIP );

		//get the template defined
		$layouts = CBXLatestTweetsHelper::get_layouts();

		$scope = 'widget';


		$latest_tweets = CBXLatestTweetsHelper::getLatestTweets( $username, $limit, $include_rts, $exclude_replies );


		CBXLatestTweetsHelper::enqueue_styles( $layout );
		
		$layouts_p_keys = array_keys(CBXLatestTweetsHelper::get_layouts_p());
		if(!in_array($layout, $layouts_p_keys)) $layout = 'basic';
		
		//load the template
		$layout_include_url = $layouts[ $layout ]['template_dir'];
		include( $layout_include_url );

		//include( cbxlatesttweets_locate_template( $layout . '.php' ) );
		$content = ob_get_contents();
		ob_end_clean();

		$widget_string .= $content;


		$widget_string .= $after_widget;

		echo $widget_string;

	}//end of method widget


	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		//$settings_api     = new CBXLatestTweets_Settings_API( CBXLATESTTWEETS_PLUGIN_NAME, CBXLATESTTWEETS_PLUGIN_VERSION );
		$settings_api = $this->settings_api;

		$default_include_rts      = $settings_api->get_option( 'include_rts', 'cbxlatesttweets_config', 0 );
		$default_exclude_replies  = $settings_api->get_option( 'exclude_replies', 'cbxlatesttweets_config', 1 );
		$default_time_format      = intval( $settings_api->get_option( 'time_format', 'cbxlatesttweets_config', 1 ) );
		$default_date_time_format = $settings_api->get_option( 'date_time_format', 'cbxlatesttweets_config', CBXLatestTweetsHelper::getGlobalDateTimeFormat() );

		$instance = $old_instance;

		$instance['title']            = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['username']         = isset( $new_instance['username'] ) ? sanitize_text_field( $new_instance['username'] ) : '';
		$instance['layout']           = isset( $new_instance['layout'] ) ? sanitize_text_field( $new_instance['layout'] ) : 'basic';
		$instance['limit']            = isset( $new_instance['limit'] ) ? intval( $new_instance['limit'] ) : 10;
		$instance['include_rts']      = isset( $new_instance['include_rts'] ) ? intval( $new_instance['include_rts'] ) : $default_include_rts;
		$instance['exclude_replies']  = isset( $new_instance['exclude_replies'] ) ? intval( $new_instance['exclude_replies'] ) : $default_exclude_replies;
		$instance['time_format']      = isset( $new_instance['time_format'] ) ? intval( $new_instance['time_format'] ) : $default_time_format;
		$instance['date_time_format'] = isset( $new_instance['date_time_format'] ) ? $new_instance['date_time_format'] : $default_date_time_format;

		$instance = apply_filters( 'cbxlatesttweetswidget_update', $instance, $new_instance );

		return $instance;

	}//end of method widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		//$settings_api     = new
		$settings_api = $this->settings_api;

		$default_include_rts      = intval( $settings_api->get_option( 'include_rts', 'cbxlatesttweets_config', 0 ) );
		$default_exclude_replies  = intval( $settings_api->get_option( 'exclude_replies', 'cbxlatesttweets_config', 1 ) );
		$default_time_format      = intval( $settings_api->get_option( 'time_format', 'cbxlatesttweets_config', 1 ) );
		$default_date_time_format = $settings_api->get_option( 'date_time_format', 'cbxlatesttweets_config', CBXLatestTweetsHelper::getGlobalDateTimeFormat() );

		$defaults = array(
			'title'            => esc_html__( 'CBX Latest Tweets', 'cbxlatesttweets' ),
			'username'         => '',
			'layout'           => 'basic',
			'limit'            => 10,
			'include_rts'      => $default_include_rts,
			'exclude_replies'  => $default_exclude_replies,
			'time_format'      => $default_time_format,
			'date_time_format' => $default_date_time_format,
		);

		$defaults = apply_filters( 'cbxlatesttweetswidget_widget_form_fields', $defaults );


		$instance = wp_parse_args(
			(array) $instance,
			$defaults
		);

		$instance = apply_filters( 'cbxlatesttweetswidget_widget_form', $instance );


		extract( $instance, EXTR_SKIP );

		//$layouts = CBXLatestTweetsHelper::get_layouts();
		$layouts = CBXLatestTweetsHelper::get_layouts_p();


		// Display the admin form
		include( plugin_dir_path( __FILE__ ) . 'views/admin.php' );

	}//end of method form

}// end class CBXLatestTweetsWidget