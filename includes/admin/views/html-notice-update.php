<?php
/**
* Admin View: Notice - Update
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="message" class="updated timetrader-message tt-connect">
	<p><?php _e( '<strong>timetrader Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'timetrader' ); ?></p>
	<p class="submit">
		<a href="<?php echo esc_url( add_query_arg( 'do_update_timetrader', 'true', admin_url( 'admin.php?page=tt-settings' ) ) ); ?>" class="tt-update-now button-primary"><?php _e( 'Run the updater', 'timetrader' ); ?></a>
	</p>
</div>
<script type="text/javascript">
jQuery('.tt-update-now').click('click', function(){
	var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'timetrader' ); ?>' );
	return answer;
});
</script>
