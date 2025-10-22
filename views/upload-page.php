<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

settings_errors('safefonts_messages');
?>

<div class="wrap safefonts-admin-wrap">
    <h1 class="safefonts-admin-title">
        <img src="<?php echo esc_url(SAFEFONTS_PLUGIN_URL . 'assets/images/logo.png'); ?>"
             alt="SafeFonts"
             class="safefonts-logo">
        <?php echo esc_html(get_admin_page_title()); ?>
    </h1>

    <?php safefonts()->admin->render_upload_form(); ?>

    <!-- How to Use Section -->
    <div class="safefonts-upload-section">
        <h3><?php esc_html_e('How to Use SafeFonts', 'safefonts'); ?></h3>
        <div class="safefonts-instructions">
            <ol>
                <li>
                    <strong><?php esc_html_e('Upload Font Files', 'safefonts'); ?></strong><br>
                    <?php esc_html_e('Upload individual font files (.woff2, .woff, .ttf, .otf) using the form above.', 'safefonts'); ?>
                </li>
                <li>
                    <strong><?php esc_html_e('Use in Gutenberg', 'safefonts'); ?></strong><br>
                    <?php esc_html_e('Your fonts will automatically appear in the Gutenberg block editor typography settings.', 'safefonts'); ?>
                </li>
                <li>
                    <strong><?php esc_html_e('WordPress 6.5+ Font Library', 'safefonts'); ?></strong><br>
                    <?php esc_html_e('If you have WordPress 6.5+, fonts will also appear in the Site Editor Font Library.', 'safefonts'); ?>
                </li>
            </ol>
        </div>
    </div>

    <!-- Gutenberg Integration -->
    <div class="safefonts-upload-section">
        <h3><?php esc_html_e('Gutenberg Integration', 'safefonts'); ?></h3>
        <div class="safefonts-gutenberg-info">
            <p><strong><?php esc_html_e('✅ Block Editor Typography', 'safefonts'); ?></strong></p>
            <p><?php esc_html_e('Your uploaded fonts automatically appear in:', 'safefonts'); ?></p>
            <ul style="margin-left: 20px;">
                <li><?php esc_html_e('Paragraph blocks typography settings', 'safefonts'); ?></li>
                <li><?php esc_html_e('Heading blocks font family options', 'safefonts'); ?></li>
                <li><?php esc_html_e('All blocks with typography support', 'safefonts'); ?></li>
                <?php if (function_exists('wp_register_font_collection')): ?>
                    <li><?php esc_html_e('WordPress 6.5+ Site Editor Font Library', 'safefonts'); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
