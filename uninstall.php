<?php
/**
* timetrader Uninstall
*
* Uninstalling timetrader deletes user roles, pages, tables, and options.
*
* @author      Indiorlei de Oliveira (Agência Cion)
* @version     0.1
*/

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}

$status_options = get_option( 'timetrader_status_options', array() );

if ( ! empty( $status_options['uninstall_data'] ) ) {

	global $wpdb;

	// Roles + caps
	include_once( 'class/install.php' );
	TimetraderInstall::remove_roles();

	// Pages
	wp_trash_post( get_option( 'timetrader_shop_page_id' ) );

	// Tables
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "timetrader_attribute_taxonomies" );

	// Delete options
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'timetrader_%';");

	// // Delete posts + data
	// $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'product', 'product_variation', 'shop_coupon', 'shop_order', 'shop_order_refund' );" );
	// $wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
	// $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "timetrader_order_items" );
	// $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "timetrader_order_itemmeta" );
}