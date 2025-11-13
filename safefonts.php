<?php
/**
 * Plugin Name: SafeFonts
 * Plugin URI: https://safefonts.com
 * Description: Secure font management for WordPress with Gutenberg integration and local hosting for GDPR compliance.
 * Version: 1.1.7
 * Requires at least: 6.2
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
define('CHRMRTNS_SAFEFONTS_VERSION', '1.1.7');
define('CHRMRTNS_SAFEFONTS_PLUGIN_FILE', __FILE__);
define('CHRMRTNS_SAFEFONTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CHRMRTNS_SAFEFONTS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Use WordPress upload directory functions for proper compatibility
$chrmrtns_safefonts_upload_dir = wp_upload_dir();
define('CHRMRTNS_SAFEFONTS_ASSETS_DIR', $chrmrtns_safefonts_upload_dir['basedir'] . '/safefonts/');
define('CHRMRTNS_SAFEFONTS_ASSETS_URL', $chrmrtns_safefonts_upload_dir['baseurl'] . '/safefonts/');

// Load PSR-4 Autoloader
require_once CHRMRTNS_SAFEFONTS_PLUGIN_DIR . 'includes/Autoloader.php';
$chrmrtns_safefonts_autoloader = new \Chrmrtns\SafeFonts\Autoloader();
$chrmrtns_safefonts_autoloader->register();

/**
 * Initialize the plugin
 *
 * @return \Chrmrtns\SafeFonts\Core
 */
function chrmrtns_safefonts() {
    return \Chrmrtns\SafeFonts\Core::get_instance();
}

// Start the plugin
chrmrtns_safefonts();
