<?php
/**
 * SafeFonts Uninstall
 *
 * Handles plugin uninstallation and data cleanup
 *
 * @package SafeFonts
 * @since 1.0.0
 */

// Exit if not called by WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user wants to delete data
$delete_data = get_option('safefonts_delete_data_on_uninstall', false);

if (!$delete_data) {
    // User wants to keep data - exit without deleting anything
    return;
}

// Check if SafeFonts Pro is still installed
$pro_active = is_plugin_active('safefonts-pro/safefonts-pro.php') ||
              file_exists(WP_PLUGIN_DIR . '/safefonts-pro/safefonts-pro.php');

global $wpdb;

// Delete plugin-specific options
delete_option('safefonts_max_file_size');
delete_option('safefonts_allowed_types');
delete_option('safefonts_preload_fonts');
delete_option('safefonts_version');
delete_option('safefonts_delete_data_on_uninstall');

// Only delete shared resources if Pro is NOT installed
if (!$pro_active) {
    // Drop the fonts database table (shared with Pro)
    $table_name = $wpdb->prefix . 'chrmrtns_safefonts';
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Safe table drop during uninstall
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

    // Delete the uploads directory (shared with Pro)
    $uploads_dir = WP_CONTENT_DIR . '/uploads/safefonts/';
    if (is_dir($uploads_dir)) {
        safefonts_uninstall_delete_directory($uploads_dir);
    }
}

/**
 * Recursively delete directory and its contents
 *
 * @param string $dir Directory path
 * @return bool Success status
 */
function safefonts_uninstall_delete_directory($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $items = array_diff(scandir($dir), array('.', '..'));

    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            safefonts_uninstall_delete_directory($path);
        } else {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Necessary for uninstall
            unlink($path);
        }
    }

    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- Necessary for uninstall
    return rmdir($dir);
}
