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

    <?php safefonts()->admin->render_settings_form(); ?>
</div>
