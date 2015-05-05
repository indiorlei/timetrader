<?php
/**
* Admin View: Notice - Tracking
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="message" class="updated timetrader-message timetrader-tracker">
	<p>
		<?php printf( __( 'Want to help make timetrader even more awesome? Allow WooThemes to collect non-sensitive diagnostic data and usage information, and get %s discount on your next WooThemes purchase. %sFind out more%s.', 'timetrader' ), '20%', '<a href="/timetrader/usage-tracking/" target="_blank">', '</a>' ); ?>
	</p>
	<p class="submit">
		<a class="button-primary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'tt_tracker_optin', 'true' ), 'tt_tracker_optin', 'tt_tracker_nonce' ) ); ?>"><?php _e( 'Allow', 'timetrader' ); ?></a>
		<a class="skip button" href="<?php echo esc_url( add_query_arg( 'wc-hide-notice', 'tracking' ) ); ?>"><?php _e( 'No, do not bother me again', 'timetrader' ); ?></a>
	</p>
</div>
