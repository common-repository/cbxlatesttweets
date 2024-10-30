<?php
/**
 * Provide a public template view for the plugin
 *
 * This file is used to markup the admin facing widget form
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/templates
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<?php


$tweets_items_class      = apply_filters( 'cbxlatesttweets_grid_wrap_class', 'cbxlatesttweets-grid cbxlatesttweets-grid-' . $scope );
$tweets_item_class       = apply_filters( 'cbxlatesttweets_grid_item_class', 'cbxlatesttweets-grid-item cbxlatesttweets-grid-item-' . $scope );


if ( is_array( $latest_tweets ) && sizeof( $latest_tweets ) > 0 ) {
	echo '<div class="' . $tweets_items_class . ' cbxlatesttweets-row">';
	foreach ( $latest_tweets as $index => $latest_tweet ) {
		$tweet_user = $latest_tweet->user;

		$tweet_user_name        = $tweet_user->name;
		$tweet_user_screen_name = $tweet_user->screen_name;


		$tweet_permalink = 'https://twitter.com/' . esc_attr( $tweet_user_screen_name ) . '/statuses/' . $latest_tweet->id_str;

		echo '<div class="' . $tweets_item_class . ' cbxlatesttweets-col-md-3 cbxlatesttweets-col-sm-6 cbxlatesttweets-col-xs-12"><div class="cbxlatesttweets-grid-item-wrap">';
		echo CBXLatestTweetsHelper::linkifyTwitterStatus( $latest_tweet->text );
		echo ' - ' . '<a title="' . esc_attr( $latest_tweet->created_at ) . '(' . esc_attr( $tweet_user_name ) . '@' . esc_attr( $tweet_user_screen_name ) . ')" class="cbxlatesttweets-grid-item-permalink" href="' . esc_url( $tweet_permalink ) . '" target="_blank">' . CBXLatestTweetsHelper::getTweetTime( strtotime( $latest_tweet->created_at ), $time_format, $date_time_format ) . '</a>';
		echo '</div></div>';
	}
	echo '</div>';
} else {
	if ( $latest_tweets == '' ) {
		echo '<p class="cbxlatesttweets-item-notfound">' . esc_html__( 'No tweets found', 'cbxlatesttweets' ) . '</p>';
	} else {
		echo '<p class="cbxlatesttweets-item-error">' . $latest_tweets . '</p>';
	}
}