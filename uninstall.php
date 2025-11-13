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
$chrmrtns_safefonts_delete_data = get_option('chrmrtns_safefonts_delete_data_on_uninstall', false);

if (!$chrmrtns_safefonts_delete_data) {
    // User wants to keep data - exit without deleting anything
    return;
}

// Check if SafeFonts Pro is still installed
$chrmrtns_safefonts_pro_active = is_plugin_active('safefonts-pro/safefonts-pro.php') ||
                                  file_exists(WP_PLUGIN_DIR . '/safefonts-pro/safefonts-pro.php');

global $wpdb;

// Delete plugin-specific options
delete_option('chrmrtns_safefonts_max_file_size');
delete_option('chrmrtns_safefonts_allowed_types');
delete_option('chrmrtns_safefonts_preload_fonts');
delete_option('chrmrtns_safefonts_version');
delete_option('chrmrtns_safefonts_delete_data_on_uninstall');

// Only delete shared resources if Pro is NOT installed
if (!$chrmrtns_safefonts_pro_active) {
    // Drop the fonts database table (shared with Pro)
    $chrmrtns_safefonts_table_name = $wpdb->prefix . 'chrmrtns_safefonts';
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Safe table drop during uninstall
    $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %i", $chrmrtns_safefonts_table_name));

    // Delete the uploads directory (shared with Pro)
    $chrmrtns_safefonts_upload_dir = wp_upload_dir();
    $chrmrtns_safefonts_uploads_dir = $chrmrtns_safefonts_upload_dir['basedir'] . '/safefonts/';
    if (is_dir($chrmrtns_safefonts_uploads_dir)) {
        chrmrtns_safefonts_uninstall_delete_directory($chrmrtns_safefonts_uploads_dir);
    }
}

/**
 * Recursively delete directory and its contents
 *
 * @param string $dir Directory path
 * @return bool Success status
 */
function chrmrtns_safefonts_uninstall_delete_directory($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $items = array_diff(scandir($dir), array('.', '..'));

    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            chrmrtns_safefonts_uninstall_delete_directory($path);
        } else {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Necessary for uninstall
            unlink($path);
        }
    }

    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- Necessary for uninstall
    return rmdir($dir);
}
