<?php
/**
 * Display notices in admin.
 *
 * @author      Indiorlei de Oliveira (Agência Cion)
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TT_Admin_Notices Class
 */
class TT_Admin_Notices {

	/**
	 * Array of notices - name => callback
	 * @var array
	 */
	private $notices = array(
		'install'             => 'install_notice',
		'update'              => 'update_notice',
		'template_files'      => 'template_file_check_notice',
		'frontend_colors'     => 'frontend_colors_notice',
		'theme_support'       => 'theme_check_notice',
		'translation_upgrade' => 'translation_upgrade_notice',
		'tracking'            => 'tracking_notice'
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'switch_theme', array( $this, 'reset_admin_notices' ) );
		add_action( 'timetrader_installed', array( $this, 'reset_admin_notices' ) );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
		add_action( 'timetrader_hide_frontend_colors_notice', array( $this, 'hide_frontend_colors_notice' ) );
		add_action( 'timetrader_hide_install_notice', array( $this, 'hide_install_notice' ) );
		add_action( 'timetrader_hide_translation_upgrade_notice', array( $this, 'hide_translation_upgrade_notice' ) );
		add_action( 'timetrader_hide_tracking_notice', array( $this, 'hide_tracking_notice' ) );
		add_action( 'admin_print_styles', array( $this, 'add_notices' ) );
		add_action( 'admin_init', array( $this, 'check_optin_action' ) );
	}

	/**
	 * Reset notices for themes when switched or a new version of tt is installed
	 */
	public function reset_admin_notices() {

		if ( $this->has_frontend_colors() ) {
			self::add_notice( 'frontend_colors' );
		}

		if ( $this->has_not_confirmed_tracking() ) {
			self::add_notice( 'tracking' );
		}

		if ( ! current_theme_supports( 'timetrader' ) && ! in_array( get_option( 'template' ), tt_get_core_supported_themes() ) ) {
			self::add_notice( 'theme_support' );
		}

		self::add_notice( 'template_files' );
	}

	/**
	 * Show a notice
	 * @param  string $name
	 */
	public static function add_notice( $name ) {
		$notices = array_unique( array_merge( get_option( 'timetrader_admin_notices', array() ), array( $name ) ) );
		update_option( 'timetrader_admin_notices', $notices );
	}

	/**
	 * Remove a notice from being displayed
	 * @param  string $name
	 */
	public static function remove_notice( $name ) {
		$notices = array_diff( get_option( 'timetrader_admin_notices', array() ), array( $name ) );
		update_option( 'timetrader_admin_notices', $notices );
	}

	/**
	 * See if a notice is being shown
	 * @param  string  $name
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		return in_array( $name, get_option( 'timetrader_admin_notices', array() ) );
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_notices() {
		if ( isset( $_GET['tt-hide-notice'] ) ) {
			$hide_notice = sanitize_text_field( $_GET['tt-hide-notice'] );
			self::remove_notice( $hide_notice );
			do_action( 'timetrader_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * When install is hidden, trigger a redirect
	 */
	public function hide_install_notice() {
		// What's new redirect
		if ( ! self::has_notice( 'update' ) ) {
			delete_transient( '_tt_activation_redirect' );
			wp_redirect( admin_url( 'index.php?page=tt-about&tt-updated=true' ) );
			exit;
		}
	}

	/**
	 * Delete colors option
	 */
	public function hide_frontend_colors_notice() {
		delete_option( 'timetrader_frontend_css_colors' );
	}

	/**
	 * Hide translation upgrade message
	 */
	public function hide_translation_upgrade_notice() {
		update_option( 'timetrader_language_pack_version', array( TT_VERSION , get_locale() ) );
	}

	/**
	 * Hide tracking notice
	 */
	public function hide_tracking_notice() {
		update_option( 'timetrader_allow_tracking', 'no' );
	}

	/**
	 * Add notices + styles if needed.
	 */
	public function add_notices() {
		$notices = get_option( 'timetrader_admin_notices', array() );

		foreach ( $notices as $notice ) {
			wp_enqueue_style( 'timetrader-activation', plugins_url(  '/assets/css/activation.css', WC_PLUGIN_FILE ) );
			wp_enqueue_script( 'tt-admin-notices' );
			add_action( 'admin_notices', array( $this, $this->notices[ $notice ] ) );
		}
	}

	/**
	 * If we need to update, include a message with the update button
	 */
	public function update_notice() {
		include( 'views/html-notice-update.php' );
	}

	/**
	 * If we have just installed, show a message with the install pages button
	 */
	public function install_notice() {
		include( 'views/html-notice-install.php' );
	}

	/**
	 * Show the Theme Check notice
	 */
	public function theme_check_notice() {
		if ( ! current_theme_supports( 'timetrader' ) ) {
			include( 'views/html-notice-theme-support.php' );
		}
	}

	/**
	 * Show the translation upgrade notice
	 */
	public function translation_upgrade_notice() {
		$screen = get_current_screen();

		if ( 'update-core' !== $screen->id ) {
			include( 'views/html-notice-translation-upgrade.php' );
		}
	}

	/**
	 * Show a notice highlighting bad template files
	 */
	public function template_file_check_notice() {
		$core_templates = TT_Admin_Status::scan_template_files( TT()->plugin_path() . '/templates' );
		$outdated       = false;

		foreach ( $core_templates as $file ) {
			$theme_file = false;
			if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_stylesheet_directory() . '/timetrader/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/timetrader/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif( file_exists( get_template_directory() . '/timetrader/' . $file ) ) {
				$theme_file = get_template_directory() . '/timetrader/' . $file;
			}

			if ( $theme_file ) {
				$core_version  = TT_Admin_Status::get_file_version( TT()->plugin_path() . '/templates/' . $file );
				$theme_version = TT_Admin_Status::get_file_version( $theme_file );

				if ( $core_version && $theme_version && version_compare( $theme_version, $core_version, '<' ) ) {
					$outdated = true;
					break;
				}
			}
		}

		if ( $outdated ) {
			include( 'views/html-notice-template-check.php' );
		} else {
			self::remove_notice( 'template_files' );
		}
	}

	/**
	 * Checks if there is any change in timetrader_frontend_css_colors
	 *
	 * @return bool
	 */
	public function has_frontend_colors() {
		$styles = (array) TT_Frontend_Scripts::get_styles();

		if ( ! array_key_exists( 'timetrader-general', $styles ) ) {
			return false;
		}

		$colors  = get_option( 'timetrader_frontend_css_colors' );
		$default = array(
			'primary'    => '#ad74a2',
			'secondary'  => '#f7f6f7',
			'highlight'  => '#85ad74',
			'content_bg' => '#ffffff',
			'subtext'    => '#777777'
		);

		if ( ! $colors || $colors === $default ) {
			return false;
		}

		return true;
	}

	/**
	 * Notice to say Frontend Colors options has been deprecated in 2.3
	 */
	public function frontend_colors_notice() {
		include( 'views/html-notice-frontend-colors.php' );
	}

	/**
	 * See if the user has explicitly opted out fro tracking
	 * @return boolean
	 */
	public function has_not_confirmed_tracking() {
		return 'unknown' === get_option( 'timetrader_allow_tracking', 'unknown' );
	}

	/**
	 * Notice to opt-in into tracking
	 */
	public function tracking_notice() {
		if ( current_user_can( 'manage_timetrader' ) ) {
			include( 'views/html-notice-tracking.php' );
		}
	}

	/**
	 * Handle opt in or out actions based on notice selection
	 * @return void
	 */
	public function check_optin_action() {
		if ( ! isset( $_GET['tt_tracker_optin'] ) || ! isset( $_GET['tt_tracker_nonce'] ) || ! wp_verify_nonce( $_GET['tt_tracker_nonce'], 'tt_tracker_optin' ) ) {
			return;
		}
		// Enable tracking
		update_option( 'timetrader_allow_tracking', 'yes' );

		// Remove notice
		self::remove_notice( 'tracking' );

		// Trigger the first track
		TT_Tracker::send_tracking_data( true );
	}
}

new TT_Admin_Notices();
