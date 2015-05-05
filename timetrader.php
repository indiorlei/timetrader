<?php
/*
Plugin Name: timetrader
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

if ( ! class_exists( 'timetrader' ) ) :

/**
 * Main timetrader Class
 *
 * @class timetrader
 * @version	0.1
 */
final class timetrader {
	public $version = '0.1';

	/**
	* @var timetrader The single instance of the class
	* @since 0.1
	*/
	protected static $_instance = null;
	public $session = null;
	public $query = null;
	public $product_factory = null;
	public $countries = null;
	public $integrations = null;
	public $cart = null;
	public $customer = null;
	public $order_factory = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	* Cloning is forbidden.
	* @since 0.1
	*/
	public function __clone() {
		// _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'timetrader' ), '0.1' );
	}

	/**
	* Unserializing instances of this class is forbidden.
	* @since 0.1
	*/
	public function __wakeup() {
		// _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'timetrader' ), '0.1' );
	}

	/**
	* Auto-load in-accessible properties on demand.
	* @param mixed $key
	* @return mixed
	*/
	public function __get( $key ) {
		// if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ) ) ) {
		// 	return $this->$key();
		// }
	}

	/**
	* timetrader Constructor.
	*/
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'timetrader_loaded' );
	}

	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'TT_Install', 'install' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'TT_Shortcodes', 'init' ) );
		add_action( 'init', array( 'TT_Emails', 'init_transactional_emails' ) );
	}

	/**
	* Define TT Constants
	*/
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

	/**
	* Define constant if not already set
	* @param  string $name
	* @param  string|bool $value
	*/
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	* What type of request is this?
	* string $type ajax, frontend or admin
	* @return bool
	*/
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	* Include required core files used in admin and on the frontend.
	*/
	public function includes() {

		// include_once( 'includes/class-autoloader.php' );
		// include_once( 'includes/tt-core-functions.php' );
		// include_once( 'includes/tt-widget-functions.php' );
		// include_once( 'includes/tt-webhook-functions.php' );
		include_once( 'includes/class-install.php' );
		// include_once( 'includes/class-geolocation.php' );
		// include_once( 'includes/class-download-handler.php' );
		// include_once( 'includes/class-comments.php' );
		// include_once( 'includes/class-post-data.php' );

		// if ( $this->is_request( 'admin' ) ) {
		// 	include_once( 'includes/admin/class-admin.php' );
		// }

		// if ( $this->is_request( 'ajax' ) ) {
		// 	$this->ajax_includes();
		// }

		// if ( $this->is_request( 'frontend' ) ) {
		// 	$this->frontend_includes();
		// }

		// if ( $this->is_request( 'cron' ) && 'yes' === get_option( 'timetrader_allow_tracking', 'no' ) ) {
		// 	include_once( 'includes/class-tracker.php' );
		// }

		// $this->query = include( 'includes/class-query.php' );                // The main query class
		// $this->api   = include( 'includes/class-api.php' );                  // API Class

		// include_once( 'includes/class-post-types.php' );                     // Registers post types
		// include_once( 'includes/abstracts/abstract-product.php' );           // Products
		// include_once( 'includes/abstracts/abstract-order.php' );             // Orders
		// include_once( 'includes/abstracts/abstract-settings-api.php' );      // Settings API (for gateways, shipping, and integrations)
		// include_once( 'includes/abstracts/abstract-shipping-method.php' );   // A Shipping method
		// include_once( 'includes/abstracts/abstract-payment-gateway.php' );   // A Payment gateway
		// include_once( 'includes/abstracts/abstract-integration.php' );       // An integration with a service
		// include_once( 'includes/class-product-factory.php' );                // Product factory
		// include_once( 'includes/class-countries.php' );                      // Defines countries and states
		// include_once( 'includes/class-integrations.php' );                   // Loads integrations
		// include_once( 'includes/class-cache-helper.php' );                   // Cache Helper
		// include_once( 'includes/class-language-pack-upgrader.php' );         // Download/update languages

	}

	/**
	* Include required ajax files.
	*/
	public function ajax_includes() {
		// include_once( 'includes/class-ajax.php' ); // Ajax functions for admin and the front-end
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		// include_once( 'includes/tt-cart-functions.php' );
		// include_once( 'includes/tt-notice-functions.php' );
		// include_once( 'includes/abstracts/abstract-session.php' );
		// include_once( 'includes/class-session-handler.php' );
		// include_once( 'includes/tt-template-hooks.php' );
		// include_once( 'includes/class-template-loader.php' );                // Template Loader
		// include_once( 'includes/class-frontend-scripts.php' );               // Frontend Scripts
		// include_once( 'includes/class-form-handler.php' );                   // Form Handlers
		// include_once( 'includes/class-cart.php' );                           // The main cart class
		// include_once( 'includes/class-tax.php' );                            // Tax class
		// include_once( 'includes/class-customer.php' );                       // Customer class
		// include_once( 'includes/class-shortcodes.php' );                     // Shortcodes class
		// include_once( 'includes/class-https.php' );                          // https Helper
	}

	/**
	 * Function used to Init timetrader Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		// include_once( 'includes/tt-template-functions.php' );
	}

	/**
	 * Init timetrader when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		// do_action( 'before_timetrader_init' );

		// Set up localisation
		// $this->load_plugin_textdomain();

		// Load class instances
		// $this->product_factory = new TT_Product_Factory();                      // Product Factory to create new product instances
		// $this->order_factory   = new TT_Order_Factory();                        // Order Factory to create new order instances
		// $this->countries       = new TT_Countries();                            // Countries class
		// $this->integrations    = new TT_Integrations();                         // Integrations class

		// Classes/actions loaded for the frontend and for ajax requests
		// if ( $this->is_request( 'frontend' ) ) {
			// Session class, handles session data for users - can be overwritten if custom handler is needed
			// $session_class = apply_filters( 'timetrader_session_handler', 'TT_Session_Handler' );

			// Class instances
		// 	$this->session  = new $session_class();
		// 	$this->cart     = new TT_Cart();                                    // Cart class, stores the cart contents
		// 	$this->customer = new TT_Customer();                                // Customer class, handles data such as customer location
		// }

		// $this->load_webhooks();

		// Init action
		// do_action( 'timetrader_init' );
	}

	/**
	* Load Localisation files.
	*
	* Note: the first-loaded translation file overrides any following ones if the same translation is present.
	*
	* Admin Locales are found in:
	* 		- WP_LANG_DIR/timetrader/timetrader-admin-LOCALE.mo
	* 		- WP_LANG_DIR/plugins/timetrader-admin-LOCALE.mo
	*
	* Frontend/global Locales found in:
	* 		- WP_LANG_DIR/timetrader/timetrader-LOCALE.mo
	* 	 	- timetrader/i18n/languages/timetrader-LOCALE.mo (which if not found falls back to:)
	* 	 	- WP_LANG_DIR/plugins/timetrader-LOCALE.mo
	*/
	public function load_plugin_textdomain() {
		// $locale = apply_filters( 'plugin_locale', get_locale(), 'timetrader' );
		// if ( $this->is_request( 'admin' ) ) {
		// 	load_textdomain( 'timetrader', WP_LANG_DIR . '/timetrader/timetrader-admin-' . $locale . '.mo' );
		// 	load_textdomain( 'timetrader', WP_LANG_DIR . '/plugins/timetrader-admin-' . $locale . '.mo' );
		// }
		// load_textdomain( 'timetrader', WP_LANG_DIR . '/timetrader/timetrader-' . $locale . '.mo' );
		// load_plugin_textdomain( 'timetrader', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
	}

	/**
	* Ensure theme and server variable compatibility and setup image sizes.
	*/
	public function setup_environment() {
		// $this->define( 'TT_TEMPLATE_PATH', $this->template_path() );
		// $this->add_thumbnail_support();
		// $this->add_image_sizes();
		// $this->fix_server_vars();
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
	* @since 2.3
	*/
	private function add_image_sizes() {
		// $shop_thumbnail = TT_get_image_size( 'shop_thumbnail' );
		// $shop_catalog	= TT_get_image_size( 'shop_catalog' );
		// $shop_single	= TT_get_image_size( 'shop_single' );
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
		// if ( ! isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_SERVER['HTTP_REMOTE_ADDR'] ) ) {
		// 	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'];
		// }

		// if ( ! isset( $_SERVER['HTTPS'] ) ) {
		// 	if ( ! empty( $_SERVER['HTTP_HTTPS'] ) ) {
		// 		$_SERVER['HTTPS'] = $_SERVER['HTTP_HTTPS'];
		// 	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
		// 		$_SERVER['HTTPS'] = '1';
		// 	}
		// }
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

	/**
	 * Return the TT API URL for a given request
	 *
	 * @param string $request
	 * @param mixed $ssl (default: null)
	 * @return string
	 */
	public function api_request_url( $request, $ssl = null ) {
		// if ( is_null( $ssl ) ) {
		// 	$scheme = parse_url( home_url(), PHP_URL_SCHEME );
		// } elseif ( $ssl ) {
		// 	$scheme = 'https';
		// } else {
		// 	$scheme = 'http';
		// }

		// if ( strstr( get_option( 'permalink_structure' ), '/index.php/' ) ) {
		// 	$api_request_url = trailingslashit( home_url( '/index.php/tt-api/' . $request, $scheme ) );
		// } elseif ( get_option( 'permalink_structure' ) ) {
		// 	$api_request_url = trailingslashit( home_url( '/tt-api/' . $request, $scheme ) );
		// } else {
		// 	$api_request_url = add_query_arg( 'tt-api', $request, trailingslashit( home_url( '', $scheme ) ) );
		// }

		// return esc_url_raw( $api_request_url );
	}

	/**
	 * Load & enqueue active webhooks
	 *
	 * @since 2.2
	 */
	private function load_webhooks() {
		// if ( false === ( $webhooks = get_transient( 'timetrader_webhook_ids' ) ) ) {
		// 	$webhooks = get_posts( array(
		// 		'fields'         => 'ids',
		// 		'post_type'      => 'shop_webhook',
		// 		'post_status'    => 'publish',
		// 		'posts_per_page' => -1
		// 	) );
		// 	set_transient( 'timetrader_webhook_ids', $webhooks );
		// }
		// foreach ( $webhooks as $webhook_id ) {
		// 	$webhook = new TT_Webhook( $webhook_id );
		// 	$webhook->enqueue();
		// }
	}

	/**
	 * Get Checkout Class.
	 * @return TT_Checkout
	 */
	public function checkout() {
		// return TT_Checkout::instance();
	}

	/**
	 * Get gateways class
	 * @return TT_Payment_Gateways
	 */
	public function payment_gateways() {
		// return TT_Payment_Gateways::instance();
	}

	/**
	 * Get shipping class
	 * @return TT_Shipping
	 */
	public function shipping() {
		// return TT_Shipping::instance();
	}

	/**
	 * Email Class.
	 * @return TT_Emails
	 */
	public function mailer() {
		// return TT_Emails::instance();
	}
}

endif;

/**
 * Returns the main instance of TT to prevent the need to use globals.
 *
 * @since  2.1
 * @return timetrader
 */
function TT() {
	return timetrader::instance();
}

// Global for backwards compatibility.
$GLOBALS['timetrader'] = TT();