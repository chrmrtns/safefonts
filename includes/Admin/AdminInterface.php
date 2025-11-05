<?php
/**
 * SafeFonts Admin Interface
 *
 * @package SafeFonts
 * @since 2.0.0
 */

namespace Chrmrtns\SafeFonts\Admin;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Interface Class
 *
 * Handles WordPress admin interface for basic font management
 */
class AdminInterface {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'handle_form_submissions'));
    }

    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main menu page (Dashboard)
        add_menu_page(
            __('SafeFonts', 'safefonts'),              // Page title
            __('SafeFonts', 'safefonts'),              // Menu title
            'manage_options',                          // Capability
            'safefonts',                               // Menu slug
            array($this, 'dashboard_page'),            // Callback function
            'dashicons-editor-textcolor',              // Icon (font icon)
            30                                         // Position (after Comments)
        );

        // Dashboard submenu (same as main page)
        add_submenu_page(
            'safefonts',                               // Parent slug
            __('Dashboard', 'safefonts'),              // Page title
            __('Dashboard', 'safefonts'),              // Menu title
            'manage_options',                          // Capability
            'safefonts',                               // Menu slug (same as parent)
            array($this, 'dashboard_page')             // Callback function
        );

        // Fonts submenu
        add_submenu_page(
            'safefonts',
            __('Fonts', 'safefonts'),
            __('Fonts', 'safefonts'),
            'manage_options',
            'safefonts-fonts',
            array($this, 'fonts_page')
        );

        // Upload submenu
        add_submenu_page(
            'safefonts',
            __('Upload', 'safefonts'),
            __('Upload', 'safefonts'),
            'manage_options',
            'safefonts-upload',
            array($this, 'upload_page')
        );

        // Settings submenu
        add_submenu_page(
            'safefonts',
            __('Settings', 'safefonts'),
            __('Settings', 'safefonts'),
            'manage_options',
            'safefonts-settings',
            array($this, 'settings_page')
        );

        // System Info submenu
        add_submenu_page(
            'safefonts',
            __('System Info', 'safefonts'),
            __('System Info', 'safefonts'),
            'manage_options',
            'safefonts-system',
            array($this, 'system_page')
        );

        // Help & Documentation submenu
        add_submenu_page(
            'safefonts',
            __('Help & Documentation', 'safefonts'),
            __('Help & Documentation', 'safefonts'),
            'manage_options',
            'safefonts-help',
            array($this, 'help_page')
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on SafeFonts admin pages
        if (strpos($hook, 'safefonts') === false) {
            return;
        }

        // Enqueue fonts.css for font previews in admin
        $fonts_css_file = SAFEFONTS_PLUGIN_DIR . 'assets/css/fonts.css';
        $fonts_css_url = SAFEFONTS_PLUGIN_URL . 'assets/css/fonts.css';

        if (file_exists($fonts_css_file)) {
            wp_enqueue_style(
                'safefonts-fonts',
                $fonts_css_url,
                array(),
                filemtime($fonts_css_file)
            );
        }

        wp_enqueue_style(
            'safefonts-admin',
            SAFEFONTS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SAFEFONTS_VERSION . '-' . filemtime(SAFEFONTS_PLUGIN_DIR . 'assets/css/admin.css')
        );

        wp_enqueue_script(
            'safefonts-admin',
            SAFEFONTS_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            SAFEFONTS_VERSION . '-' . filemtime(SAFEFONTS_PLUGIN_DIR . 'assets/js/admin.js'),
            true
        );

        wp_localize_script('safefonts-admin', 'safefontsAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('safefonts_admin'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this font?', 'safefonts'),
                'uploading' => __('Uploading...', 'safefonts'),
                'processing' => __('Processing...', 'safefonts'),
                'upload_success' => __('Upload completed successfully!', 'safefonts'),
                'upload_error' => __('Upload failed', 'safefonts'),
                'delete_error' => __('Failed to delete font', 'safefonts'),
                'font_family_required' => __('Font family name is required', 'safefonts'),
            )
        ));
    }

    /**
     * Handle form submissions
     */
    public function handle_form_submissions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle settings save
        if (isset($_POST['safefonts_save_settings']) &&
            isset($_POST['safefonts_nonce']) &&
            wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['safefonts_nonce'])), 'safefonts_settings')) {

            $this->save_settings();
        }
    }

    /**
     * Save plugin settings
     */
    private function save_settings() {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in init() before calling this method
        $max_file_size = isset($_POST['max_file_size']) ? intval($_POST['max_file_size']) * 1024 * 1024 : 2 * 1024 * 1024;
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in init() before calling this method
        $allowed_types = isset($_POST['allowed_types']) ? array_map('sanitize_text_field', wp_unslash($_POST['allowed_types'])) : array();
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in init() before calling this method
        $preload_fonts = isset($_POST['preload_fonts']) ? array_map('sanitize_text_field', wp_unslash($_POST['preload_fonts'])) : array();
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in init() before calling this method
        $delete_data_on_uninstall = isset($_POST['delete_data_on_uninstall']) ? true : false;

        update_option('safefonts_max_file_size', $max_file_size);
        update_option('safefonts_allowed_types', $allowed_types);
        update_option('safefonts_preload_fonts', $preload_fonts);
        update_option('safefonts_delete_data_on_uninstall', $delete_data_on_uninstall);

        add_settings_error(
            'safefonts_messages',
            'safefonts_message',
            __('Settings saved successfully.', 'safefonts'),
            'updated'
        );
    }

    /**
     * Dashboard page
     */
    public function dashboard_page() {
        include SAFEFONTS_PLUGIN_DIR . 'views/dashboard-page.php';
    }

    /**
     * Fonts page
     */
    public function fonts_page() {
        $font_manager = safefonts()->font_manager;
        $fonts = $font_manager->get_fonts_by_family();

        include SAFEFONTS_PLUGIN_DIR . 'views/fonts-page.php';
    }

    /**
     * Upload page
     */
    public function upload_page() {
        include SAFEFONTS_PLUGIN_DIR . 'views/upload-page.php';
    }

    /**
     * Settings page
     */
    public function settings_page() {
        include SAFEFONTS_PLUGIN_DIR . 'views/settings-page.php';
    }

    /**
     * System Info page
     */
    public function system_page() {
        include SAFEFONTS_PLUGIN_DIR . 'views/system-page.php';
    }

    /**
     * Help & Documentation page
     */
    public function help_page() {
        include SAFEFONTS_PLUGIN_DIR . 'views/help-page.php';
    }

    /**
     * Render single font upload form
     */
    public function render_upload_form() {
        $nonce = wp_create_nonce('safefonts_upload');
        ?>
        <div class="safefonts-upload-section">
            <h3><?php esc_html_e('Upload Font File', 'safefonts'); ?></h3>
            <p class="description">
                <?php esc_html_e('Upload individual font files (.woff2, .woff, .ttf, .otf) for use in Gutenberg.', 'safefonts'); ?>
            </p>

            <form id="safefonts-upload-form" enctype="multipart/form-data">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="font_family"><?php esc_html_e('Font Family Name', 'safefonts'); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="font_family"
                                   name="font_family"
                                   placeholder="e.g., Open Sans"
                                   required>
                            <p class="description">
                                <?php esc_html_e('Enter the font family name (e.g., "Open Sans", "Roboto").', 'safefonts'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="font_weight"><?php esc_html_e('Font Weight', 'safefonts'); ?></label>
                        </th>
                        <td>
                            <select id="font_weight" name="font_weight">
                                <option value="100">100 (Thin)</option>
                                <option value="200">200 (Extra Light)</option>
                                <option value="300">300 (Light)</option>
                                <option value="400" selected>400 (Normal)</option>
                                <option value="500">500 (Medium)</option>
                                <option value="600">600 (Semi Bold)</option>
                                <option value="700">700 (Bold)</option>
                                <option value="800">800 (Extra Bold)</option>
                                <option value="900">900 (Black)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="font_style"><?php esc_html_e('Font Style', 'safefonts'); ?></label>
                        </th>
                        <td>
                            <select id="font_style" name="font_style">
                                <option value="normal">Normal</option>
                                <option value="italic">Italic</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="font_file"><?php esc_html_e('Font File', 'safefonts'); ?></label>
                        </th>
                        <td>
                            <input type="file"
                                   id="font_file"
                                   name="font_file"
                                   accept=".woff2,.woff,.ttf,.otf"
                                   required>
                            <p class="description">
                                <?php esc_html_e('Select a font file (.woff2, .woff, .ttf, .otf).', 'safefonts'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>">
                <input type="hidden" name="action" value="safefonts_upload_font">

                <p class="submit">
                    <input type="submit"
                           id="safefonts-upload-submit"
                           class="button button-primary"
                           value="<?php esc_html_e('Upload Font', 'safefonts'); ?>"
                           tabindex="0"
                           aria-label="<?php esc_html_e('Upload font file', 'safefonts'); ?>">
                </p>
            </form>

            <div id="safefonts-upload-progress" style="display: none;">
                <div class="safefonts-progress-bar">
                    <div class="safefonts-progress-fill"></div>
                </div>
                <p class="safefonts-progress-text"><?php esc_html_e('Uploading...', 'safefonts'); ?></p>
            </div>

            <div id="safefonts-upload-result"></div>
        </div>
        <?php
    }

    /**
     * Render fonts list
     */
    public function render_fonts_list($fonts) {
        if (empty($fonts)) {
            echo '<p>' . esc_html__('No fonts found. Upload font files to get started.', 'safefonts') . '</p>';
            return;
        }

        $delete_nonce = wp_create_nonce('safefonts_delete');
        $bulk_delete_nonce = wp_create_nonce('safefonts_bulk_delete');
        ?>
        <div class="safefonts-fonts-list">
            <div class="safefonts-list-header">
                <h3><?php esc_html_e('Installed Fonts', 'safefonts'); ?></h3>
                <div class="safefonts-bulk-actions">
                    <label>
                        <input type="checkbox" id="safefonts-select-all">
                        <?php esc_html_e('Select All', 'safefonts'); ?>
                    </label>
                    <button type="button"
                            id="safefonts-delete-selected"
                            class="button button-secondary"
                            data-nonce="<?php echo esc_attr($bulk_delete_nonce); ?>"
                            disabled>
                        <?php esc_html_e('Delete Selected', 'safefonts'); ?>
                    </button>
                </div>
            </div>

            <?php foreach ($fonts as $family => $family_fonts): ?>
                <div class="safefonts-font-family">
                    <h4 class="safefonts-family-name" style="font-family: '<?php echo esc_attr($family); ?>', sans-serif;">
                        <?php echo esc_html($family); ?>
                    </h4>

                    <div class="safefonts-font-variants">
                        <?php foreach ($family_fonts as $font): ?>
                            <div class="safefonts-font-item">
                                <div class="safefonts-font-info">
                                    <input type="checkbox"
                                           class="safefonts-font-select"
                                           value="<?php echo esc_attr($font->id); ?>"
                                           data-family="<?php echo esc_attr($font->font_family); ?>">
                                    <span class="safefonts-font-weight"><?php echo esc_html($font->font_weight); ?></span>
                                    <span class="safefonts-font-style"><?php echo esc_html($font->font_style); ?></span>
                                    <?php
                                    $font_format = strtoupper(pathinfo($font->file_path, PATHINFO_EXTENSION));
                                    ?>
                                    <span class="safefonts-font-format"><?php echo esc_html($font_format); ?></span>
                                    <span class="safefonts-font-size"><?php echo esc_html(size_format($font->file_size)); ?></span>
                                </div>

                                <div class="safefonts-font-preview"
                                     style="font-family: '<?php echo esc_attr($font->font_family); ?>', sans-serif;
                                            font-weight: <?php echo esc_attr($font->font_weight); ?>;
                                            font-style: <?php echo esc_attr($font->font_style); ?>;">
                                    The quick brown fox jumps over the lazy dog.
                                </div>

                                <div class="safefonts-font-actions">
                                    <?php
                                    /* translators: %s: font family name */
                                    $delete_label = sprintf(__('Delete %s font', 'safefonts'), $font->font_family);
                                    ?>
                                    <button type="button"
                                            class="button button-small safefonts-delete-font"
                                            data-font-id="<?php echo esc_attr($font->id); ?>"
                                            data-nonce="<?php echo esc_attr($delete_nonce); ?>"
                                            aria-label="<?php echo esc_attr($delete_label); ?>">
                                        <?php esc_html_e('Delete', 'safefonts'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render settings form
     */
    public function render_settings_form() {
        $max_file_size = get_option('safefonts_max_file_size', 2 * 1024 * 1024);
        $allowed_types = get_option('safefonts_allowed_types', array('woff2', 'woff', 'ttf', 'otf'));

        $nonce = wp_create_nonce('safefonts_settings');
        ?>
        <div class="safefonts-settings-section">
            <h3><?php esc_html_e('Settings', 'safefonts'); ?></h3>

            <form method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="max_file_size"><?php esc_html_e('Maximum File Size (MB)', 'safefonts'); ?></label>
                        </th>
                        <td>
                            <input type="number"
                                   id="max_file_size"
                                   name="max_file_size"
                                   value="<?php echo esc_attr($max_file_size / 1024 / 1024); ?>"
                                   min="1"
                                   max="50"
                                   step="1">
                            <p class="description">
                                <?php esc_html_e('Maximum size for individual font files.', 'safefonts'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php esc_html_e('Allowed Font Types', 'safefonts'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php esc_html_e('Allowed Font Types', 'safefonts'); ?></span>
                                </legend>

                                <label>
                                    <input type="checkbox"
                                           name="allowed_types[]"
                                           value="woff2"
                                           <?php checked(in_array('woff2', $allowed_types)); ?>>
                                    WOFF2 (<?php esc_html_e('Recommended', 'safefonts'); ?>)
                                </label><br>

                                <label>
                                    <input type="checkbox"
                                           name="allowed_types[]"
                                           value="woff"
                                           <?php checked(in_array('woff', $allowed_types)); ?>>
                                    WOFF
                                </label><br>

                                <label>
                                    <input type="checkbox"
                                           name="allowed_types[]"
                                           value="ttf"
                                           <?php checked(in_array('ttf', $allowed_types)); ?>>
                                    TTF
                                </label><br>

                                <label>
                                    <input type="checkbox"
                                           name="allowed_types[]"
                                           value="otf"
                                           <?php checked(in_array('otf', $allowed_types)); ?>>
                                    OTF
                                </label>

                                <p class="description">
                                    <?php esc_html_e('WOFF2 is recommended for best performance and compression.', 'safefonts'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php esc_html_e('Font Preloading', 'safefonts'); ?>
                        </th>
                        <td>
                            <?php
                            $preload_fonts = get_option('safefonts_preload_fonts', array());
                            $fonts = safefonts()->font_manager->get_fonts_by_family();
                            ?>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php esc_html_e('Font Preloading', 'safefonts'); ?></span>
                                </legend>

                                <?php if (empty($fonts)): ?>
                                    <p class="description">
                                        <?php esc_html_e('No fonts uploaded yet. Upload fonts to enable preloading.', 'safefonts'); ?>
                                    </p>
                                <?php else: ?>
                                    <?php foreach ($fonts as $family => $family_fonts): ?>
                                        <label>
                                            <input type="checkbox"
                                                   name="preload_fonts[]"
                                                   value="<?php echo esc_attr($family); ?>"
                                                   <?php checked(in_array($family, $preload_fonts)); ?>>
                                            <?php echo esc_html($family); ?>
                                        </label><br>
                                    <?php endforeach; ?>

                                    <p class="description">
                                        <?php esc_html_e('⚡ Font preloading tells the browser to download fonts earlier, reducing flash of invisible text (FOIT).', 'safefonts'); ?><br>
                                        <strong><?php esc_html_e('Tip:', 'safefonts'); ?></strong> <?php esc_html_e('Only preload 1-2 critical fonts (like your main body font) for best performance.', 'safefonts'); ?>
                                    </p>
                                <?php endif; ?>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php esc_html_e('Data Management', 'safefonts'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php esc_html_e('Data Management', 'safefonts'); ?></span>
                                </legend>

                                <label>
                                    <input type="checkbox"
                                           name="delete_data_on_uninstall"
                                           value="1"
                                           <?php checked(get_option('safefonts_delete_data_on_uninstall', false)); ?>>
                                    <?php esc_html_e('Delete all plugin data when uninstalling', 'safefonts'); ?>
                                </label>

                                <p class="description">
                                    <?php esc_html_e('⚠️ Warning: If checked, uninstalling this plugin will permanently delete:', 'safefonts'); ?><br>
                                    • <?php esc_html_e('All uploaded font files', 'safefonts'); ?><br>
                                    • <?php esc_html_e('Font database records', 'safefonts'); ?><br>
                                    • <?php esc_html_e('All plugin settings', 'safefonts'); ?><br>
                                    <strong><?php esc_html_e('This cannot be undone!', 'safefonts'); ?></strong> <?php esc_html_e('Leave unchecked if you plan to reinstall later.', 'safefonts'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="safefonts_nonce" value="<?php echo esc_attr($nonce); ?>">

                <p class="submit">
                    <input type="submit"
                           name="safefonts_save_settings"
                           class="button button-primary"
                           value="<?php esc_html_e('Save Settings', 'safefonts'); ?>">
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * Render system info
     */
    public function render_system_info() {
        $fonts_dir = SAFEFONTS_ASSETS_DIR;
        $fonts_url = SAFEFONTS_ASSETS_URL;

        global $wpdb;
        $table_name = $wpdb->prefix . 'chrmrtns_safefonts';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Simple count query, table name is safe
        $font_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");

        ?>
        <div class="safefonts-system-info">
            <h3><?php esc_html_e('System Information', 'safefonts'); ?></h3>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Plugin Version', 'safefonts'); ?></th>
                    <td><?php echo esc_html(SAFEFONTS_VERSION); ?></td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('WordPress Version', 'safefonts'); ?></th>
                    <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('PHP Version', 'safefonts'); ?></th>
                    <td><?php echo esc_html(PHP_VERSION); ?></td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Fonts Directory', 'safefonts'); ?></th>
                    <td>
                        <code><?php echo esc_html($fonts_dir); ?></code>
                        <?php
                        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_is_writable -- Read-only check for display purposes
                        if (is_writable($fonts_dir)):
                        ?>
                            <span class="safefonts-status-ok">✓ <?php esc_html_e('Writable', 'safefonts'); ?></span>
                        <?php else: ?>
                            <span class="safefonts-status-error">✗ <?php esc_html_e('Not writable', 'safefonts'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Fonts URL', 'safefonts'); ?></th>
                    <td><code><?php echo esc_html($fonts_url); ?></code></td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Installed Fonts', 'safefonts'); ?></th>
                    <td><?php echo esc_html($font_count); ?></td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Max Upload Size', 'safefonts'); ?></th>
                    <td><?php echo esc_html(size_format(wp_max_upload_size())); ?></td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Memory Limit', 'safefonts'); ?></th>
                    <td><?php echo esc_html(ini_get('memory_limit')); ?></td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Gutenberg Integration', 'safefonts'); ?></th>
                    <td>
                        <?php if (function_exists('wp_register_font_collection')): ?>
                            <span class="safefonts-status-ok">✓ <?php esc_html_e('WordPress 6.5+ Font Library Supported', 'safefonts'); ?></span>
                        <?php else: ?>
                            <span class="safefonts-status-error">✗ <?php esc_html_e('WordPress 6.5+ required for Font Library', 'safefonts'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <div class="safefonts-upgrade-notice">
                <h4><?php esc_html_e('🚀 Want More Features?', 'safefonts'); ?></h4>
                <p><?php esc_html_e('Upgrade to SafeFonts Pro for:', 'safefonts'); ?></p>
                <ul>
                    <li><?php esc_html_e('✅ Page Builder Integration (Elementor, Bricks, Beaver Builder, Divi, Oxygen)', 'safefonts'); ?></li>
                    <li><?php esc_html_e('✅ Bulk ZIP Package Import from Google Fonts Downloader', 'safefonts'); ?></li>
                    <li><?php esc_html_e('✅ Professional Admin Interface with Drag & Drop', 'safefonts'); ?></li>
                    <li><?php esc_html_e('✅ Advanced Font Management Tools', 'safefonts'); ?></li>
                </ul>
                <p style="margin-top: 15px;">
                    <a href="https://safefonts.com" target="_blank" class="button button-primary" style="text-decoration: none;">
                        <?php esc_html_e('Learn More About SafeFonts Pro →', 'safefonts'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php
    }
}
