<?php
/**
 * SafeFonts Core Class
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
 * Main SafeFonts Core Class
 */
class Core {

    /**
     * Single instance
     *
     * @var Core
     */
    private static $instance = null;

    /**
     * Font Manager instance
     *
     * @var FontManager
     */
    public $font_manager;

    /**
     * Admin Interface instance
     *
     * @var Admin\AdminInterface
     */
    public $admin;

    /**
     * Get singleton instance
     *
     * @return Core
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize plugin
     *
     * @return void
     */
    private function init() {
        // Ensure uploads directory exists
        add_action('admin_init', array($this, 'ensure_uploads_directory'));
        add_action('admin_notices', array($this, 'show_directory_notices'));

        // Initialize components
        $this->font_manager = new FontManager();

        if (is_admin()) {
            $this->admin = new Admin\AdminInterface();
        }

        // Register hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_fonts'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_fonts'));
        add_action('wp_head', array($this, 'add_font_preload_tags'), 1);

        // Allow font file uploads
        add_filter('upload_mimes', array($this, 'allow_font_uploads'));

        // Register fonts with Gutenberg editor
        add_filter('block_editor_settings_all', array($this, 'register_fonts_with_editor'), 10, 2);

        // Font Library integration (WordPress 6.5+)
        if (function_exists('wp_register_font_collection')) {
            add_action('init', array($this, 'register_font_collection'));
        }

        // Register activation/deactivation hooks
        register_activation_hook(SAFEFONTS_PLUGIN_DIR . 'safefonts.php', array($this, 'activate'));
        register_deactivation_hook(SAFEFONTS_PLUGIN_DIR . 'safefonts.php', array($this, 'deactivate'));
    }

    /**
     * Enqueue fonts CSS
     *
     * @return void
     */
    public function enqueue_fonts() {
        $fonts_css_file = SAFEFONTS_PLUGIN_DIR . 'assets/css/fonts.css';
        $fonts_css_url = SAFEFONTS_PLUGIN_URL . 'assets/css/fonts.css';

        // Generate fonts.css if it doesn't exist
        if (!file_exists($fonts_css_file)) {
            $this->generate_fonts_css();
        }

        // Enqueue the fonts CSS file
        if (file_exists($fonts_css_file)) {
            wp_enqueue_style(
                'safefonts-fonts',
                $fonts_css_url,
                array(),
                filemtime($fonts_css_file)
            );
        }
    }

    /**
     * Add font preload tags to head
     *
     * @return void
     */
    public function add_font_preload_tags() {
        $preload_fonts = get_option('safefonts_preload_fonts', array());

        if (empty($preload_fonts)) {
            return;
        }

        $fonts = $this->font_manager->get_fonts();

        foreach ($fonts as $font) {
            // Check if this font family is marked for preloading
            if (!in_array($font->font_family, $preload_fonts, true)) {
                continue;
            }

            // Use relative path from database (includes family folder if present)
            $font_url = SAFEFONTS_ASSETS_URL . $font->file_path;
            $extension = strtolower(pathinfo($font->file_path, PATHINFO_EXTENSION));

            // Map file extension to MIME type
            $mime_types = array(
                'woff2' => 'font/woff2',
                'woff' => 'font/woff',
                'ttf' => 'font/ttf',
                'otf' => 'font/otf'
            );

            $mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'font/woff2';

            printf(
                '<link rel="preload" href="%s" as="font" type="%s" crossorigin>%s',
                esc_url($font_url),
                esc_attr($mime_type),
                "\n"
            );
        }
    }

    /**
     * Generate fonts.css file
     *
     * @return bool
     */
    public function generate_fonts_css() {
        $fonts_css = $this->font_manager->get_fonts_css();
        $fonts_css_file = SAFEFONTS_PLUGIN_DIR . 'assets/css/fonts.css';

        // Ensure directory exists
        $css_dir = dirname($fonts_css_file);
        if (!file_exists($css_dir)) {
            wp_mkdir_p($css_dir);
        }

        // Write CSS to file
        $result = file_put_contents($fonts_css_file, $fonts_css);

        return $result !== false;
    }

