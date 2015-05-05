<?php
/**
* Admin View: Notice - Frontend Colors
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$plugin_slug = 'timetrader-colors';
if ( current_user_can( 'install_plugins' ) ) {
	$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
} else {
	$url = 'http://wordpress.org/plugins/' . $plugin_slug;
}
?>
<div id="message" class="updated timetrader-message tt-connect">
	<p>
		<?php _e( '<strong>The Frontend Style options are deprecated</strong> &#8211; If you want to continue editing the colors of your store we recommended that you install the replacement timetrader Colors plugin from WordPress.org.', 'timetrader' ); ?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( $url ); ?>" class="tt-update-now button-primary"><?php _e( 'Install the new timetrader Colors plugin', 'timetrader' ); ?></a>
		<a class="skip button" href="<?php echo esc_url( add_query_arg( 'tt-hide-notice', 'frontend_colors' ) ); ?>"><?php _e( 'Hide this notice', 'timetrader' ); ?></a>
	</p>
</div>
