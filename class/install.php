<?php
/**
 * Installation related functions and actions.
 *
 * @author      Indiorlei de Oliveira (Agência Cion)
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * TimetraderInstall Class
 */
class TimetraderInstall {
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		// add_action( 'in_plugin_update_message-woocommerce/woocommerce.php', array( __CLASS__, 'in_plugin_update_message' ) );
		// add_filter( 'plugin_action_links_' . WC_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		// add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		// add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
	}

	public static function check_version() {
	
		// if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'timetrader_version' ) != TT()->version || get_option( 'timetrader_db_version' ) != TT()->version ) ) {
			self::install();
			do_action( 'timetrader_updated' );
		// }

	}

	public static function install_actions() {
		// Install - Add pages button

		if ( ! empty( $_GET['install_timetrader_pages'] ) ) {

			self::create_pages();

			// // We no longer need to install pages
			// WC_Admin_Notices::remove_notice( 'install' );

			// // What's new redirect
			// if ( ! WC_Admin_Notices::has_notice( 'update' ) ) {
			// 	delete_transient( '_wc_activation_redirect' );
			// 	wp_redirect( admin_url( 'index.php?page=wc-about&wc-updated=true' ) );
			// 	exit;
			// }

		// Update button
		} elseif ( ! empty( $_GET['do_update_timetrader'] ) ) {

			self::update();

			// // Update complete
			// WC_Admin_Notices::remove_notice( 'update' );

			// // What's new redirect
			// if ( ! WC_Admin_Notices::has_notice( 'install' ) ) {
			// 	delete_transient( '_wc_activation_redirect' );
			// 	wp_redirect( admin_url( 'index.php?page=wc-about&wc-updated=true' ) );
			// 	exit;
			// }
		}
	
	}

	/**
	* Install timetrader
	*/
	public static function install() {

		if ( ! defined( 'TT_INSTALLING' ) ) {
			define( 'TT_INSTALLING', true );
		}

		// // Ensure needed classes are loaded
		// include_once( 'admin/class-wc-admin-notices.php' );

		self::create_options();
		self::create_tables();
		self::create_roles();

		// // Register post types
		TimeTrader_PostTypes::register_post_types();
		TimeTrader_PostTypes::register_taxonomies();

		// // Also register endpoints - this needs to be done prior to rewrite rule flush
		// WC()->query->init_query_vars();
		// WC()->query->add_endpoints();

		self::create_terms();
		// self::create_cron_jobs();
		// self::create_files();

		// // Queue upgrades
		// $current_db_version = get_option( 'woocommerce_db_version', null );

		// if ( version_compare( $current_db_version, '2.3.0', '<' ) && null !== $current_db_version ) {
		// 	WC_Admin_Notices::add_notice( 'update' );
		// } else {
		// 	delete_option( 'woocommerce_db_version' );
		// 	add_option( 'woocommerce_db_version', WC()->version );
		// }

		// // Update version
		// delete_option( 'woocommerce_version' );
		// add_option( 'woocommerce_version', WC()->version );

		// // Check if pages are needed
		// if ( wc_get_page_id( 'shop' ) < 1 ) {
		// 	WC_Admin_Notices::add_notice( 'install' );
		// }

		// // Flush rules after install
		// flush_rewrite_rules();
		// delete_transient( 'wc_attribute_taxonomies' );

		// // Redirect to welcome screen
		// if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
		// 	set_transient( '_wc_activation_redirect', 1, 30 );
		// }

		// // Trigger action
		// do_action( 'woocommerce_installed' );
	}

	/**
	 * Handle updates
	 */
	private static function update() {
		// $current_db_version = get_option( 'woocommerce_db_version' );
		// $db_updates = array(
		// 	'2.0.0' => 'updates/woocommerce-update-2.0.php',
		// 	'2.0.9' => 'updates/woocommerce-update-2.0.9.php',
		// 	'2.1.0' => 'updates/woocommerce-update-2.1.php',
		// 	'2.2.0' => 'updates/woocommerce-update-2.2.php',
		// 	'2.3.0' => 'updates/woocommerce-update-2.3.php'
		// );

		// foreach ( $db_updates as $version => $updater ) {
		// 	if ( version_compare( $current_db_version, $version, '<' ) ) {
		// 		include( $updater );
		// 		delete_option( 'woocommerce_db_version' );
		// 		add_option( 'woocommerce_db_version', $version );
		// 	}
		// }

		// delete_option( 'woocommerce_db_version' );
		// add_option( 'woocommerce_db_version', WC()->version );
	}

	/**
	 * Create cron jobs (clear them first)
	 */
	private static function create_cron_jobs() {
		// wp_clear_scheduled_hook( 'woocommerce_scheduled_sales' );
		// wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );
		// wp_clear_scheduled_hook( 'woocommerce_cleanup_sessions' );
		// wp_clear_scheduled_hook( 'woocommerce_geoip_updater' );
		// wp_clear_scheduled_hook( 'woocommerce_tracker_send_event' );

		// $ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';

		// wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'daily', 'woocommerce_scheduled_sales' );

		// $held_duration = get_option( 'woocommerce_hold_stock_minutes', '60' );

		// if ( $held_duration != '' ) {
		// 	wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 ), 'woocommerce_cancel_unpaid_orders' );
		// }

		// wp_schedule_event( time(), 'twicedaily', 'woocommerce_cleanup_sessions' );
		// wp_schedule_event( strtotime( 'first tuesday of next month' ), 'monthly', 'woocommerce_geoip_updater' );
		// wp_schedule_event( time(), apply_filters( 'woocommerce_tracker_event_recurrence', 'daily' ), 'woocommerce_tracker_send_event' );
	}

	
	/**
	* Create pages that the plugin relies on, storing page id's in variables.
	*/
	public static function create_pages() {
		// include_once( 'admin/wc-admin-functions.php' );

		$pages = apply_filters( 'timetrader_create_pages', array(
			'shop' => array(
				'name'    => _x( 'shop', 'Page slug', 'timetrader' ),
				'title'   => _x( 'Shop', 'Page title', 'timetrader' ),
				'content' => ''
			),
			'cart' => array(
				'name'    => _x( 'cart', 'Page slug', 'timetrader' ),
				'title'   => _x( 'Cart', 'Page title', 'timetrader' ),
				'content' => '[' . apply_filters( 'timetrader_cart_shortcode_tag', 'timetrader_cart' ) . ']'
			),
			'checkout' => array(
				'name'    => _x( 'checkout', 'Page slug', 'timetrader' ),
				'title'   => _x( 'Checkout', 'Page title', 'timetrader' ),
				'content' => '[' . apply_filters( 'timetrader_checkout_shortcode_tag', 'timetrader_checkout' ) . ']'
			),
			'myaccount' => array(
				'name'    => _x( 'my-account', 'Page slug', 'timetrader' ),
				'title'   => _x( 'My Account', 'Page title', 'timetrader' ),
				'content' => '[' . apply_filters( 'timetrader_my_account_shortcode_tag', 'timetrader_my_account' ) . ']'
			)
		) );

		foreach ( $pages as $key => $page ) {
			wc_create_page( esc_sql( $page['name'] ), 'timetrader_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wc_get_page_id( $page['parent'] ) : '' );
		}
	}


	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 */
	private static function create_options() {
		// // Include settings so that we can run through defaults
		// include_once( 'admin/class-wc-admin-settings.php' );

		// $settings = WC_Admin_Settings::get_settings_pages();

		// foreach ( $settings as $section ) {
		// 	if ( ! method_exists( $section, 'get_settings' ) ) {
		// 		continue;
		// 	}
		// 	$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );

		// 	foreach ( $subsections as $subsection ) {
		// 		foreach ( $section->get_settings( $subsection ) as $value ) {
		// 			if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
		// 				$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
		// 				add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
		// 			}
		// 		}
		// 	}
		// }
	}

	/**
	 * Add the default terms for WC taxonomies - product types and order statuses. Modify this at your own risk.
	 */
	private static function create_terms() {
		$taxonomies = array(
			'product_type' => array(
				'simple',
				'grouped',
				'variable',
				'external'
			)
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					wp_insert_term( $term, $taxonomy );
				}
			}
		}
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *		woocommerce_attribute_taxonomies - Table for storing attribute taxonomies - these are user defined
	 *		woocommerce_termmeta - Term meta table - sadly WordPress does not have termmeta so we need our own
	 *		woocommerce_downloadable_product_permissions - Table for storing user and guest download permissions.
	 *			KEY(order_id, product_id, download_id) used for organizing downloads on the My Account page
	 *		woocommerce_order_items - Order line items are stored in a table to make them easily queryable for reports
	 *		woocommerce_order_itemmeta - Order line item meta is stored in a table for storing extra data.
	 *		woocommerce_tax_rates - Tax Rates are stored inside 2 tables making tax queries simple and efficient.
	 *		woocommerce_tax_rate_locations - Each rate can be applied to more than one postcode/city hence the second table.
	 *
	 * @return void
	 */
	private static function create_tables() {
		// global $wpdb;
		// $wpdb->hide_errors();
		// require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		// /**
		//  * Before updating with DBDELTA, remove any primary keys which could be modified due to schema updates
		//  */
		// if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}woocommerce_downloadable_product_permissions';" ) ) {
		// 	if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}woocommerce_downloadable_product_permissions` LIKE 'permission_id';" ) ) {
		// 		$wpdb->query( "ALTER TABLE {$wpdb->prefix}woocommerce_downloadable_product_permissions DROP PRIMARY KEY, ADD `permission_id` bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT;" );
		// 	}
		// }
		// dbDelta( self::get_schema() );





		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}timetrader_reservation (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					client_id mediumint(9) NOT NULL,
					subject varchar(200),
					description varchar(1000),
					status_id mediumint(9) NOT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;
				CREATE TABLE {$wpdb->prefix}timetrader_status (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					description varchar(45),
					UNIQUE KEY id (id)
				) $charset_collate;
				CREATE TABLE {$wpdb->prefix}timetrader_client (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					name varchar(45),
					email varchar(45),
					telephone varchar(45),
					age int,
					gender varchar(45),
					relative varchar(45),
					states varchar(45),
					skype varchar(45),
					UNIQUE KEY id (id)
				) $charset_collate;
				CREATE TABLE {$wpdb->prefix}timetrader_date_available (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					date_available DATE,
					UNIQUE KEY id (id)
				) $charset_collate;
				CREATE TABLE {$wpdb->prefix}timetrader_time_available (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					time_available DATE,
					UNIQUE KEY id (id)
				) $charset_collate;
				CREATE TABLE {$wpdb->prefix}timetrader_time_available (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					time_available_id mediumint(9) NOT NULL,
					date_available_id mediumint(9) NOT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );


		// $welcome_name = 'Mr. WordPress';
		// $welcome_text = 'Congratulations, you just completed the installation!';

		// $table_name = $wpdb->prefix . 'INDIORLEIDEOLIVEIRA';

		// $wpdb->insert( 
		// 	$table_name,
		// 	array(
		// 		'time' => current_time( 'mysql' ),
		// 		'name' => $welcome_name,
		// 		'text' => $welcome_text,
		// 		)
		// 	);




	}



	/**
	 * Create roles and capabilities
	 */
	public static function create_roles() {
		// global $wp_roles;

		// if ( ! class_exists( 'WP_Roles' ) ) {
		// 	return;
		// }

		// if ( ! isset( $wp_roles ) ) {
		// 	$wp_roles = new WP_Roles();
		// }

		// // Customer role
		// add_role( 'customer', __( 'Customer', 'woocommerce' ), array(
		// 	'read' 						=> true,
		// 	'edit_posts' 				=> false,
		// 	'delete_posts' 				=> false
		// ) );

		// // Shop manager role
		// add_role( 'shop_manager', __( 'Shop Manager', 'woocommerce' ), array(
		// 	'level_9'                => true,
		// 	'level_8'                => true,
		// 	'level_7'                => true,
		// 	'level_6'                => true,
		// 	'level_5'                => true,
		// 	'level_4'                => true,
		// 	'level_3'                => true,
		// 	'level_2'                => true,
		// 	'level_1'                => true,
		// 	'level_0'                => true,
		// 	'read'                   => true,
		// 	'read_private_pages'     => true,
		// 	'read_private_posts'     => true,
		// 	'edit_users'             => true,
		// 	'edit_posts'             => true,
		// 	'edit_pages'             => true,
		// 	'edit_published_posts'   => true,
		// 	'edit_published_pages'   => true,
		// 	'edit_private_pages'     => true,
		// 	'edit_private_posts'     => true,
		// 	'edit_others_posts'      => true,
		// 	'edit_others_pages'      => true,
		// 	'publish_posts'          => true,
		// 	'publish_pages'          => true,
		// 	'delete_posts'           => true,
		// 	'delete_pages'           => true,
		// 	'delete_private_pages'   => true,
		// 	'delete_private_posts'   => true,
		// 	'delete_published_pages' => true,
		// 	'delete_published_posts' => true,
		// 	'delete_others_posts'    => true,
		// 	'delete_others_pages'    => true,
		// 	'manage_categories'      => true,
		// 	'manage_links'           => true,
		// 	'moderate_comments'      => true,
		// 	'unfiltered_html'        => true,
		// 	'upload_files'           => true,
		// 	'export'                 => true,
		// 	'import'                 => true,
		// 	'list_users'             => true
		// ) );

		// $capabilities = self::get_core_capabilities();

		// foreach ( $capabilities as $cap_group ) {
		// 	foreach ( $cap_group as $cap ) {
		// 		$wp_roles->add_cap( 'shop_manager', $cap );
		// 		$wp_roles->add_cap( 'administrator', $cap );
		// 	}
		// }
	}

	/**
	 * Get capabilities for WooCommerce - these are assigned to admin/shop manager during installation or reset
	 *
	 * @return array
	 */
	 private static function get_core_capabilities() {
		// $capabilities = array();

		// $capabilities['core'] = array(
		// 	'manage_woocommerce',
		// 	'view_woocommerce_reports'
		// );

		// $capability_types = array( 'product', 'shop_order', 'shop_coupon', 'shop_webhook' );

		// foreach ( $capability_types as $capability_type ) {

		// 	$capabilities[ $capability_type ] = array(
		// 		// Post type
		// 		"edit_{$capability_type}",
		// 		"read_{$capability_type}",
		// 		"delete_{$capability_type}",
		// 		"edit_{$capability_type}s",
		// 		"edit_others_{$capability_type}s",
		// 		"publish_{$capability_type}s",
		// 		"read_private_{$capability_type}s",
		// 		"delete_{$capability_type}s",
		// 		"delete_private_{$capability_type}s",
		// 		"delete_published_{$capability_type}s",
		// 		"delete_others_{$capability_type}s",
		// 		"edit_private_{$capability_type}s",
		// 		"edit_published_{$capability_type}s",

		// 		// Terms
		// 		"manage_{$capability_type}_terms",
		// 		"edit_{$capability_type}_terms",
		// 		"delete_{$capability_type}_terms",
		// 		"assign_{$capability_type}_terms"
		// 	);
		// }

		// return $capabilities;
	}

	/**
	 * woocommerce_remove_roles function.
	 */
	public static function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'shop_manager', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}

		remove_role( 'customer' );
		remove_role( 'shop_manager' );
	}

	/**
	 * Create files/directories
	 */
	private static function create_files() {
		// // Install files and folders for uploading files and prevent hotlinking
		// $upload_dir =  wp_upload_dir();

		// $files = array(
		// 	array(
		// 		'base' 		=> $upload_dir['basedir'] . '/woocommerce_uploads',
		// 		'file' 		=> '.htaccess',
		// 		'content' 	=> 'deny from all'
		// 	),
		// 	array(
		// 		'base' 		=> $upload_dir['basedir'] . '/woocommerce_uploads',
		// 		'file' 		=> 'index.html',
		// 		'content' 	=> ''
		// 	),
		// 	array(
		// 		'base' 		=> WC_LOG_DIR,
		// 		'file' 		=> '.htaccess',
		// 		'content' 	=> 'deny from all'
		// 	),
		// 	array(
		// 		'base' 		=> WC_LOG_DIR,
		// 		'file' 		=> 'index.html',
		// 		'content' 	=> ''
		// 	)
		// );

		// foreach ( $files as $file ) {
		// 	if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
		// 		if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
		// 			fwrite( $file_handle, $file['content'] );
		// 			fclose( $file_handle );
		// 		}
		// 	}
		// }
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 */
	public static function in_plugin_update_message( $args ) {
		// $transient_name = 'wc_upgrade_notice_' . $args['Version'];

		// if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {
		// 	$response = wp_remote_get( 'https://plugins.svn.wordpress.org/woocommerce/trunk/readme.txt' );

		// 	if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
		// 		$upgrade_notice = self::parse_update_notice( $response['body'] );
		// 		set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
		// 	}
		// }

		// echo wp_kses_post( $upgrade_notice );
	}

	/**
	 * Parse update notice from readme file
	 * @param  string $content
	 * @return string
	 */
	private static function parse_update_notice( $content ) {
		// // Output Upgrade Notice
		// $matches        = null;
		// $regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( WC_VERSION ) . '\s*=|$)~Uis';
		// $upgrade_notice = '';

		// if ( preg_match( $regexp, $content, $matches ) ) {
		// 	$version = trim( $matches[1] );
		// 	$notices = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );

		// 	if ( version_compare( WC_VERSION, $version, '<' ) ) {

		// 		$upgrade_notice .= '<div class="wc_plugin_upgrade_notice">';

		// 		foreach ( $notices as $index => $line ) {
		// 			$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) );
		// 		}

		// 		$upgrade_notice .= '</div> ';
		// 	}
		// }

		// return wp_kses_post( $upgrade_notice );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public static function plugin_action_links( $links ) {
		// $action_links = array(
		// 	'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings' ) . '" title="' . esc_attr( __( 'View WooCommerce Settings', 'woocommerce' ) ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>',
		// );

		// return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public static function plugin_row_meta( $links, $file ) {
		// if ( $file == WC_PLUGIN_BASENAME ) {
		// 	$row_meta = array(
		// 		'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_docs_url', 'http://docs.woothemes.com/documentation/plugins/woocommerce/' ) ) . '" title="' . esc_attr( __( 'View WooCommerce Documentation', 'woocommerce' ) ) . '">' . __( 'Docs', 'woocommerce' ) . '</a>',
		// 		'apidocs' => '<a href="' . esc_url( apply_filters( 'woocommerce_apidocs_url', 'http://docs.woothemes.com/wc-apidocs/' ) ) . '" title="' . esc_attr( __( 'View WooCommerce API Docs', 'woocommerce' ) ) . '">' . __( 'API Docs', 'woocommerce' ) . '</a>',
		// 		'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_support_url', 'http://support.woothemes.com/' ) ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support Forum', 'woocommerce' ) ) . '">' . __( 'Premium Support', 'woocommerce' ) . '</a>',
		// 	);

		// 	return array_merge( $links, $row_meta );
		// }

		// return (array) $links;
	}

	public static function wpmu_drop_tables( $tables ) {
		global $wpdb;

		$tables[] = $wpdb->prefix . "timetrader_reservation";
		$tables[] = $wpdb->prefix . "timetrader_status";
		$tables[] = $wpdb->prefix . "timetrader_client";
		$tables[] = $wpdb->prefix . "timetrader_date_available";
		$tables[] = $wpdb->prefix . "timetrader_time_available";
		$tables[] = $wpdb->prefix . "timetrader_time_available";

		return $tables;
	}
}

TimetraderInstall::init();
