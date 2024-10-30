<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CBXLatestTweets_WPBWidget extends WPBakeryShortCode {

	/**
	 * CBXLatestTweets_WPBWidget constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'cbx_latest_tweets_mapping' ) );
	}// /end of constructor


	/**
	 * Element Mapping
	 */
	public function cbx_latest_tweets_mapping() {

		$layouts_r = CBXLatestTweetsHelper::get_layouts_r();


		// Map the block with vc_map()
		vc_map( array(
			"name"        => esc_html__( "CBX Latest Tweets", 'cbxlatesttweets' ),
			"description" => esc_html__( "Latest Tweets by twitter handle", 'cbxlatesttweets' ),
			"base"        => "cbxlatesttweets",
			"icon"        => CBXLATESTTWEETS_ROOT_URL . 'widgets/vc-elements/vc-icon/icon.png',
			"category"    => esc_html__( 'CBX Widgets', 'cbxlatesttweets' ),
			"params"      => array(
				array(
					"type"        => "textfield",
					"holder"      => "div",
					"class"       => "",
					'admin_label' => true,
					"heading"     => esc_html__( "Username", 'cbxlatesttweets' ),
					"param_name"  => "username",
				),
				array(
					"type"        => "textfield",
					"holder"      => "div",
					"class"       => "",
					'admin_label' => true,
					"heading"     => esc_html__( "Number of Tweets", 'cbxlatesttweets' ),
					"param_name"  => "limit",
					"std"         => 10,

				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Layout', 'cbxlatesttweets' ),
					'param_name'  => 'layout',
					'admin_label' => true,
					'value'       => $layouts_r,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Include Retweets', 'cbxlatesttweets' ),
					'param_name'  => 'include_rts',
					'admin_label' => true,
					'value'       => array(
						esc_html__( 'Yes', 'cbxlatesttweets' ) => 1,
						esc_html__( 'No', 'cbxlatesttweets' )  => 0,
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Exclude Replies', 'cbxlatesttweets' ),
					'param_name'  => 'exclude_replies',
					'admin_label' => true,
					'value'       => array(
						esc_html__( 'Yes', 'cbxlatesttweets' ) => 1,
						esc_html__( 'No', 'cbxlatesttweets' )  => 0,
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Time Format', 'cbxlatesttweets' ),
					'param_name'  => 'time_format',
					'admin_label' => true,
					'value'       => array(
						esc_html__( 'Relative (Example: 5 mins ago)', 'cbxlatesttweets' ) => 1,
						esc_html__( 'Regular (Example: dd-mm-yyyy)', 'cbxlatesttweets' )  => 0,
					),
				),
				array(
					"type"        => "textfield",
					"class"       => "",
					'admin_label' => true,
					"heading"     => esc_html__( 'Date Time Format', 'cbxlatesttweets' ),
					"param_name"  => "date_time_format",
					"value"       => esc_html__( 'F j, Y g:i a', 'cbxlatesttweets' ),
					'description' => sprintf( __( 'If time format is regular then date time display format.<a href="%s" target="_blank">Documentation on date and time formatting)</a>).',
						'cbxlatesttweets' ),
						'https://wordpress.org/support/article/formatting-date-and-time/' ),
				),
				array(
					'type'       => 'css_editor',
					"class"      => "",
					'heading'    => esc_html__( 'Css', 'cbxlatesttweets' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design options', 'cbxlatesttweets' ),
				),

			),
		) );
	}
}// end class CBXLatestTweets_WPBWidget

new CBXLatestTweets_WPBWidget();