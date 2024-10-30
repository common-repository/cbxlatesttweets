<?php
/**
 * Provide a admin widget view for the plugin
 *
 * This file is used to markup the admin facing widget form
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/widgets/views
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<?php
do_action( 'cbxlatesttweetswidget_form_before_admin', $instance, $this );
?>
    <!-- Custom  Title Field -->
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'cbxlatesttweets' ); ?></label>

        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
               name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
    </p>

	<p>
		<span class="dashicons dashicons-info"></span> <a target="_blank" href="<?php echo esc_url(admin_url('options-general.php?page=cbxlatesttweetssettings#cbxlatesttweets_api_config')) ?>"><?php esc_html_e('To make the widget work twitter Api config is needed', 'cbxlatesttweets'); ?></a>
	</p>
    <!-- Custom  Username Field -->
    <p>
        <label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php esc_html_e( 'Twitter Username', 'cbxlatesttweets' ); ?></label>

        <input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>"
               name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo $username; ?>"/>
    </p>


    <!-- Display Limit -->
    <p>
        <label for="<?php echo $this->get_field_id( 'limit' ); ?>">
			<?php esc_html_e( 'Number of Tweets', "cbxlatesttweets" ); ?>
        </label>

        <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>"
               name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo $limit; ?>"/>
    </p>

    <!-- Widget Insta Post ID-->
    <p>
        <label for="<?php echo $this->get_field_id( 'layout' ); ?>"> <?php esc_html_e( 'Layout', 'cbxlatesttweets' ); ?>
            <select class="widefat" id="<?php echo $this->get_field_id( 'layout' ); ?>"
                    name="<?php echo $this->get_field_name( 'layout' ); ?>">
				<?php foreach ( $layouts as $key => $value ) { ?>
                    <option value="<?php echo $key; ?>" <?php echo ( $layout == $key ) ? 'selected="selected"' : ''; ?>><?php echo esc_attr( $value); ?></option>
				<?php } ?>
            </select> </label>
    </p>

    <!-- Include Retweets -->
    <p>
        <label for="<?php echo $this->get_field_id( 'include_rts' ); ?>">
			<?php esc_html_e( 'Include Retweets', 'cbxlatesttweets' ); ?>
        </label>

        <select name="<?php echo $this->get_field_name( 'include_rts' ); ?>" id="<?php echo $this->get_field_id( 'include_rts' ); ?>">
            <option value="1" <?php selected( $include_rts, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxlatesttweets' ); ?></option>
            <option value="0" <?php selected( $include_rts, 0 ); ?> ><?php esc_html_e( 'No', 'cbxlatesttweets' ); ?></option>
        </select>
    </p>

    <!-- Exclude Replies -->
    <p>
        <label for="<?php echo $this->get_field_id( 'exclude_replies' ); ?>">
			<?php esc_html_e( 'Exclude Replies', 'cbxlatesttweets' ); ?>
        </label>

        <select name="<?php echo $this->get_field_name( 'exclude_replies' ); ?>" id="<?php echo $this->get_field_id( 'exclude_replies' ); ?>">
            <option value="1" <?php selected( $exclude_replies, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxlatesttweets' ); ?></option>
            <option value="0" <?php selected( $exclude_replies, 0 ); ?> ><?php esc_html_e( 'No', 'cbxlatesttweets' ); ?></option>
        </select>
    </p>


    <!-- Time Format -->
    <p>
        <label for="<?php echo $this->get_field_id( 'time_format' ); ?>">
			<?php esc_html_e( 'Time Format', 'cbxlatesttweets' ); ?>
        </label>

        <select name="<?php echo $this->get_field_name( 'time_format' ); ?>" id="<?php echo $this->get_field_id( 'time_format' ); ?>">
            <option value="1" <?php selected( $time_format, 1 ); ?> ><?php esc_html_e( 'Relative (Example: 5 mins ago)', 'cbxlatesttweets' ); ?></option>
            <option value="0" <?php selected( $time_format, 0 ); ?> ><?php esc_html_e( 'Regular (Example: dd-mm-yyyy)', 'cbxlatesttweets' ); ?></option>
        </select>
    </p>
    <p><a target="_blank" href="https://codex.wordpress.org/Formatting_Date_and_Time"><?php esc_html_e( 'Documentation on date and time formatting(regular date time display format)', 'cbxlatesttweets' ); ?></a></p>


    <!-- Date Time Format -->
    <p>
        <label for="<?php echo $this->get_field_id( 'date_time_format' ); ?>">
			<?php esc_html_e( 'Date Time Format', 'cbxlatesttweets' ); ?>
        </label>

        <input class="widefat" id="<?php echo $this->get_field_id( 'date_time_format' ); ?>"
               name="<?php echo $this->get_field_name( 'date_time_format' ); ?>" type="text" value="<?php echo $date_time_format; ?>"/>
    </p>


    <input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>"
           name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1"/>

<?php
do_action( 'cbxlatesttweetswidget_form_after_admin', $instance, $this );