<?php

namespace CBXLatestTweetsElemWidget\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CBX Latest Tweets Elementor Widget
 */
class CBXLatestTweets_ElemWidget extends \Elementor\Widget_Base {

	/**
	 * Retrieve google maps widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'cbxlatesttweets';
	}

	/**
	 * Retrieve google maps widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return esc_html__( 'CBX Latest Tweets', 'cbxlatesttweets' );
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @return array Widget categories.
	 * @since  1.0.10
	 * @access public
	 *
	 */
	public function get_categories() {
		return array( 'codeboxr' );
	}

	/**
	 * Retrieve google maps widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'cbxlatesttweets-icon';
	}

	/**
	 * Register google maps widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$settings_api = new \CBXLatestTweets_Settings_API();

		$include_rts      = $settings_api->get_option( 'include_rts', 'cbxlatesttweets_config', 0 );
		$exclude_replies  = $settings_api->get_option( 'exclude_replies', 'cbxlatesttweets_config', 1 );
		$time_format      = $settings_api->get_option( 'time_format', 'cbxlatesttweets_config', 1 );
		$date_time_format = $settings_api->get_option( 'date_time_format', 'cbxlatesttweets_config', \CBXLatestTweetsHelper::getGlobalDateTimeFormat() );



		$layouts = \CBXLatestTweetsHelper::get_layouts_p();


		$this->start_controls_section(
			'section_cbxlatesttweets_single',
			array(
				'label' => esc_html__( 'CBX Latest Tweets Setting', 'cbxlatesttweets' ),
			)
		);


		$this->add_control(
			'cbxlatesttweets_layout',
			array(
				'label'       => esc_html__( 'Layout', 'cbxlatesttweets' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'placeholder' => esc_html__( 'Select layout', 'cbxlatesttweets' ),
				'default'     => 'basic',
				'options'     => $layouts,
			)
		);


		$this->add_control(
			'cbxlatesttweets_username',
			array(
				'label'       => esc_html__( 'Twitter Username', 'cbxlatesttweets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'multiple using comma', 'cbxlatesttweets' ),
				'default'     => '',
			)
		);


		$this->add_control(
			'cbxlatesttweets_limit',
			array(
				'label'       => esc_html__( 'Number of Tweets', 'cbxlatesttweets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'No of tweets to display', 'cbxlatesttweets' ),
				'default'     => 10,
			)
		);


		$this->add_control(
			'cbxlatesttweets_include_rts',
			array(
				'label'   => esc_html__( 'Include Retweets', 'cbxlatesttweets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'1' => esc_html__( 'Yes', 'cbxlatesttweets' ),
					'0' => esc_html__( 'No', 'cbxlatesttweets' ),
				),
				'default' => $include_rts,
			)
		);

		$this->add_control(
			'cbxlatesttweets_exclude_replies',
			array(
				'label'   => esc_html__( 'Exclude Replies', 'cbxlatesttweets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'1' => esc_html__( 'Yes', 'cbxlatesttweets' ),
					'0' => esc_html__( 'No', 'cbxlatesttweets' ),
				),
				'default' => $exclude_replies,
			)
		);

		$this->add_control(
			'cbxlatesttweets_time_format',
			array(
				'label'   => esc_html__( 'Exclude Replies', 'cbxlatesttweets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'1' => esc_html__( 'Relative (Example: 5 mins ago)', 'cbxlatesttweets' ),
					'0' => esc_html__( 'Regular (Example: dd-mm-yyyy)', 'cbxlatesttweets' ),
				),
				'default' => $time_format,
			)
		);


		$this->add_control(
			'cbxlatesttweets_date_time_format',
			array(
				'label'       => esc_html__( 'Date Time Format', 'cbxlatesttweets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Date Time Format', 'cbxlatesttweets' ),
				'default'     => $date_time_format,
			)
		);


		$this->end_controls_section();
	}//end method _register_controls

	/**
	 * Render google maps widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings_api = new \CBXLatestTweets_Settings_API();

		$include_rts      = $settings_api->get_option( 'include_rts', 'cbxlatesttweets_config', 0 );
		$exclude_replies  = $settings_api->get_option( 'exclude_replies', 'cbxlatesttweets_config', 1 );
		$time_format      = $settings_api->get_option( 'time_format', 'cbxlatesttweets_config', 1 );
		$date_time_format = $settings_api->get_option( 'date_time_format', 'cbxlatesttweets_config', \CBXLatestTweetsHelper::getGlobalDateTimeFormat() );

		$atts = array();

		$instance = $this->get_settings();


		//render map from custom attributes
		$atts['username']         = isset( $instance['cbxlatesttweets_username'] ) ? esc_attr( $instance['cbxlatesttweets_username'] ) : '';
		$atts['layout']           = isset( $instance['cbxlatesttweets_layout'] ) ? esc_attr( $instance['cbxlatesttweets_layout'] ) : 'basic';
		$atts['limit']            = isset( $instance['cbxlatesttweets_limit'] ) ? intval( $instance['cbxlatesttweets_limit'] ) : 10;
		$atts['include_rts']      = isset( $instance['cbxlatesttweets_include_rts'] ) ? intval( $instance['cbxlatesttweets_include_rts'] ) : $include_rts;
		$atts['exclude_replies']  = isset( $instance['cbxlatesttweets_exclude_replies'] ) ? intval( $instance['cbxlatesttweets_exclude_replies'] ) : $exclude_replies;
		$atts['time_format']      = isset( $instance['cbxlatesttweets_time_format'] ) ? intval( $instance['cbxlatesttweets_time_format'] ) : $time_format;
		$atts['date_time_format'] = isset( $instance['cbxlatesttweets_date_time_format'] ) ? esc_attr( $instance['cbxlatesttweets_date_time_format'] ) : $date_time_format;

		$atts['scope'] = 'elementor';

		$atts = apply_filters( 'cbxbusinesshours_shortcode_builder_attr', $atts );

		$attr_html = '';

		foreach ( $atts as $key => $value ) {
			$attr_html .= ' ' . $key . '="' . $value . '" ';
		}

		echo do_shortcode( '[cbxlatesttweets ' . $attr_html . ']' );

	}//end method render

	/**
	 * Render google maps widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function _content_template() {
	}//end method _content_template
}//end method CBXLatestTweets_ElemWidget