    /**
     * Allow font file uploads
     *
     * @param array $mimes Existing MIME types
     * @return array Modified MIME types
     */
    public function allow_font_uploads($mimes) {
        // Add font MIME types
        $mimes['woff2'] = 'font/woff2';
        $mimes['woff'] = 'font/woff';
        $mimes['ttf'] = 'font/ttf';
        $mimes['otf'] = 'font/otf';

        return $mimes;
    }

    /**
     * Register fonts with Gutenberg editor
     *
     * @param array $settings Editor settings
     * @param WP_Block_Editor_Context $context Editor context
     * @return array Modified settings
     */
    public function register_fonts_with_editor($settings, $context) {
        $fonts = $this->font_manager->get_fonts_by_family();

        if (empty($fonts)) {
            return $settings;
        }

        // Build font families array for Gutenberg
        $font_families = array();

        foreach ($fonts as $family => $variants) {
            $font_faces = array();

            foreach ($variants as $variant) {
                // Use relative path from database (includes family folder if present)
                $font_url = SAFEFONTS_ASSETS_URL . $variant->file_path;

                $font_faces[] = array(
                    'fontFamily' => $family,
                    'fontStyle' => $variant->font_style,
                    'fontWeight' => $variant->font_weight,
                    'src' => array($font_url)
                );
            }

            $font_families[] = array(
                'fontFamily' => $family,
                'name' => $family,
                'slug' => sanitize_title($family),
                'fontFace' => $font_faces
            );
        }

        // Initialize typography settings if not present
        if (!isset($settings['__experimentalFeatures'])) {
            $settings['__experimentalFeatures'] = array();
        }

        if (!isset($settings['__experimentalFeatures']['typography'])) {
            $settings['__experimentalFeatures']['typography'] = array();
        }

        if (!isset($settings['__experimentalFeatures']['typography']['fontFamilies'])) {
            $settings['__experimentalFeatures']['typography']['fontFamilies'] = array();
        }

        if (!isset($settings['__experimentalFeatures']['typography']['fontFamilies']['custom'])) {
            $settings['__experimentalFeatures']['typography']['fontFamilies']['custom'] = array();
        }

        // Add SafeFonts to the custom font families
        $settings['__experimentalFeatures']['typography']['fontFamilies']['custom'] = array_merge(
            $settings['__experimentalFeatures']['typography']['fontFamilies']['custom'],
            $font_families
        );

        return $settings;
    }

    /**
     * Register font collection for WordPress 6.5+ Font Library
     *
     * @return void
     */
    public function register_font_collection() {
        $fonts = $this->font_manager->get_fonts_by_family();

        if (empty($fonts)) {
            return;
        }

        $font_families = array();

        foreach ($fonts as $family => $variants) {
            $font_face = array();

            foreach ($variants as $variant) {
                // Use relative path from database (includes family folder if present)
                $font_url = SAFEFONTS_ASSETS_URL . $variant->file_path;

                $font_face[] = array(
                    'fontFamily' => $family,
                    'fontStyle' => $variant->font_style,
                    'fontWeight' => $variant->font_weight,
                    'src' => array($font_url)
                );
            }

            $font_families[] = array(
                'fontFamily' => $family,
                'name' => $family,
                'slug' => sanitize_title($family),
                'fontFace' => $font_face
            );
        }

        // Final safety check - don't register if no font families
        if (empty($font_families)) {
            return;
        }

        $config = array(
            'name' => __('SafeFonts', 'safefonts'),
            'description' => __('Locally hosted fonts managed by SafeFonts', 'safefonts'),
            'font_families' => $font_families
        );

        wp_register_font_collection('safefonts', $config);
    }

    /**
     * Plugin activation
     *
     * @return void
     */
    public function activate() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'chrmrtns_safefonts';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            font_family varchar(255) NOT NULL,
            family_slug varchar(255) NOT NULL DEFAULT '',
            font_style varchar(50) NOT NULL DEFAULT 'normal',
            font_weight varchar(50) NOT NULL DEFAULT '400',
            file_path varchar(500) NOT NULL,
            file_hash varchar(64) NOT NULL,
            file_size bigint(20) NOT NULL,
            mime_type varchar(100) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY font_family (font_family),
            KEY family_slug (family_slug),
            KEY file_hash (file_hash)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Check if migration is needed (upgrading from older version)
        $installed_version = get_option('safefonts_version', '0.0.0');
        $needs_migration = version_compare($installed_version, '1.1.0', '<');

