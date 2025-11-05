<?php
/**
 * SafeFonts Font Manager
 *
 * @package SafeFonts
 * @since 2.0.0
 */

namespace Chrmrtns\SafeFonts;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Font Manager Class
 *
 * Handles font validation and management for Gutenberg
 */
class FontManager {

    /**
     * Allowed font MIME types (multiple variations for compatibility)
     */
    private $allowed_mime_types = array(
        'woff2' => array('font/woff2', 'application/font-woff2', 'application/x-font-woff2', 'application/octet-stream'),
        'woff' => array('font/woff', 'application/font-woff', 'application/x-font-woff', 'font/x-woff', 'application/octet-stream'),
        'ttf' => array('font/ttf', 'application/x-font-ttf', 'font/sfnt', 'application/x-font-truetype', 'application/octet-stream'),
        'otf' => array('font/otf', 'application/x-font-otf', 'font/opentype', 'application/x-font-opentype', 'application/octet-stream')
    );

    /**
     * Magic bytes for font file validation
     */
    private $font_signatures = array(
        'woff2' => 'wOF2',
        'woff' => 'wOFF',
        'ttf' => array("\x00\x01\x00\x00", "true", "typ1"),
        'otf' => 'OTTO'
    );

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_safefonts_upload_font', array($this, 'handle_single_font_upload'));
        add_action('wp_ajax_safefonts_delete_font', array($this, 'handle_font_deletion'));
        add_action('wp_ajax_safefonts_bulk_delete_fonts', array($this, 'handle_bulk_font_deletion'));
    }

    /**
     * Get all fonts from database
     */
    public function get_fonts() {
        $cache_key = 'safefonts_fonts_list_v' . SAFEFONTS_VERSION;
        $fonts = get_transient($cache_key);

        if (false === $fonts) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'chrmrtns_safefonts';

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is safe (prefix + hardcoded), results are cached via transient
            $fonts = $wpdb->get_results(
                "SELECT * FROM {$table_name} ORDER BY font_family ASC, font_weight ASC" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            );

            // Cache for 12 hours
            set_transient($cache_key, $fonts, 12 * HOUR_IN_SECONDS);
        }

        return $fonts;
    }

    /**
     * Get fonts grouped by family
     */
    public function get_fonts_by_family() {
        $fonts = $this->get_fonts();
        $grouped = array();

        foreach ($fonts as $font) {
            $family = $font->font_family;
            if (!isset($grouped[$family])) {
                $grouped[$family] = array();
            }
            $grouped[$family][] = $font;
        }

        return $grouped;
    }

    /**
     * Generate CSS for all fonts
     */
    public function get_fonts_css() {
        $fonts = $this->get_fonts();
        if (empty($fonts)) {
            return "/* SafeFonts - No fonts installed */\n";
        }

        $css = "/* SafeFonts - Generated CSS */\n";
        $css .= "/* Generated: " . current_time('mysql') . " */\n\n";
        $css .= "/* ================================= */\n";
        $css .= "/*   FONT FACE DECLARATIONS         */\n";
        $css .= "/* ================================= */\n\n";

        foreach ($fonts as $font) {
            // Use relative path from database (includes family folder if present)
            $font_url = SAFEFONTS_ASSETS_URL . $font->file_path;
            $format = $this->get_font_format($font->file_path);

            $css .= "@font-face {\n";
            $css .= "  font-family: '" . esc_attr($font->font_family) . "';\n";
            $css .= "  font-style: " . esc_attr($font->font_style) . ";\n";
            $css .= "  font-weight: " . esc_attr($font->font_weight) . ";\n";
            $css .= "  font-display: swap;\n";
            $css .= "  src: url('" . esc_url($font_url) . "') format('" . $format . "');\n";
            $css .= "}\n\n";
        }

        // Generate Gutenberg CSS classes
        $css .= "/* ================================= */\n";
        $css .= "/*   GUTENBERG FONT FAMILY CLASSES  */\n";
        $css .= "/* ================================= */\n\n";

        $fonts_by_family = $this->get_fonts_by_family();
        foreach ($fonts_by_family as $family => $variants) {
            $slug = sanitize_title($family);
            $fallback = $this->get_font_fallback($family);

            $css .= ".has-" . $slug . "-font-family {\n";
            $css .= "  font-family: '" . esc_attr($family) . "'" . $fallback . ";\n";
            $css .= "}\n\n";
        }

        return $css;
    }

    /**
     * Import a single font file
     *
     * @param string $font_file_path Path to temporary uploaded file
     * @param array $font_info Font metadata (family, style, weight)
     * @param string $original_filename Original filename with extension
     */
    public function import_single_font($font_file_path, $font_info, $original_filename = '') {
        // Use original filename if provided, otherwise extract from path
        if (empty($original_filename)) {
            $original_filename = basename($font_file_path);
        }

        // Validate font file (pass original filename for extension detection)
        $validation_result = $this->validate_font_file($font_file_path, $original_filename);
        if (is_wp_error($validation_result)) {
            return $validation_result;
        }

        // Get family slug for folder name
        $family_slug = $this->get_family_slug($font_info['family']);

        // Create family folder if it doesn't exist
        $family_dir = SAFEFONTS_ASSETS_DIR . $family_slug . '/';
        if (!file_exists($family_dir)) {
            if (!wp_mkdir_p($family_dir)) {
                return new \WP_Error('mkdir_failed', __('Failed to create font family directory.', 'safefonts'));
            }
        }

        // Verify directory is writable
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_is_writable -- Simple permission check before file operations
        if (!is_writable($family_dir)) {
            return new \WP_Error('dir_not_writable', __('Font directory is not writable. Check file permissions.', 'safefonts'));
        }

        // Generate safe filename
        $safe_filename = $this->generate_safe_filename($original_filename);

        // Full path includes family folder
        $relative_path = $family_slug . '/' . $safe_filename;
        $destination = SAFEFONTS_ASSETS_DIR . $relative_path;

        // Copy file to assets directory
        // Use @ to suppress PHP warnings and check result properly
        $copy_result = @copy($font_file_path, $destination);

        if (!$copy_result) {
            $error_msg = error_get_last();
            return new \WP_Error('copy_failed', __('Failed to copy font file.', 'safefonts') . ' ' . ($error_msg ? $error_msg['message'] : ''));
        }

        // Verify file was actually copied
        if (!file_exists($destination)) {
            return new \WP_Error('file_not_copied', __('Font file was not copied to destination.', 'safefonts'));
        }

        // Set proper file permissions
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- Setting secure file permissions for uploaded font
        chmod($destination, 0644);

        // Save to database
        global $wpdb;
        $table_name = $wpdb->prefix . 'chrmrtns_safefonts';

        $font_data = array(
            'font_family' => sanitize_text_field($font_info['family']),
            'family_slug' => $family_slug,
            'font_style' => sanitize_text_field($font_info['style'] ?? 'normal'),
            'font_weight' => sanitize_text_field($font_info['weight'] ?? '400'),
            'file_path' => $relative_path,
            'file_hash' => hash_file('sha256', $destination),
            'file_size' => filesize($destination),
            'mime_type' => $validation_result['mime_type']
        );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table insert, cache cleared after
        $result = $wpdb->insert($table_name, $font_data);

        if ($result === false) {
            // Remove the file if database insert failed
            wp_delete_file($destination);
            return new \WP_Error('db_insert_failed', __('Failed to save font to database.', 'safefonts'));
        }

        // Clear cache
        $this->clear_fonts_cache();

        // Regenerate fonts.css
        $this->regenerate_fonts_css();

        return $wpdb->insert_id;
    }

    /**
     * Validate font file for security
     *
     * @param string $file_path Path to the file to validate
     * @param string $original_filename Original filename with extension
     */
    public function validate_font_file($file_path, $original_filename = '') {
        if (!file_exists($file_path)) {
            return new \WP_Error('file_not_found', __('Font file not found.', 'safefonts'));
        }

        // Check file size
        $max_size = get_option('safefonts_max_file_size', 2 * 1024 * 1024);
        if (filesize($file_path) > $max_size) {
            return new \WP_Error('file_too_large', __('Font file is too large.', 'safefonts'));
        }

        // Get file extension from original filename if provided, otherwise from file path
        $filename_to_check = !empty($original_filename) ? $original_filename : $file_path;
        $file_info = pathinfo($filename_to_check);
        $extension = strtolower($file_info['extension'] ?? '');

        // Check allowed extensions
        $allowed_types = get_option('safefonts_allowed_types', array('woff2', 'woff', 'ttf', 'otf'));

        // Ensure allowed_types is an array (could be empty or corrupted)
        if (!is_array($allowed_types) || empty($allowed_types)) {
            $allowed_types = array('woff2', 'woff', 'ttf', 'otf');
            update_option('safefonts_allowed_types', $allowed_types);
        }

        if (!in_array($extension, $allowed_types)) {
            /* translators: %1$s: file extension, %2$s: allowed file types */
            return new \WP_Error('invalid_extension', sprintf(__('Font file extension "%1$s" is not allowed. Allowed types: %2$s', 'safefonts'), $extension, implode(', ', $allowed_types)));
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);

        if (!isset($this->allowed_mime_types[$extension]) ||
            !in_array($mime_type, $this->allowed_mime_types[$extension])) {
            /* translators: %1$s: detected MIME type, %2$s: file extension */
            return new \WP_Error('invalid_mime_type', sprintf(__('Font file MIME type "%1$s" is not valid for .%2$s files.', 'safefonts'), $mime_type, $extension));
        }

        // Check file signature (magic bytes)
        if (!$this->verify_font_signature($file_path, $extension)) {
            return new \WP_Error('invalid_signature', __('Font file signature validation failed.', 'safefonts'));
        }

        return array(
            'extension' => $extension,
            'mime_type' => $mime_type,
            'size' => filesize($file_path)
        );
    }

    /**
     * Verify font file signature
     */
    private function verify_font_signature($file_path, $extension) {
        if (!isset($this->font_signatures[$extension])) {
            return false;
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Required for binary signature verification
        $handle = fopen($file_path, 'rb');
        if (!$handle) {
            return false;
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread -- Required for binary signature verification
        $signature = fread($handle, 4);
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Required for binary signature verification
        fclose($handle);

        $expected = $this->font_signatures[$extension];

        if (is_array($expected)) {
            return in_array($signature, $expected);
        }

        return strpos($signature, $expected) === 0;
    }

    /**
     * Generate safe filename
     */
    private function generate_safe_filename($filename) {
        $info = pathinfo($filename);
        $name = sanitize_file_name($info['filename']);
        $extension = strtolower($info['extension']);

        // Add timestamp to prevent conflicts
        $safe_name = $name . '-' . time() . '.' . $extension;

        return $safe_name;
    }

    /**
     * Get sanitized family slug for folder/database
     *
     * @param string $family_name Font family name
     * @return string Sanitized slug
     */
    private function get_family_slug($family_name) {
        return sanitize_title($family_name);
    }

    /**
     * Get font format for CSS
     */
    private function get_font_format($file_path) {
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        $formats = array(
            'woff2' => 'woff2',
            'woff' => 'woff',
            'ttf' => 'truetype',
            'otf' => 'opentype'
        );

        return $formats[$extension] ?? 'truetype';
    }

    /**
     * Get appropriate fallback fonts based on font family name
     *
     * @param string $family_name Font family name
     * @return string Fallback font stack
     */
    private function get_font_fallback($family_name) {
        $family_lower = strtolower($family_name);

        // Monospace fonts
        if (strpos($family_lower, 'mono') !== false ||
            strpos($family_lower, 'code') !== false ||
            strpos($family_lower, 'courier') !== false ||
            strpos($family_lower, 'console') !== false) {
            return ', monospace';
        }

        // Serif fonts
        if (strpos($family_lower, 'serif') !== false ||
            strpos($family_lower, 'times') !== false ||
            strpos($family_lower, 'garamond') !== false ||
            strpos($family_lower, 'baskerville') !== false ||
            strpos($family_lower, 'caslon') !== false) {
            return ', serif';
        }

        // Cursive/handwriting fonts
        if (strpos($family_lower, 'script') !== false ||
            strpos($family_lower, 'cursive') !== false ||
            strpos($family_lower, 'handwriting') !== false ||
            strpos($family_lower, 'brush') !== false) {
            return ', cursive, sans-serif';
        }

        // Default to sans-serif
        return ', sans-serif';
    }

    /**
     * Clear fonts cache
     */
    public function clear_fonts_cache() {
        delete_transient('safefonts_fonts_list_v' . SAFEFONTS_VERSION);
    }

    /**
     * Regenerate fonts.css file
     */
    private function regenerate_fonts_css() {
        $core = Core::get_instance();
        $core->generate_fonts_css();
    }

    /**
     * Handle AJAX single font upload
     */
    public function handle_single_font_upload() {
        // Verify nonce and capabilities
        if (!current_user_can('manage_options') ||
            !isset($_POST['nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'safefonts_upload')) {
            wp_die(esc_html__('Security check failed.', 'safefonts'));
        }

        if (!isset($_FILES['font_file'])) {
            wp_send_json_error(__('No file uploaded.', 'safefonts'));
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File upload validation done later
        $file = isset($_FILES['font_file']) ? $_FILES['font_file'] : array();

        // Basic file validation
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(__('File upload error.', 'safefonts'));
        }

        // Get font info from form
        $font_info = array(
            'family' => isset($_POST['font_family']) ? sanitize_text_field(wp_unslash($_POST['font_family'])) : '',
            'style' => isset($_POST['font_style']) ? sanitize_text_field(wp_unslash($_POST['font_style'])) : 'normal',
            'weight' => isset($_POST['font_weight']) ? sanitize_text_field(wp_unslash($_POST['font_weight'])) : '400'
        );

        if (empty($font_info['family'])) {
            wp_send_json_error(__('Font family name is required.', 'safefonts'));
        }

        // Import the font (pass both tmp_name and original name)
        $result = $this->import_single_font($file['tmp_name'], $font_info, $file['name']);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(array(
            'font_id' => $result,
            'message' => __('Font uploaded successfully!', 'safefonts')
        ));
    }

    /**
     * Handle font deletion
     */
    public function handle_font_deletion() {
        // Verify nonce and capabilities
        if (!current_user_can('manage_options') ||
            !isset($_POST['nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'safefonts_delete')) {
            wp_die(esc_html__('Security check failed.', 'safefonts'));
        }

        if (!isset($_POST['font_id'])) {
            wp_send_json_error(__('Font ID is required.', 'safefonts'));
        }

        $font_id = intval($_POST['font_id']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'chrmrtns_safefonts';

        // Get font info
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is safe (prefix + hardcoded), single row query
        $font = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $font_id
            )
        );

        if (!$font) {
            wp_send_json_error(__('Font not found.', 'safefonts'));
        }

        // Delete file
        $file_path = SAFEFONTS_ASSETS_DIR . $font->file_path;
        if (file_exists($file_path)) {
            wp_delete_file($file_path);
        }

        // Check if family folder is empty and remove it
        $family_dir = dirname($file_path);
        if ($family_dir !== SAFEFONTS_ASSETS_DIR && is_dir($family_dir)) {
            // Check if directory is empty (only . and .. remain)
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readdir -- Required for directory cleanup check
            $files = scandir($family_dir);
            $files = array_diff($files, array('.', '..', 'index.php', '.htaccess'));

            if (empty($files)) {
                // Remove index.php if exists
                $index_file = $family_dir . '/index.php';
                if (file_exists($index_file)) {
                    wp_delete_file($index_file);
                }

                // Remove directory
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- Required for cleanup
                @rmdir($family_dir);
            }
        }

        // Delete from database
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table delete, cache cleared after
        $wpdb->delete($table_name, array('id' => $font_id));

        // Clear cache
        $this->clear_fonts_cache();

        // Regenerate fonts.css
        $this->regenerate_fonts_css();

        wp_send_json_success(__('Font deleted successfully.', 'safefonts'));
    }

    /**
     * Handle bulk font deletion
     */
    public function handle_bulk_font_deletion() {
        // Verify nonce and capabilities
        if (!current_user_can('manage_options') ||
            !isset($_POST['nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'safefonts_bulk_delete')) {
            wp_die(esc_html__('Security check failed.', 'safefonts'));
        }

        if (!isset($_POST['font_ids']) || !is_array($_POST['font_ids'])) {
            wp_send_json_error(__('No fonts selected.', 'safefonts'));
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Array of integers sanitized below
        $font_ids = array_map('intval', wp_unslash($_POST['font_ids']));

        if (empty($font_ids)) {
            wp_send_json_error(__('No valid font IDs provided.', 'safefonts'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'chrmrtns_safefonts';

        $deleted_count = 0;
        $empty_folders = array();

        foreach ($font_ids as $font_id) {
            // Get font info
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is safe (prefix + hardcoded), single row query
            $font = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$table_name} WHERE id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $font_id
                )
            );

            if (!$font) {
                continue;
            }

            // Delete file
            $file_path = SAFEFONTS_ASSETS_DIR . $font->file_path;
            if (file_exists($file_path)) {
                wp_delete_file($file_path);
            }

            // Track family folder for cleanup
            $family_dir = dirname($file_path);
            if ($family_dir !== SAFEFONTS_ASSETS_DIR && !in_array($family_dir, $empty_folders, true)) {
                $empty_folders[] = $family_dir;
            }

            // Delete from database
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table delete, cache cleared after
            $result = $wpdb->delete($table_name, array('id' => $font_id));

            if ($result !== false) {
                $deleted_count++;
            }
        }

        // Cleanup empty family folders
        foreach ($empty_folders as $family_dir) {
            if (is_dir($family_dir)) {
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readdir -- Required for directory cleanup check
                $files = scandir($family_dir);
                $files = array_diff($files, array('.', '..', 'index.php', '.htaccess'));

                if (empty($files)) {
                    // Remove index.php if exists
                    $index_file = $family_dir . '/index.php';
                    if (file_exists($index_file)) {
                        wp_delete_file($index_file);
                    }

                    // Remove directory
                    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- Required for cleanup
                    @rmdir($family_dir);
                }
            }
        }

        // Clear cache
        $this->clear_fonts_cache();

        // Regenerate fonts.css
        $this->regenerate_fonts_css();

        /* translators: %d: number of fonts deleted */
        $message = sprintf(_n('%d font deleted successfully.', '%d fonts deleted successfully.', $deleted_count, 'safefonts'), $deleted_count);

        wp_send_json_success(array(
            'message' => $message,
            'deleted_count' => $deleted_count
        ));
    }
}
