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


$tweets_items_class      = apply_filters( 'cbxlatesttweets_list_wrap_class', 'cbxlatesttweets-list cbxlatesttweets-list-' . $scope );
$tweets_item_class       = apply_filters( 'cbxlatesttweets_list_item_class', 'cbxlatesttweets-list-item cbxlatesttweets-list-item-' . $scope );


if ( is_array( $latest_tweets ) && sizeof( $latest_tweets ) > 0 ) {
	echo '<ul class="' . $tweets_items_class . '">';

	foreach ( $latest_tweets as $index => $latest_tweet ) {
		$tweet_user = $latest_tweet->user;

		$tweet_user_name        = $tweet_user->name;
		$tweet_user_screen_name = $tweet_user->screen_name;


		$tweet_permalink = 'http://twitter.com/' . esc_attr( $tweet_user_screen_name ) . '/statuses/' . $latest_tweet->id_str;

		echo '<li class="' . $tweets_item_class . '">';
		echo CBXLatestTweetsHelper::linkifyTwitterStatus( $latest_tweet->text );
		echo ' - ' . '<a title="' . esc_attr( $latest_tweet->created_at ) . '(' . esc_attr( $tweet_user_name ) . '@' . esc_attr( $tweet_user_screen_name ) . ')" class="cbxlatesttweets-list-item-permalink" href="' . $tweet_permalink . '" target="_blank">' . CBXLatestTweetsHelper::getTweetTime( strtotime( $latest_tweet->created_at ), $time_format, $date_time_format ) . '</a>';
		echo '</li>';
	}

	echo '</ul>';
} else {
	if ( $latest_tweets == '' ) {
		echo '<p class="cbxlatesttweets-item-notfound">' . esc_html__( 'No tweets found', 'cbxlatesttweets' ) . '</p>';
	} else {
		echo '<p class="cbxlatesttweets-item-error">' . $latest_tweets . '</p>';
	}
}