        if ($needs_migration && $installed_version !== '0.0.0') {
            // Migrate existing fonts to family folders
            $this->migrate_to_family_folders();
        }

        // Set default options
        add_option('safefonts_max_file_size', 2 * 1024 * 1024);
        add_option('safefonts_allowed_types', array('woff2', 'woff', 'ttf', 'otf'));

        // Create fonts directory in uploads folder
        $fonts_dir = SAFEFONTS_ASSETS_DIR;
        if (!file_exists($fonts_dir)) {
            wp_mkdir_p($fonts_dir);

            // Add index.php for directory protection
            file_put_contents($fonts_dir . 'index.php', "<?php\n// Silence is golden.\n");
        }

        // Migrate fonts from old plugin folder location to uploads folder (v1.0.9+)
        $old_fonts_dir = SAFEFONTS_PLUGIN_DIR . 'assets/fonts/';
        if (file_exists($old_fonts_dir) && is_dir($old_fonts_dir)) {
            $this->migrate_fonts_to_uploads($old_fonts_dir, $fonts_dir);
        }

        // Create CSS directory (in assets/css/ not assets/fonts/css/)
        $css_dir = SAFEFONTS_PLUGIN_DIR . 'assets/css/';
        if (!file_exists($css_dir)) {
            wp_mkdir_p($css_dir);
        }

        // Generate initial fonts.css
        $this->generate_fonts_css();

