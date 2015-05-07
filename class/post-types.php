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
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
	}


	public static function register_taxonomies() {

		if ( taxonomy_exists( 'product_type' ) ) {
			return;
		}

		do_action( 'timetrader_register_taxonomy' );
		$permalinks = get_option( 'timetrader_permalinks' );

		// register_taxonomy( 'product_type',
		// 	apply_filters( 'timetrader_taxonomy_objects_product_type', array( 'product' ) ),
		// 	apply_filters( 'timetrader_taxonomy_args_product_type', array(
		// 		'hierarchical'      => false,
		// 		'show_ui'           => false,
		// 		'show_in_nav_menus' => false,
		// 		'query_var'         => is_admin(),
		// 		'rewrite'           => false,
		// 		'public'            => false
		//    );

		// register_taxonomy( 'product_cat',
		// 	apply_filters( 'timetrader_taxonomy_objects_product_cat', array( 'product' ) ),
		// 	apply_filters( 'timetrader_taxonomy_args_product_cat', array(
		// 		'hierarchical'          => true,
		// 		'update_count_callback' => '_wc_term_recount',
		// 		'label'                 => __( 'Product Categories', 'timetrader' ),
		// 		'labels' => array(
		// 				'name'              => __( 'Product Categories', 'timetrader' ),
		// 				'singular_name'     => __( 'Product Category', 'timetrader' ),
		// 				'menu_name'         => _x( 'Categories', 'Admin menu name', 'timetrader' ),
		// 				'search_items'      => __( 'Search Product Categories', 'timetrader' ),
		// 				'all_items'         => __( 'All Product Categories', 'timetrader' ),
		// 				'parent_item'       => __( 'Parent Product Category', 'timetrader' ),
		// 				'parent_item_colon' => __( 'Parent Product Category:', 'timetrader' ),
		// 				'edit_item'         => __( 'Edit Product Category', 'timetrader' ),
		// 				'update_item'       => __( 'Update Product Category', 'timetrader' ),
		// 				'add_new_item'      => __( 'Add New Product Category', 'timetrader' ),
		// 				'new_item_name'     => __( 'New Product Category Name', 'timetrader' )
		// 			),
		// 		'show_ui'               => true,
		// 		'query_var'             => true,
		// 		'capabilities'          => array(
		// 			'manage_terms' => 'manage_product_terms',
		// 			'edit_terms'   => 'edit_product_terms',
		// 			'delete_terms' => 'delete_product_terms',
		// 			'assign_terms' => 'assign_product_terms',
		// 		),
		// 		'rewrite'               => array(
		// 			'slug'         => empty( $permalinks['category_base'] ) ? _x( 'product-category', 'slug', 'timetrader' ) : $permalinks['category_base'],
		// 			'with_front'   => false,
		// 			'hierarchical' => true,
		// 		),
		// 	) )
		// );

		// register_taxonomy( 'product_tag',
		// 	apply_filters( 'timetrader_taxonomy_objects_product_tag', array( 'product' ) ),
		// 	apply_filters( 'timetrader_taxonomy_args_product_tag', array(
		// 		'hierarchical'          => false,
		// 		'update_count_callback' => '_wc_term_recount',
		// 		'label'                 => __( 'Product Tags', 'timetrader' ),
		// 		'labels'                => array(
		// 				'name'                       => __( 'Product Tags', 'timetrader' ),
		// 				'singular_name'              => __( 'Product Tag', 'timetrader' ),
		// 				'menu_name'                  => _x( 'Tags', 'Admin menu name', 'timetrader' ),
		// 				'search_items'               => __( 'Search Product Tags', 'timetrader' ),
		// 				'all_items'                  => __( 'All Product Tags', 'timetrader' ),
		// 				'edit_item'                  => __( 'Edit Product Tag', 'timetrader' ),
		// 				'update_item'                => __( 'Update Product Tag', 'timetrader' ),
		// 				'add_new_item'               => __( 'Add New Product Tag', 'timetrader' ),
		// 				'new_item_name'              => __( 'New Product Tag Name', 'timetrader' ),
		// 				'popular_items'              => __( 'Popular Product Tags', 'timetrader' ),
		// 				'separate_items_with_commas' => __( 'Separate Product Tags with commas', 'timetrader'  ),
		// 				'add_or_remove_items'        => __( 'Add or remove Product Tags', 'timetrader' ),
		// 				'choose_from_most_used'      => __( 'Choose from the most used Product tags', 'timetrader' ),
		// 				'not_found'                  => __( 'No Product Tags found', 'timetrader' ),
		// 			),
		// 		'show_ui'               => true,
		// 		'query_var'             => true,
		// 		'capabilities'          => array(
		// 			'manage_terms' => 'manage_product_terms',
		// 			'edit_terms'   => 'edit_product_terms',
		// 			'delete_terms' => 'delete_product_terms',
		// 			'assign_terms' => 'assign_product_terms',
		// 		),
		// 		'rewrite'               => array(
		// 			'slug'       => empty( $permalinks['tag_base'] ) ? _x( 'product-tag', 'slug', 'timetrader' ) : $permalinks['tag_base'],
		// 			'with_front' => false
		// 		),
		// 	) )
		// );

		// register_taxonomy( 'product_shipping_class',
		// 	apply_filters( 'timetrader_taxonomy_objects_product_shipping_class', array('product', 'product_variation') ),
		// 	apply_filters( 'timetrader_taxonomy_args_product_shipping_class', array(
		// 		'hierarchical'          => true,
		// 		'update_count_callback' => '_update_post_term_count',
		// 		'label'                 => __( 'Shipping Classes', 'timetrader' ),
		// 		'labels' => array(
		// 				'name'              => __( 'Shipping Classes', 'timetrader' ),
		// 				'singular_name'     => __( 'Shipping Class', 'timetrader' ),
		// 				'menu_name'         => _x( 'Shipping Classes', 'Admin menu name', 'timetrader' ),
		// 				'search_items'      => __( 'Search Shipping Classes', 'timetrader' ),
		// 				'all_items'         => __( 'All Shipping Classes', 'timetrader' ),
		// 				'parent_item'       => __( 'Parent Shipping Class', 'timetrader' ),
		// 				'parent_item_colon' => __( 'Parent Shipping Class:', 'timetrader' ),
		// 				'edit_item'         => __( 'Edit Shipping Class', 'timetrader' ),
		// 				'update_item'       => __( 'Update Shipping Class', 'timetrader' ),
		// 				'add_new_item'      => __( 'Add New Shipping Class', 'timetrader' ),
		// 				'new_item_name'     => __( 'New Shipping Class Name', 'timetrader' )
		// 			),
		// 		'show_ui'               => false,
		// 		'show_in_nav_menus'     => false,
		// 		'query_var'             => is_admin(),
		// 		'capabilities'          => array(
		// 			'manage_terms' => 'manage_product_terms',
		// 			'edit_terms'   => 'edit_product_terms',
		// 			'delete_terms' => 'delete_product_terms',
		// 			'assign_terms' => 'assign_product_terms',
		// 		),
		// 		'rewrite'               => false,
		// 	) )
		// );

		global $wc_product_attributes;
		$wc_product_attributes = array();

		// if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
		// 	foreach ( $attribute_taxonomies as $tax ) {
		// 		if ( $name = wc_attribute_taxonomy_name( $tax->attribute_name ) ) {
		// 			$tax->attribute_public          = absint( isset( $tax->attribute_public ) ? $tax->attribute_public : 1 );
		// 			$label                          = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
		// 			$wc_product_attributes[ $name ] = $tax;
		// 			$taxonomy_data                  = array(
		// 				'hierarchical'          => true,
		// 				'update_count_callback' => '_update_post_term_count',
		// 				'labels'                => array(
		// 						'name'              => $label,
		// 						'singular_name'     => $label,
		// 						'search_items'      => sprintf( __( 'Search %s', 'timetrader' ), $label ),
		// 						'all_items'         => sprintf( __( 'All %s', 'timetrader' ), $label ),
		// 						'parent_item'       => sprintf( __( 'Parent %s', 'timetrader' ), $label ),
		// 						'parent_item_colon' => sprintf( __( 'Parent %s:', 'timetrader' ), $label ),
		// 						'edit_item'         => sprintf( __( 'Edit %s', 'timetrader' ), $label ),
		// 						'update_item'       => sprintf( __( 'Update %s', 'timetrader' ), $label ),
		// 						'add_new_item'      => sprintf( __( 'Add New %s', 'timetrader' ), $label ),
		// 						'new_item_name'     => sprintf( __( 'New %s', 'timetrader' ), $label )
		// 					),
		// 				'show_ui'           => false,
		// 				'query_var'         => 1 === $tax->attribute_public,
		// 				'rewrite'           => false,
		// 				'sort'              => false,
		// 				'public'            => 1 === $tax->attribute_public,
		// 				'show_in_nav_menus' => 1 === $tax->attribute_public && apply_filters( 'timetrader_attribute_show_in_nav_menus', false, $name ),
		// 				'capabilities'      => array(
		// 					'manage_terms' => 'manage_product_terms',
		// 					'edit_terms'   => 'edit_product_terms',
		// 					'delete_terms' => 'delete_product_terms',
		// 					'assign_terms' => 'assign_product_terms',
		// 				)
		// 			);

		// 			if ( 1 === $tax->attribute_public ) {
		// 				$taxonomy_data['rewrite'] = array(
		// 					'slug'         => empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] ) . sanitize_title( $tax->attribute_name ),
		// 					'with_front'   => false,
		// 					'hierarchical' => true
		// 				);
		// 			}

		// 			register_taxonomy( $name, apply_filters( "timetrader_taxonomy_objects_{$name}", array( 'product' ) ), apply_filters( "timetrader_taxonomy_args_{$name}", $taxonomy_data ) );
		// 		}
		// 	}

		// 	do_action( 'timetrader_after_register_taxonomy' );
		// }
	}





	public static function register_post_types() {

		if ( post_type_exists('product') ) {
			return;
		}

		do_action( 'timetrader_register_post_type' );

		$permalinks        = get_option( 'timetrader_permalinks' );
		$product_permalink = empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'timetrader' ) : $permalinks['product_base'];

		// register_post_type( 'product',
		// 	apply_filters( 'timetrader_register_post_type_product',
		// 		array(
		// 			'labels'              => array(
		// 					'name'               => __( 'Products', 'timetrader' ),
		// 					'singular_name'      => __( 'Product', 'timetrader' ),
		// 					'menu_name'          => _x( 'Products', 'Admin menu name', 'timetrader' ),
		// 					'add_new'            => __( 'Add Product', 'timetrader' ),
		// 					'add_new_item'       => __( 'Add New Product', 'timetrader' ),
		// 					'edit'               => __( 'Edit', 'timetrader' ),
		// 					'edit_item'          => __( 'Edit Product', 'timetrader' ),
		// 					'new_item'           => __( 'New Product', 'timetrader' ),
		// 					'view'               => __( 'View Product', 'timetrader' ),
		// 					'view_item'          => __( 'View Product', 'timetrader' ),
		// 					'search_items'       => __( 'Search Products', 'timetrader' ),
		// 					'not_found'          => __( 'No Products found', 'timetrader' ),
		// 					'not_found_in_trash' => __( 'No Products found in trash', 'timetrader' ),
		// 					'parent'             => __( 'Parent Product', 'timetrader' )
		// 				),
		// 			'description'         => __( 'This is where you can add new products to your store.', 'timetrader' ),
		// 			'public'              => true,
		// 			'show_ui'             => true,
		// 			'capability_type'     => 'product',
		// 			'map_meta_cap'        => true,
		// 			'publicly_queryable'  => true,
		// 			'exclude_from_search' => false,
		// 			'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
		// 			'rewrite'             => $product_permalink ? array( 'slug' => untrailingslashit( $product_permalink ), 'with_front' => false, 'feeds' => true ) : false,
		// 			'query_var'           => true,
		// 			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
		// 			'has_archive'         => ( $shop_page_id = wc_get_page_id( 'shop' ) ) && get_post( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop',
		// 			'show_in_nav_menus'   => true
		// 		)
		// 	)
		// );

		// register_post_type( 'product_variation',
		// 	apply_filters( 'timetrader_register_post_type_product_variation',
		// 		array(
		// 			'label'        => __( 'Variations', 'timetrader' ),
		// 			'public'       => false,
		// 			'hierarchical' => false,
		// 			'supports'     => false
		// 		)
		// 	)
		// );

		// wc_register_order_type(
		// 	'shop_order',
		// 	apply_filters( 'timetrader_register_post_type_shop_order',
		// 		array(
		// 			'labels'              => array(
		// 					'name'               => __( 'Orders', 'timetrader' ),
		// 					'singular_name'      => __( 'Order', 'timetrader' ),
		// 					'add_new'            => __( 'Add Order', 'timetrader' ),
		// 					'add_new_item'       => __( 'Add New Order', 'timetrader' ),
		// 					'edit'               => __( 'Edit', 'timetrader' ),
		// 					'edit_item'          => __( 'Edit Order', 'timetrader' ),
		// 					'new_item'           => __( 'New Order', 'timetrader' ),
		// 					'view'               => __( 'View Order', 'timetrader' ),
		// 					'view_item'          => __( 'View Order', 'timetrader' ),
		// 					'search_items'       => __( 'Search Orders', 'timetrader' ),
		// 					'not_found'          => __( 'No Orders found', 'timetrader' ),
		// 					'not_found_in_trash' => __( 'No Orders found in trash', 'timetrader' ),
		// 					'parent'             => __( 'Parent Orders', 'timetrader' ),
		// 					'menu_name'          => _x( 'Orders', 'Admin menu name', 'timetrader' )
		// 				),
		// 			'description'         => __( 'This is where store orders are stored.', 'timetrader' ),
		// 			'public'              => false,
		// 			'show_ui'             => true,
		// 			'capability_type'     => 'shop_order',
		// 			'map_meta_cap'        => true,
		// 			'publicly_queryable'  => false,
		// 			'exclude_from_search' => true,
		// 			'show_in_menu'        => current_user_can( 'manage_timetrader' ) ? 'timetrader' : true,
		// 			'hierarchical'        => false,
		// 			'show_in_nav_menus'   => false,
		// 			'rewrite'             => false,
		// 			'query_var'           => false,
		// 			'supports'            => array( 'title', 'comments', 'custom-fields' ),
		// 			'has_archive'         => false,
		// 		)
		// 	)
		// );

		// wc_register_order_type(
		// 	'shop_order_refund',
		// 	apply_filters( 'timetrader_register_post_type_shop_order_refund',
		// 		array(
		// 			'label'                            => __( 'Refunds', 'timetrader' ),
		// 			'capability_type'                  => 'shop_order',
		// 			'public'                           => false,
		// 			'hierarchical'                     => false,
		// 			'supports'                         => false,
		// 			'exclude_from_orders_screen'       => false,
		// 			'add_order_meta_boxes'             => false,
		// 			'exclude_from_order_count'         => true,
		// 			'exclude_from_order_views'         => false,
		// 			'exclude_from_order_reports'       => false,
		// 			'exclude_from_order_sales_reports' => true,
		// 			'class_name'                       => 'WC_Order_Refund'
		// 		)
		// 	)
		// );

		// if ( 'yes' == get_option( 'timetrader_enable_coupons' ) ) {
		// 	register_post_type( 'shop_coupon',
		// 		apply_filters( 'timetrader_register_post_type_shop_coupon',
		// 			array(
		// 				'labels'              => array(
		// 						'name'               => __( 'Coupons', 'timetrader' ),
		// 						'singular_name'      => __( 'Coupon', 'timetrader' ),
		// 						'menu_name'          => _x( 'Coupons', 'Admin menu name', 'timetrader' ),
		// 						'add_new'            => __( 'Add Coupon', 'timetrader' ),
		// 						'add_new_item'       => __( 'Add New Coupon', 'timetrader' ),
		// 						'edit'               => __( 'Edit', 'timetrader' ),
		// 						'edit_item'          => __( 'Edit Coupon', 'timetrader' ),
		// 						'new_item'           => __( 'New Coupon', 'timetrader' ),
		// 						'view'               => __( 'View Coupons', 'timetrader' ),
		// 						'view_item'          => __( 'View Coupon', 'timetrader' ),
		// 						'search_items'       => __( 'Search Coupons', 'timetrader' ),
		// 						'not_found'          => __( 'No Coupons found', 'timetrader' ),
		// 						'not_found_in_trash' => __( 'No Coupons found in trash', 'timetrader' ),
		// 						'parent'             => __( 'Parent Coupon', 'timetrader' )
		// 					),
		// 				'description'         => __( 'This is where you can add new coupons that customers can use in your store.', 'timetrader' ),
		// 				'public'              => false,
		// 				'show_ui'             => true,
		// 				'capability_type'     => 'shop_coupon',
		// 				'map_meta_cap'        => true,
		// 				'publicly_queryable'  => false,
		// 				'exclude_from_search' => true,
		// 				'show_in_menu'        => current_user_can( 'manage_timetrader' ) ? 'timetrader' : true,
		// 				'hierarchical'        => false,
		// 				'rewrite'             => false,
		// 				'query_var'           => false,
		// 				'supports'            => array( 'title' ),
		// 				'show_in_nav_menus'   => false,
		// 				'show_in_admin_bar'   => true
		// 			)
		// 		)
		// 	);
		// }

		// register_post_type( 'shop_webhook', apply_filters( 'timetrader_register_post_type_shop_webhook', array(
		// 			'labels'              => array(
		// 				'name'               => __( 'Webhooks', 'timetrader' ),
		// 				'singular_name'      => __( 'Webhook', 'timetrader' ),
		// 				'menu_name'          => _x( 'Webhooks', 'Admin menu name', 'timetrader' ),
		// 				'add_new'            => __( 'Add Webhook', 'timetrader' ),
		// 				'add_new_item'       => __( 'Add New Webhook', 'timetrader' ),
		// 				'edit'               => __( 'Edit', 'timetrader' ),
		// 				'edit_item'          => __( 'Edit Webhook', 'timetrader' ),
		// 				'new_item'           => __( 'New Webhook', 'timetrader' ),
		// 				'view'               => __( 'View Webhooks', 'timetrader' ),
		// 				'view_item'          => __( 'View Webhook', 'timetrader' ),
		// 				'search_items'       => __( 'Search Webhooks', 'timetrader' ),
		// 				'not_found'          => __( 'No Webhooks found', 'timetrader' ),
		// 				'not_found_in_trash' => __( 'No Webhooks found in trash', 'timetrader' ),
		// 				'parent'             => __( 'Parent Webhook', 'timetrader' )
		// 			),
		// 			'public'              => false,
		// 			'show_ui'             => false,
		// 			'capability_type'     => 'shop_webhook',
		// 			'map_meta_cap'        => true,
		// 			'publicly_queryable'  => false,
		// 			'exclude_from_search' => true,
		// 			'show_in_menu'        => false,
		// 			'hierarchical'        => false,
		// 			'rewrite'             => false,
		// 			'query_var'           => false,
		// 			'supports'            => false,
		// 			'show_in_nav_menus'   => false,
		// 			'show_in_admin_bar'   => false
		// 		)
		// 	)
		// );

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

	/**
	 * Add Product Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'product' );
		}
	}

}

TimeTrader_PostTypes::init();
