<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get plugin stats
$font_manager = safefonts()->font_manager;
$fonts = $font_manager->get_fonts_by_family();
$font_count = count($fonts);

global $wpdb;
$table_name = $wpdb->prefix . 'chrmrtns_safefonts';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Simple count query
$total_files = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
?>

<div class="wrap safefonts-admin-wrap">
    <h1 class="safefonts-admin-title">
        <img src="<?php echo esc_url(SAFEFONTS_PLUGIN_URL . 'assets/images/logo.png'); ?>"
             alt="SafeFonts"
             class="safefonts-logo">
        <?php esc_html_e('Welcome to SafeFonts', 'safefonts'); ?>
    </h1>

    <div class="safefonts-dashboard">
        <!-- Welcome Message -->
        <div class="safefonts-dashboard-hero">
            <h2><?php esc_html_e('🚀 Secure Font Management for WordPress', 'safefonts'); ?></h2>
            <p class="description">
                <?php esc_html_e('Host your fonts locally for GDPR compliance, better performance, and complete control. Your fonts integrate seamlessly with Gutenberg and the WordPress Site Editor.', 'safefonts'); ?>
            </p>
        </div>

        <!-- Quick Stats -->
        <div class="safefonts-dashboard-stats">
            <div class="safefonts-stat-card">
                <div class="safefonts-stat-number"><?php echo esc_html($font_count); ?></div>
                <div class="safefonts-stat-label"><?php esc_html_e('Font Families', 'safefonts'); ?></div>
            </div>
            <div class="safefonts-stat-card">
                <div class="safefonts-stat-number"><?php echo esc_html($total_files); ?></div>
                <div class="safefonts-stat-label"><?php esc_html_e('Font Files', 'safefonts'); ?></div>
            </div>
            <div class="safefonts-stat-card">
                <div class="safefonts-stat-number">✓</div>
                <div class="safefonts-stat-label"><?php esc_html_e('GDPR Compliant', 'safefonts'); ?></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="safefonts-dashboard-actions">
            <h3><?php esc_html_e('Quick Actions', 'safefonts'); ?></h3>
            <div class="safefonts-action-cards">
                <a href="<?php echo esc_url(admin_url('admin.php?page=safefonts-upload')); ?>" class="safefonts-action-card">
                    <span class="dashicons dashicons-upload"></span>
                    <h4><?php esc_html_e('Upload Fonts', 'safefonts'); ?></h4>
                    <p><?php esc_html_e('Add new font files to your library', 'safefonts'); ?></p>
                </a>

                <a href="<?php echo esc_url(admin_url('admin.php?page=safefonts-fonts')); ?>" class="safefonts-action-card">
                    <span class="dashicons dashicons-editor-textcolor"></span>
                    <h4><?php esc_html_e('Manage Fonts', 'safefonts'); ?></h4>
                    <p><?php esc_html_e('View and organize your font collection', 'safefonts'); ?></p>
                </a>

                <a href="<?php echo esc_url(admin_url('admin.php?page=safefonts-settings')); ?>" class="safefonts-action-card">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <h4><?php esc_html_e('Settings', 'safefonts'); ?></h4>
                    <p><?php esc_html_e('Configure file sizes and preloading', 'safefonts'); ?></p>
                </a>

                <a href="<?php echo esc_url(admin_url('admin.php?page=safefonts-help')); ?>" class="safefonts-action-card">
                    <span class="dashicons dashicons-book"></span>
                    <h4><?php esc_html_e('Documentation', 'safefonts'); ?></h4>
                    <p><?php esc_html_e('Learn how to use SafeFonts', 'safefonts'); ?></p>
                </a>
            </div>
        </div>

        <!-- Getting Started -->
        <?php if ($font_count === 0): ?>
        <div class="safefonts-dashboard-getting-started">
            <h3><?php esc_html_e('🎯 Getting Started', 'safefonts'); ?></h3>
            <ol class="safefonts-steps">
                <li>
                    <strong><?php esc_html_e('Upload Your Fonts', 'safefonts'); ?></strong>
                    <p><?php esc_html_e('Go to Upload and add your font files (.woff2, .woff, .ttf, .otf)', 'safefonts'); ?></p>
                </li>
                <li>
                    <strong><?php esc_html_e('Use in Gutenberg', 'safefonts'); ?></strong>
                    <p><?php esc_html_e('Your fonts automatically appear in the block editor typography settings', 'safefonts'); ?></p>
                </li>
                <li>
                    <strong><?php esc_html_e('Optimize Performance', 'safefonts'); ?></strong>
                    <p><?php esc_html_e('Enable font preloading in Settings for faster page loads', 'safefonts'); ?></p>
                </li>
            </ol>
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=safefonts-upload')); ?>" class="button button-primary button-hero">
                    <?php esc_html_e('Upload Your First Font', 'safefonts'); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>

        <!-- Features Overview -->
        <div class="safefonts-dashboard-features">
            <h3><?php esc_html_e('✨ Features', 'safefonts'); ?></h3>
            <div class="safefonts-feature-grid">
                <div class="safefonts-feature">
                    <span class="dashicons dashicons-shield"></span>
                    <h4><?php esc_html_e('GDPR Compliant', 'safefonts'); ?></h4>
                    <p><?php esc_html_e('Self-host fonts locally, no external requests to Google or other CDNs', 'safefonts'); ?></p>
                </div>

                <div class="safefonts-feature">
                    <span class="dashicons dashicons-performance"></span>
                    <h4><?php esc_html_e('Better Performance', 'safefonts'); ?></h4>
                    <p><?php esc_html_e('Serve fonts from your own server for faster load times and better control', 'safefonts'); ?></p>
                </div>

                <div class="safefonts-feature">
                    <span class="dashicons dashicons-admin-customizer"></span>
                    <h4><?php esc_html_e('Gutenberg Integration', 'safefonts'); ?></h4>
                    <p><?php esc_html_e('Fonts automatically appear in the block editor typography settings', 'safefonts'); ?></p>
                </div>

                <div class="safefonts-feature">
                    <span class="dashicons dashicons-admin-site-alt3"></span>
                    <h4><?php esc_html_e('Site Editor Support', 'safefonts'); ?></h4>
                    <p><?php esc_html_e('WordPress 6.5+ users get full Site Editor Font Library integration', 'safefonts'); ?></p>
                </div>
            </div>
        </div>

        <!-- Upgrade to Pro (if not Pro) -->
        <?php if (!defined('SAFEFONTS_PRO_VERSION')): ?>
        <div class="safefonts-dashboard-upgrade">
            <h3><?php esc_html_e('🚀 Upgrade to SafeFonts Pro', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Get even more features with SafeFonts Pro:', 'safefonts'); ?></p>
            <ul class="safefonts-pro-features">
                <li>✅ <?php esc_html_e('Bulk import Google Fonts families', 'safefonts'); ?></li>
                <li>✅ <?php esc_html_e('Elementor, Bricks, Beaver Builder, Divi, Oxygen support', 'safefonts'); ?></li>
                <li>✅ <?php esc_html_e('Advanced font management tools', 'safefonts'); ?></li>
                <li>✅ <?php esc_html_e('Priority support', 'safefonts'); ?></li>
            </ul>
            <p>
                <a href="https://safefonts.com/pro" target="_blank" class="button button-primary">
                    <?php esc_html_e('Learn More About Pro', 'safefonts'); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>