        // Set version
        update_option('safefonts_version', SAFEFONTS_VERSION);
    }

    /**
     * Migrate fonts from old plugin folder location to uploads folder
     *
     * @param string $old_dir Old fonts directory
     * @param string $new_dir New fonts directory
     * @return void
     */
    private function migrate_fonts_to_uploads($old_dir, $new_dir) {
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readdir -- Required for directory migration
        $files = scandir($old_dir);

        if (!$files) {
            return;
        }

        $migrated_count = 0;

        foreach ($files as $file) {
            // Skip . and .. and index.php
            if ($file === '.' || $file === '..' || $file === 'index.php') {
                continue;
            }

            $old_file = $old_dir . $file;
            $new_file = $new_dir . $file;

            // Only migrate font files (not subdirectories)
            if (is_file($old_file) && preg_match('/\.(woff2|woff|ttf|otf)$/i', $file)) {
                // Copy file to new location
                if (copy($old_file, $new_file)) {
                    $migrated_count++;
                }
            }
        }

        // Add admin notice about migration
        if ($migrated_count > 0) {
            add_option('safefonts_migration_notice', $migrated_count);
        }
    }

    /**
     * Migrate fonts from flat structure to family folders (v1.1.0+)
     *
     * @return void
     */
    private function migrate_to_family_folders() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'chrmrtns_safefonts';

        // Get all fonts that need migration (file_path doesn't contain '/')
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query, one-time operation
        $fonts = $wpdb->get_results(
            "SELECT * FROM {$table_name} WHERE file_path NOT LIKE '%/%'" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        );

        if (empty($fonts)) {
            return;
        }

        $migrated_count = 0;

        foreach ($fonts as $font) {
            // Generate family slug
            $family_slug = sanitize_title($font->font_family);

            // Skip if already has family_slug set and file already in folder
            if (!empty($font->family_slug) && strpos($font->file_path, '/') !== false) {
                continue;
            }

            // Create family folder
            $family_dir = SAFEFONTS_ASSETS_DIR . $family_slug . '/';
            if (!file_exists($family_dir)) {
                wp_mkdir_p($family_dir);
            }

            // Old file path (flat structure)
            $old_file = SAFEFONTS_ASSETS_DIR . $font->file_path;

            // New file path (family folder structure)
            $new_relative_path = $family_slug . '/' . $font->file_path;
            $new_file = SAFEFONTS_ASSETS_DIR . $new_relative_path;

            // Move file if it exists
            if (file_exists($old_file) && !file_exists($new_file)) {
                if (rename($old_file, $new_file)) {
                    // Update database with new path and family_slug
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration update, one-time operation
                    $wpdb->update(
                        $table_name,
                        array(
                            'file_path' => $new_relative_path,
                            'family_slug' => $family_slug
                        ),
                        array('id' => $font->id),
                        array('%s', '%s'),
                        array('%d')
                    );

                    $migrated_count++;
                }
            }
        }

        if ($migrated_count > 0) {
            // Clear fonts cache
            delete_transient('safefonts_fonts_list_v' . SAFEFONTS_VERSION);

            // Regenerate fonts.css
            $this->generate_fonts_css();

            // Add admin notice
            add_option('safefonts_family_folders_migrated_count', $migrated_count);
        }
    }

    /**
     * Ensure uploads directory exists and is writable
     *
     * @return bool True if directory exists and is writable, false otherwise
     */
    public function ensure_uploads_directory() {
        $fonts_dir = SAFEFONTS_ASSETS_DIR;

        // Check if directory exists
        if (!file_exists($fonts_dir)) {
            // Try to create directory
            if (!wp_mkdir_p($fonts_dir)) {
                // Could not create directory
                update_option('safefonts_directory_error', 'create_failed');
                return false;
            }

            // Add index.php for directory protection
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Required for security file
            file_put_contents($fonts_dir . 'index.php', "<?php\n// Silence is golden.\n");

            // Add .htaccess to allow font files
            $htaccess_content = "<IfModule mod_mime.c>\n";
            $htaccess_content .= "AddType font/woff2 .woff2\n";
            $htaccess_content .= "AddType font/woff .woff\n";
            $htaccess_content .= "AddType font/ttf .ttf\n";
            $htaccess_content .= "AddType font/otf .otf\n";
            $htaccess_content .= "</IfModule>\n\n";
            $htaccess_content .= "<IfModule mod_headers.c>\n";
            $htaccess_content .= "# Allow CORS for fonts\n";
            $htaccess_content .= "<FilesMatch \"\\.(woff2?|ttf|otf)$\">\n";
            $htaccess_content .= "Header set Access-Control-Allow-Origin \"*\"\n";
            $htaccess_content .= "</FilesMatch>\n";
            $htaccess_content .= "</IfModule>\n";

            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Required for .htaccess
            file_put_contents($fonts_dir . '.htaccess', $htaccess_content);
        }

        // Check if directory is writable
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_is_writable -- Read-only check for validation
        if (!is_writable($fonts_dir)) {
            update_option('safefonts_directory_error', 'not_writable');
            return false;
        }

        // Directory exists and is writable - clear any errors
        delete_option('safefonts_directory_error');
        return true;
    }

    /**
     * Show admin notices for directory issues
     *
     * @return void
     */
    public function show_directory_notices() {
        $error = get_option('safefonts_directory_error');

        if (!$error) {
            return;
        }

        $fonts_dir = SAFEFONTS_ASSETS_DIR;

        if ('create_failed' === $error) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php esc_html_e('SafeFonts Error:', 'safefonts'); ?></strong>
                    <?php
                    /* translators: %s: directory path */
                    echo wp_kses_post(sprintf(__('Could not create the fonts directory at <code>%s</code>. Please create this directory manually and ensure it is writable by the web server.', 'safefonts'), esc_html($fonts_dir)));
                    ?>
                </p>
            </div>
            <?php
        } elseif ('not_writable' === $error) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php esc_html_e('SafeFonts Error:', 'safefonts'); ?></strong>
                    <?php
                    /* translators: %s: directory path */
                    echo wp_kses_post(sprintf(__('The fonts directory at <code>%s</code> is not writable. Please set the correct permissions (755 or 775) for this directory.', 'safefonts'), esc_html($fonts_dir)));
                    ?>
                </p>
                <p>
                    <?php esc_html_e('You can fix this by running:', 'safefonts'); ?>
                    <code>chmod 755 <?php echo esc_html($fonts_dir); ?></code>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Plugin deactivation
     *
     * @return void
     */
    public function deactivate() {
        // Clear any transients
        delete_transient('safefonts_fonts_list_v' . SAFEFONTS_VERSION);
    }
}
