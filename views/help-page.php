<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap safefonts-admin-wrap">
    <h1 class="safefonts-admin-title">
        <img src="<?php echo esc_url(SAFEFONTS_PLUGIN_URL . 'assets/images/logo.png'); ?>"
             alt="SafeFonts"
             class="safefonts-logo">
        <?php echo esc_html(get_admin_page_title()); ?>
    </h1>

    <div class="safefonts-help-content">
        <!-- CSS Tab Navigation -->
        <input type="radio" id="tab-getting-started" name="safefonts-help-tabs" class="safefonts-tab-radio" checked>
        <input type="radio" id="tab-uploading-fonts" name="safefonts-help-tabs" class="safefonts-tab-radio">
        <input type="radio" id="tab-using-fonts" name="safefonts-help-tabs" class="safefonts-tab-radio">
        <input type="radio" id="tab-managing-fonts" name="safefonts-help-tabs" class="safefonts-tab-radio">
        <input type="radio" id="tab-settings" name="safefonts-help-tabs" class="safefonts-tab-radio">
        <input type="radio" id="tab-troubleshooting" name="safefonts-help-tabs" class="safefonts-tab-radio">
        <input type="radio" id="tab-faq" name="safefonts-help-tabs" class="safefonts-tab-radio">

        <!-- Tab Labels -->
        <div class="safefonts-help-tab-nav">
            <label for="tab-getting-started" class="safefonts-tab-label"><?php esc_html_e('🚀 Getting Started', 'safefonts'); ?></label>
            <label for="tab-uploading-fonts" class="safefonts-tab-label"><?php esc_html_e('📤 Uploading', 'safefonts'); ?></label>
            <label for="tab-using-fonts" class="safefonts-tab-label"><?php esc_html_e('✍️ Using Fonts', 'safefonts'); ?></label>
            <label for="tab-managing-fonts" class="safefonts-tab-label"><?php esc_html_e('🗂️ Managing', 'safefonts'); ?></label>
            <label for="tab-settings" class="safefonts-tab-label"><?php esc_html_e('⚙️ Settings', 'safefonts'); ?></label>
            <label for="tab-troubleshooting" class="safefonts-tab-label"><?php esc_html_e('🔧 Troubleshooting', 'safefonts'); ?></label>
            <label for="tab-faq" class="safefonts-tab-label"><?php esc_html_e('❓ FAQ', 'safefonts'); ?></label>
        </div>

        <!-- Tab Content Wrapper -->
        <div class="safefonts-help-tab-wrapper">

        <!-- Getting Started Tab -->
        <div class="safefonts-help-tab-content" data-tab="tab-getting-started">
            <h2><?php esc_html_e('🚀 Getting Started', 'safefonts'); ?></h2>
            <p><?php esc_html_e('SafeFonts allows you to self-host custom fonts on your WordPress site for GDPR compliance and better performance.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('Quick Start Guide', 'safefonts'); ?></h3>
            <ol>
                <li><strong><?php esc_html_e('Upload your fonts', 'safefonts'); ?></strong> - <?php esc_html_e('Go to Upload and add your .woff2, .woff, .ttf, or .otf font files', 'safefonts'); ?></li>
                <li><strong><?php esc_html_e('Use in Gutenberg', 'safefonts'); ?></strong> - <?php esc_html_e('Your fonts automatically appear in the block editor', 'safefonts'); ?></li>
                <li><strong><?php esc_html_e('Optimize', 'safefonts'); ?></strong> - <?php esc_html_e('Configure preloading in Settings for better performance', 'safefonts'); ?></li>
            </ol>
        </div>

        <!-- Uploading Fonts Tab -->
        <div class="safefonts-help-tab-content" data-tab="tab-uploading-fonts">
            <h2><?php esc_html_e('📤 Uploading Fonts', 'safefonts'); ?></h2>

            <h3><?php esc_html_e('Supported Font Formats', 'safefonts'); ?></h3>
            <ul>
                <li><strong>WOFF2</strong> - <?php esc_html_e('Recommended. Best compression and modern browser support.', 'safefonts'); ?></li>
                <li><strong>WOFF</strong> - <?php esc_html_e('Good fallback for older browsers.', 'safefonts'); ?></li>
                <li><strong>TTF</strong> - <?php esc_html_e('TrueType fonts, widely compatible.', 'safefonts'); ?></li>
                <li><strong>OTF</strong> - <?php esc_html_e('OpenType fonts with advanced typography features.', 'safefonts'); ?></li>
            </ul>

            <h3><?php esc_html_e('How to Upload', 'safefonts'); ?></h3>
            <ol>
                <li><?php esc_html_e('Go to SafeFonts → Upload', 'safefonts'); ?></li>
                <li><?php esc_html_e('Enter the font family name (e.g., "Open Sans")', 'safefonts'); ?></li>
                <li><?php esc_html_e('Select the font weight (100-900)', 'safefonts'); ?></li>
                <li><?php esc_html_e('Choose the font style (Normal or Italic)', 'safefonts'); ?></li>
                <li><?php esc_html_e('Upload your font file', 'safefonts'); ?></li>
                <li><?php esc_html_e('Click Upload Font', 'safefonts'); ?></li>
            </ol>

            <div class="safefonts-help-tip">
                <strong><?php esc_html_e('💡 Pro Tip:', 'safefonts'); ?></strong>
                <?php esc_html_e('For best results, use WOFF2 format. It offers the best compression (30-50% smaller than WOFF) and is supported by all modern browsers.', 'safefonts'); ?>
            </div>
        </div>

        <!-- Using Fonts Tab -->
        <div class="safefonts-help-tab-content" data-tab="tab-using-fonts">
            <h2><?php esc_html_e('✍️ Using Fonts', 'safefonts'); ?></h2>

            <h3><?php esc_html_e('Gutenberg Block Editor', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Your uploaded fonts automatically appear in:', 'safefonts'); ?></p>
            <ul>
                <li><?php esc_html_e('Paragraph blocks → Typography → Font family', 'safefonts'); ?></li>
                <li><?php esc_html_e('Heading blocks → Typography → Font family', 'safefonts'); ?></li>
                <li><?php esc_html_e('Any block with typography support', 'safefonts'); ?></li>
            </ul>

            <h3><?php esc_html_e('WordPress 6.5+ Site Editor', 'safefonts'); ?></h3>
            <p><?php esc_html_e('If you have WordPress 6.5 or higher, your fonts also appear in:', 'safefonts'); ?></p>
            <ul>
                <li><?php esc_html_e('Site Editor → Styles → Typography → Font families', 'safefonts'); ?></li>
                <li><?php esc_html_e('Global Styles → Typography settings', 'safefonts'); ?></li>
            </ul>

            <div class="safefonts-help-note">
                <strong><?php esc_html_e('ℹ️ Note:', 'safefonts'); ?></strong>
                <?php esc_html_e('After uploading fonts, you may need to refresh your browser to see them in the editor.', 'safefonts'); ?>
            </div>
        </div>

        <!-- Managing Fonts Tab -->
        <div class="safefonts-help-tab-content" data-tab="tab-managing-fonts">
            <h2><?php esc_html_e('🗂️ Managing Fonts', 'safefonts'); ?></h2>

            <h3><?php esc_html_e('View Your Fonts', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Go to SafeFonts → Fonts to see all your uploaded font families and variants.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('Delete Fonts', 'safefonts'); ?></h3>
            <p><?php esc_html_e('On the Fonts page, click the Delete button next to any font file to remove it.', 'safefonts'); ?></p>

            <div class="safefonts-help-warning">
                <strong><?php esc_html_e('⚠️ Warning:', 'safefonts'); ?></strong>
                <?php esc_html_e('Deleting a font will break any pages or templates using it. Make sure to update your content before deleting fonts.', 'safefonts'); ?>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="safefonts-help-tab-content" data-tab="tab-settings">
            <h2><?php esc_html_e('⚙️ Settings', 'safefonts'); ?></h2>

            <h3><?php esc_html_e('Maximum File Size', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Set the maximum allowed size for individual font files (1-50 MB). Default is 2 MB.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('Allowed Font Types', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Choose which font file formats can be uploaded. WOFF2 is recommended for best performance.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('Font Preloading', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Enable preloading for 1-2 critical fonts (like your main body font) to improve page load performance.', 'safefonts'); ?></p>
            <div class="safefonts-help-tip">
                <strong><?php esc_html_e('💡 Pro Tip:', 'safefonts'); ?></strong>
                <?php esc_html_e('Only preload fonts that are used above the fold. Preloading too many fonts can actually hurt performance.', 'safefonts'); ?>
            </div>

            <h3><?php esc_html_e('Data Management', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Choose whether to delete all plugin data when uninstalling. Leave unchecked if you plan to reinstall later.', 'safefonts'); ?></p>
        </div>

        <!-- Troubleshooting Tab -->
        <div class="safefonts-help-tab-content" data-tab="tab-troubleshooting">
            <h2><?php esc_html_e('🔧 Troubleshooting', 'safefonts'); ?></h2>

            <h3><?php esc_html_e('Fonts not appearing in editor', 'safefonts'); ?></h3>
            <ol>
                <li><?php esc_html_e('Clear your browser cache and refresh the page', 'safefonts'); ?></li>
                <li><?php esc_html_e('Make sure the font was uploaded successfully (check SafeFonts → Fonts)', 'safefonts'); ?></li>
                <li><?php esc_html_e('Try using a different browser to rule out browser-specific issues', 'safefonts'); ?></li>
            </ol>

            <h3><?php esc_html_e('Upload fails', 'safefonts'); ?></h3>
            <ol>
                <li><?php esc_html_e('Check that your file size is under the maximum allowed size', 'safefonts'); ?></li>
                <li><?php esc_html_e('Verify the font format is allowed in Settings', 'safefonts'); ?></li>
                <li><?php esc_html_e('Ensure the uploads directory is writable (check System Info)', 'safefonts'); ?></li>
            </ol>

            <h3><?php esc_html_e('Fonts not loading on frontend', 'safefonts'); ?></h3>
            <ol>
                <li><?php esc_html_e('Check browser console for 404 errors', 'safefonts'); ?></li>
                <li><?php esc_html_e('Verify fonts directory is accessible (check System Info)', 'safefonts'); ?></li>
                <li><?php esc_html_e('Try disabling caching plugins temporarily', 'safefonts'); ?></li>
                <li><?php esc_html_e('Clear your site cache and browser cache', 'safefonts'); ?></li>
            </ol>
        </div>

        <!-- FAQ Tab -->
        <div class="safefonts-help-tab-content" data-tab="tab-faq">
            <h2><?php esc_html_e('❓ Frequently Asked Questions', 'safefonts'); ?></h2>

            <h3><?php esc_html_e('Is SafeFonts GDPR compliant?', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Yes! SafeFonts hosts all fonts locally on your server, so no data is sent to external services like Google Fonts. This makes it fully GDPR compliant.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('Which font format should I use?', 'safefonts'); ?></h3>
            <p><?php esc_html_e('WOFF2 is recommended for best compression and modern browser support. You can also upload WOFF as a fallback for older browsers.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('How many fonts can I upload?', 'safefonts'); ?></h3>
            <p><?php esc_html_e('There is no limit on the number of fonts you can upload, but keep in mind that too many fonts can slow down your site.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('Can I use these fonts in page builders?', 'safefonts'); ?></h3>
            <p><?php esc_html_e('SafeFonts Free works with Gutenberg. For page builder support (Elementor, Bricks, Beaver Builder, Divi, Oxygen), upgrade to SafeFonts Pro.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('Where are fonts stored?', 'safefonts'); ?></h3>
            <p><?php esc_html_e('Fonts are stored in /wp-content/uploads/safefonts/ directory and registered in the WordPress database.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('Can I import Google Fonts?', 'safefonts'); ?></h3>
            <p><?php esc_html_e('SafeFonts Free requires manual upload of font files. SafeFonts Pro includes bulk Google Fonts import feature.', 'safefonts'); ?></p>

            <h3><?php esc_html_e('What happens if I delete the plugin?', 'safefonts'); ?></h3>
            <p><?php esc_html_e('By default, your fonts and settings are preserved. If you want to delete all data, enable "Delete all plugin data when uninstalling" in Settings before uninstalling.', 'safefonts'); ?></p>
        </div>

        </div><!-- .safefonts-help-tab-wrapper -->

        <!-- Support -->
        <div class="safefonts-help-support">
            <h2><?php esc_html_e('💬 Need More Help?', 'safefonts'); ?></h2>
            <p><?php esc_html_e('If you cannot find the answer to your question in this documentation:', 'safefonts'); ?></p>
            <ul>
                <li><a href="https://wordpress.org/support/plugin/safefonts/" target="_blank"><?php esc_html_e('Visit the WordPress.org support forum', 'safefonts'); ?></a></li>
                <li><a href="https://github.com/chrmrtns/safefonts/issues" target="_blank"><?php esc_html_e('Report bugs on GitHub', 'safefonts'); ?></a></li>
                <li><a href="https://safefonts.com/contact" target="_blank"><?php esc_html_e('Contact us directly', 'safefonts'); ?></a></li>
            </ul>
        </div>
    </div>
</div>
