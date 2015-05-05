<?php
/**
* Admin View: Notice - Install
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="message" class="updated timetrader-message tt-connect">
	<p>
		<?php _e( '<strong>Welcome to timetrader</strong> &#8211; You\'re almost ready to start selling :)', 'timetrader' ); ?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( add_query_arg( 'install_timetrader_pages', 'true', admin_url( 'admin.php?page=tt-settings' ) ) ); ?>" class="button-primary"><?php _e( 'Install timetrader Pages', 'timetrader' ); ?></a>
		<a class="skip button" href="<?php echo esc_url( add_query_arg( 'tt-hide-notice', 'install' ) ); ?>"><?php _e( 'Skip setup', 'timetrader' ); ?></a>
	</p>
</div>
