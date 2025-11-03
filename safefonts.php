<?php
/**
 * Plugin Name: SafeFonts
 * Plugin URI: https://safefonts.com
 * Description: Secure font management for WordPress with Gutenberg integration and local hosting for GDPR compliance.
 * Version: 1.1.3
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: Chris Martens
 * Author URI: https://chris-martens.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: safefonts
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SAFEFONTS_VERSION', '1.1.3');
define('SAFEFONTS_PLUGIN_FILE', __FILE__);
define('SAFEFONTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SAFEFONTS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Use WordPress upload directory functions for proper compatibility
$upload_dir = wp_upload_dir();
define('SAFEFONTS_ASSETS_DIR', $upload_dir['basedir'] . '/safefonts/');
define('SAFEFONTS_ASSETS_URL', $upload_dir['baseurl'] . '/safefonts/');

// Load PSR-4 Autoloader
require_once SAFEFONTS_PLUGIN_DIR . 'includes/Autoloader.php';
$autoloader = new \Chrmrtns\SafeFonts\Autoloader();
$autoloader->register();

/**
 * Initialize the plugin
 *
 * @return \Chrmrtns\SafeFonts\Core
 */
function safefonts() {
    return \Chrmrtns\SafeFonts\Core::get_instance();
}

// Start the plugin
safefonts();
