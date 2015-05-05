<?php
/**
* Admin View: Notice - Translation Upgrade
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="message" class="updated timetrader-message tt-connect">
	<p>
		<?php printf( __( '<strong>timetrader Translation Available</strong> &#8211; Install or update your <code>%s</code> translation to version <code>%s</code>.', 'timetrader' ), get_locale(), TT_VERSION ); ?>
	</p>
	<p>
		<?php if ( is_multisite() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=tt-status&tab=tools&action=translation_upgrade' ), 'debug_action' ) ); ?>" class="button-primary"><?php _e( 'Update Translation', 'timetrader' ); ?></a>
		<?php else : ?>
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'do-translation-upgrade' ), admin_url( 'update-core.php' ) ), 'upgrade-translations' ) ); ?>" class="button-primary"><?php _e( 'Update Translation', 'timetrader' ); ?></a>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=tt-status&tab=tools&action=translation_upgrade' ), 'debug_action' ) ); ?>" class="button-primary"><?php _e( 'Force Update Translation', 'timetrader' ); ?></a>
		<?php endif; ?>
		<a href="<?php echo esc_url( add_query_arg( 'tt-hide-notice', 'translation_upgrade' ) ); ?>" class="button"><?php _e( 'Hide This Message', 'timetrader' ); ?></a>
	</p>
</div>
