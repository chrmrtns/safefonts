<?php
/**
 * PSR-4 Autoloader for SafeFonts
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
 * PSR-4 Autoloader Class
 */
class Autoloader {

    /**
     * Namespace prefix
     *
     * @var string
     */
    private $namespace_prefix = 'Chrmrtns\\SafeFonts\\';

    /**
     * Base directory for the namespace
     *
     * @var string
     */
    private $base_directory;

    /**
     * Constructor
     */
    public function __construct() {
        $this->base_directory = CHRMRTNS_SAFEFONTS_PLUGIN_DIR . 'includes/';
    }

    /**
     * Register autoloader
     *
     * @return void
     */
    public function register() {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Autoload class files
     *
     * @param string $class The fully-qualified class name
     * @return void
     */
    public function autoload($class) {
        // Check if the class uses the namespace prefix
        $len = strlen($this->namespace_prefix);
        if (strncmp($this->namespace_prefix, $class, $len) !== 0) {
            return;
        }

        // Get the relative class name
        $relative_class = substr($class, $len);

        // Replace namespace separators with directory separators
        // and append with .php
        $file = $this->base_directory . str_replace('\\', '/', $relative_class) . '.php';

        // If the file exists, require it
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
