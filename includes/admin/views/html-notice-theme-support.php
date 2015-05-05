<?php
/**
* Admin View: Notice - Theme Support
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="message" class="updated timetrader-message tt-connect">
	<p>
		<?php printf( __( '<strong>Your theme does not declare timetrader support</strong> &#8211; Please read our integration guide or check out our %sStorefront%s theme which is totally free to download and designed specifically for use with timetrader :)', 'timetrader' ), '<a href="' . esc_url( admin_url( 'theme-install.php?theme=storefront' ) ) . '">', '</a>' ); ?>
	</p>
	<p class="submit">
		<a href="http://www.woothemes.com/storefront/?utm_source=wpadmin&utm_medium=notice&utm_campaign=Storefront" class="button-primary" target="_blank"><?php _e( 'Find out more about Storefront', 'timetrader' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'timetrader_docs_url', 'http://docs.woothemes.com/document/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ); ?>" class="button" target="_blank"><?php _e( 'Theme integration guide', 'timetrader' ); ?></a>
		<a class="skip button" href="<?php echo esc_url( add_query_arg( 'tt-hide-notice', 'theme_support' ) ); ?>"><?php _e( 'Hide this notice', 'timetrader' ); ?></a>
	</p>
</div>
