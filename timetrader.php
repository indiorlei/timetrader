<?php
/*
Plugin Name: Timetrader
Plugin URI: http://agenciacion.com/
Description: Marcador de horário
Version: 0.1
Author: Indiorlei de Oliveira (Agência Cion)
Author URI: http://agenciacion.com/
Requires at least: 4.0
Tested up to: 4.2
License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'TimeTrader' ) ) :

/**
* Main TimeTrader Class
*
*/
final class TimeTrader {
	public $version = '0.1';
	protected static $_instance = null;
	public $session = null;
	public $query = null;
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'timetrader_loaded' );
	}

	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'TimetraderInstall', 'install' ) );
		// add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	private function define_constants() {

		$upload_dir = wp_upload_dir();

		$this->define( 'TT_PLUGIN_FILE', __FILE__ );
		$this->define( 'TT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'TT_VERSION', $this->version );
		$this->define( 'TIMETRADER_VERSION', $this->version );
		$this->define( 'TT_ROUNDING_PRECISION', 4 );
		$this->define( 'TT_TAX_ROUNDING_MODE', 'yes' === get_option( 'timetrader_prices_include_tax', 'no' ) ? 2 : 1 );
		$this->define( 'TT_DELIMITER', '|' );
		$this->define( 'TT_LOG_DIR', $upload_dir['basedir'] . '/tt-logs/' );

	}

	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	private function is_request( $type ) {

		// switch ( $type ) {
		// 	case 'admin' :
		// 		return is_admin();
		// 	case 'ajax' :
		// 		return defined( 'DOING_AJAX' );
		// 	case 'cron' :
		// 		return defined( 'DOING_CRON' );
		// 	case 'frontend' :
		// 		return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		// }

	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {

		include_once( 'class/install.php' );
		include_once( 'class/post-types.php' ); // Registers post types

		// include_once( 'includes/class-wc-autoloader.php' );
		// include_once( 'includes/wc-core-functions.php' );
		// include_once( 'includes/wc-widget-functions.php' );
		// include_once( 'includes/wc-webhook-functions.php' );
		// include_once( 'includes/class-wc-install.php' );
		// include_once( 'includes/class-wc-geolocation.php' );
		// include_once( 'includes/class-wc-download-handler.php' );
		// include_once( 'includes/class-wc-comments.php' );
		// include_once( 'includes/class-wc-post-data.php' );

		// if ( $this->is_request( 'admin' ) ) {
		// 	include_once( 'includes/admin/class-wc-admin.php' );
		// }

		// if ( $this->is_request( 'ajax' ) ) {
		// 	$this->ajax_includes();
		// }

		// if ( $this->is_request( 'frontend' ) ) {
		// 	$this->frontend_includes();
		// }

		// if ( $this->is_request( 'cron' ) && 'yes' === get_option( 'timetrader_allow_tracking', 'no' ) ) {
		// 	include_once( 'includes/class-wc-tracker.php' );
		// }

		// $this->query = include( 'includes/class-wc-query.php' );                // The main query class
		// $this->api   = include( 'includes/class-wc-api.php' );                  // API Class

		// include_once( 'includes/class-wc-post-types.php' );                     // Registers post types
		// include_once( 'includes/abstracts/abstract-wc-product.php' );           // Products
		// include_once( 'includes/abstracts/abstract-wc-order.php' );             // Orders
		// include_once( 'includes/abstracts/abstract-wc-settings-api.php' );      // Settings API (for gateways, shipping, and integrations)
		// include_once( 'includes/abstracts/abstract-wc-shipping-method.php' );   // A Shipping method
		// include_once( 'includes/abstracts/abstract-wc-payment-gateway.php' );   // A Payment gateway
		// include_once( 'includes/abstracts/abstract-wc-integration.php' );       // An integration with a service
		// include_once( 'includes/class-wc-product-factory.php' );                // Product factory
		// include_once( 'includes/class-wc-countries.php' );                      // Defines countries and states
		// include_once( 'includes/class-wc-integrations.php' );                   // Loads integrations
		// include_once( 'includes/class-wc-cache-helper.php' );                   // Cache Helper
		// include_once( 'includes/class-wc-language-pack-upgrader.php' );         // Download/update languages
	}

	/**
	 * Include required ajax files.
	 */
	public function ajax_includes() {
		// include_once( 'includes/class-wc-ajax.php' );                           // Ajax functions for admin and the front-end
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		// include_once( 'includes/wc-cart-functions.php' );
		// include_once( 'includes/wc-notice-functions.php' );
		// include_once( 'includes/abstracts/abstract-wc-session.php' );
		// include_once( 'includes/class-wc-session-handler.php' );
		// include_once( 'includes/wc-template-hooks.php' );
		// include_once( 'includes/class-wc-template-loader.php' );                // Template Loader
		// include_once( 'includes/class-wc-frontend-scripts.php' );               // Frontend Scripts
		// include_once( 'includes/class-wc-form-handler.php' );                   // Form Handlers
		// include_once( 'includes/class-wc-cart.php' );                           // The main cart class
		// include_once( 'includes/class-wc-tax.php' );                            // Tax class
		// include_once( 'includes/class-wc-customer.php' );                       // Customer class
		// include_once( 'includes/class-wc-shortcodes.php' );                     // Shortcodes class
		// include_once( 'includes/class-wc-https.php' );                          // https Helper
	}

	/**
	 * Function used to Init timetrader Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once( 'class/template-functions.php' );
	}

	/**
	 * Init timetrader when WordPress Initialises.
	 */
	public function init() {
		// // Before init action
		// do_action( 'before_timetrader_init' );

		// // Set up localisation
		// $this->load_plugin_textdomain();

		// // Load class instances
		// $this->product_factory = new WC_Product_Factory();                      // Product Factory to create new product instances
		// $this->order_factory   = new WC_Order_Factory();                        // Order Factory to create new order instances
		// $this->countries       = new WC_Countries();                            // Countries class
		// $this->integrations    = new WC_Integrations();                         // Integrations class

		// // Classes/actions loaded for the frontend and for ajax requests
		// if ( $this->is_request( 'frontend' ) ) {
		// 	// Session class, handles session data for users - can be overwritten if custom handler is needed
		// 	$session_class = apply_filters( 'timetrader_session_handler', 'WC_Session_Handler' );

		// 	// Class instances
		// 	$this->session  = new $session_class();
		// 	$this->cart     = new WC_Cart();                                    // Cart class, stores the cart contents
		// 	$this->customer = new WC_Customer();                                // Customer class, handles data such as customer location
		// }

		// $this->load_webhooks();

		// Init action
		// do_action( 'timetrader_init' );
	}

	

	
	/**
	 * Ensure post thumbnail support is turned on
	 */
	private function add_thumbnail_support() {
		// if ( ! current_theme_supports( 'post-thumbnails' ) ) {
		// 	add_theme_support( 'post-thumbnails' );
		// }
		// add_post_type_support( 'product', 'thumbnail' );
	}

	/**
	 * Add TT Image sizes to WP
	 *
	 */
	private function add_image_sizes() {
		// $shop_thumbnail = tt_get_image_size( 'shop_thumbnail' );
		// $shop_catalog	= tt_get_image_size( 'shop_catalog' );
		// $shop_single	= tt_get_image_size( 'shop_single' );

		// add_image_size( 'shop_thumbnail', $shop_thumbnail['width'], $shop_thumbnail['height'], $shop_thumbnail['crop'] );
		// add_image_size( 'shop_catalog', $shop_catalog['width'], $shop_catalog['height'], $shop_catalog['crop'] );
		// add_image_size( 'shop_single', $shop_single['width'], $shop_single['height'], $shop_single['crop'] );
	}

	/**
	 * Fix `$_SERVER` variables for various setups.
	 *
	 * Note: Removed IIS handling due to wp_fix_server_vars()
	 *
	 * @since 2.3
	 */
	private function fix_server_vars() {
		// NGINX Proxy
		if ( ! isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_SERVER['HTTP_REMOTE_ADDR'] ) ) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'];
		}

		if ( ! isset( $_SERVER['HTTPS'] ) ) {
			if ( ! empty( $_SERVER['HTTP_HTTPS'] ) ) {
				$_SERVER['HTTPS'] = $_SERVER['HTTP_HTTPS'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
				$_SERVER['HTTPS'] = '1';
			}
		}
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		// return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		// return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		// return apply_filters( 'timetrader_template_path', 'timetrader/' );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		// return admin_url( 'admin-ajax.php', 'relative' );
	}

}

endif;

function TT() {
	return TimeTrader::instance();
}

// Global for backwards compatibility.
$GLOBALS['timetrader'] = TT();
