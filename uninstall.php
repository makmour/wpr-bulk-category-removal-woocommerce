<?php
/**
 * Uninstall routine for WPRepublic Bulk Category Removal for WooCommerce.
 *
 * @package WPR_Bulk_Category_Removal_WooCommerce
 * @version 1.1.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// 1. Delete the transient that stores the last log content.
delete_transient( 'wpr_bulk_category_removal_last_log' );

// 2. Delete the user meta for screen options from all users.
delete_metadata( 'user', 0, 'wpr_bulk_category_removal_columns', '', true );
delete_metadata( 'user', 0, 'wpr_bulk_category_removal_per_page', '', true );

// 3. Recursively remove the log directory.
$wpr_bulk_category_removal_upload_dir = wp_upload_dir();
$wpr_bulk_category_removal_log_dir    = trailingslashit( $wpr_bulk_category_removal_upload_dir['basedir'] ) . 'wpr-bulk-category-removal-woocommerce-logs';

global $wp_filesystem;
if ( empty( $wp_filesystem ) ) {
	require_once ABSPATH . '/wp-admin/includes/file.php';
	WP_Filesystem();
}

if ( $wp_filesystem->is_dir( $wpr_bulk_category_removal_log_dir ) ) {
	$wp_filesystem->delete( $wpr_bulk_category_removal_log_dir, true );
}