<?php
/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the admin setting page
 *
 * @link       https://www.codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/templates
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'CBX Latest Tweets: Setting', 'cbxlatesttweets' ); ?></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder  columns-2">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
							<?php
							$this->settings_api->show_navigation();
							$this->settings_api->show_forms();
							?>
                        </div>
                    </div>
                </div>
            </div>
			<?php include( 'sidebar.php' ); ?>
        </div>
        <div class="clear"></div>
    </div>
</div>