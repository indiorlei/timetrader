<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
* timetrader Autoloader
*
* @class 		TT_Autoloader
* @version		0.1
* @package		timetrader/Classes/
* @category		Class
* @author 		Indiorlei de Oliveira (Agência Cion)
*/
class TT_Autoloader {
	private $include_path = '';

	public function __construct() {
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit( plugin_dir_path( TT_PLUGIN_FILE ) ) . '/includes/';
	}

	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
	}

	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once( $path );
			return true;
		}
		return false;
	}

	public function autoload( $class ) {
		$class = strtolower( $class );
		$file  = $this->get_file_name_from_class( $class );
		$path  = '';

		if ( strpos( $class, 'tt_addons_gateway_' ) === 0 ) {
			$path = $this->include_path . 'gateways/' . substr( str_replace( '_', '-', $class ), 18 ) . '/';
		} elseif ( strpos( $class, 'tt_gateway_' ) === 0 ) {
			$path = $this->include_path . 'gateways/' . substr( str_replace( '_', '-', $class ), 11 ) . '/';
		} elseif ( strpos( $class, 'tt_shipping_' ) === 0 ) {
			$path = $this->include_path . 'shipping/' . substr( str_replace( '_', '-', $class ), 12 ) . '/';
		} elseif ( strpos( $class, 'tt_shortcode_' ) === 0 ) {
			$path = $this->include_path . 'shortcodes/';
		} elseif ( strpos( $class, 'tt_meta_box' ) === 0 ) {
			$path = $this->include_path . 'admin/meta-boxes/';
		} elseif ( strpos( $class, 'tt_admin' ) === 0 ) {
			$path = $this->include_path . 'admin/';
		}

		if ( empty( $path ) || ( ! $this->load_file( $path . $file ) && strpos( $class, 'tt_' ) === 0 ) ) {
			$this->load_file( $this->include_path . $file );
		}
	}
}

new TT_Autoloader();