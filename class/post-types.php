<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies
 *
 * @class       TimeTrader_PostTypes
 * @author      Indiorlei de Oliveira (Agência Cion)
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
* TimeTrader_PostTypes Class
*/
class TimeTrader_PostTypes {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );


		
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
		// add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {


		register_taxonomy( 'timetrader-categories', 'timetrader', array(
			'labels' => array(
				'name' => 'Categoria do timetrader',
				'add_new_item' => 'Adicionar Nova Categoria do timetrader',
				'new_item_name' => "Nova Categoria do timetrader"
				),
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true
			)
		);


	}


	public static function register_post_types() {


		register_post_type( 'timetrader', array(
			'labels' => array(
				'name' => 'timetraders',
				'singular_name' => 'timetrader',
				'add_new' => 'Adicionar Novo',
				'add_new_item' => 'Adicionar Novo timetrader',
				'edit' => 'Editar',
				'edit_item' => 'Editar timetrader',
				'new_item' => 'Novo timetrader',
				'view' => 'Ver',
				'view_item' => 'Ver timetrader',
				'search_items' => 'Procurar timetraders',
				'not_found' => 'Nenhum timetrader encontrado',
				'not_found_in_trash' => 'Nenhum timetrader encontrado na Lixeira',
				'parent' => 'timetrader Similar'
				),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'menu_position' => 15,
			'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
			'taxonomies' => array( 'timetrader-categories' ),
			'has_archive' => true
			)
		);


	}











	/**
	 * Register our custom post statuses, used for order status.
	 */
	public static function register_post_status() {

		// register_post_status( 'wc-pending', array(
		// 	'label'                     => _x( 'Pending payment', 'Order status', 'timetrader' ),
		// 	'public'                    => false,
		// 	'exclude_from_search'       => false,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Pending payment <span class="count">(%s)</span>', 'Pending payment <span class="count">(%s)</span>', 'timetrader' )
		// ) );

		// register_post_status( 'wc-processing', array(
		// 	'label'                     => _x( 'Processing', 'Order status', 'timetrader' ),
		// 	'public'                    => false,
		// 	'exclude_from_search'       => false,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'timetrader' )
		// ) );

		// register_post_status( 'wc-on-hold', array(
		// 	'label'                     => _x( 'On hold', 'Order status', 'timetrader' ),
		// 	'public'                    => false,
		// 	'exclude_from_search'       => false,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'On hold <span class="count">(%s)</span>', 'On hold <span class="count">(%s)</span>', 'timetrader' )
		// ) );

		// register_post_status( 'wc-completed', array(
		// 	'label'                     => _x( 'Completed', 'Order status', 'timetrader' ),
		// 	'public'                    => false,
		// 	'exclude_from_search'       => false,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'timetrader' )
		// ) );

		// register_post_status( 'wc-cancelled', array(
		// 	'label'                     => _x( 'Cancelled', 'Order status', 'timetrader' ),
		// 	'public'                    => false,
		// 	'exclude_from_search'       => false,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'timetrader' )
		// ) );

		// register_post_status( 'wc-refunded', array(
		// 	'label'                     => _x( 'Refunded', 'Order status', 'timetrader' ),
		// 	'public'                    => false,
		// 	'exclude_from_search'       => false,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'timetrader' )
		// ) );

		// register_post_status( 'wc-failed', array(
		// 	'label'                     => _x( 'Failed', 'Order status', 'timetrader' ),
		// 	'public'                    => false,
		// 	'exclude_from_search'       => false,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'timetrader' )
		// ) );

	}

	// /**
	//  * Add Product Support to Jetpack Omnisearch.
	//  */
	// public static function support_jetpack_omnisearch() {
	// 	// if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
	// 	// 	new Jetpack_Omnisearch_Posts( 'product' );
	// 	// }
	// }
}

TimeTrader_PostTypes::init();
