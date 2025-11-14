<?php
/**
 * CSS Reference Page
 *
 * Displays comprehensive CSS documentation for SafeFonts including:
 * - CSS variables for custom CSS usage
 * - Gutenberg CSS classes
 * - General CSS implementation details
 *
 * @package SafeFonts
 * @since 1.1.10
 */

namespace Chrmrtns\SafeFonts\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class CssReferencePage {

    /**
     * Font Manager instance
     *
     * @var \Chrmrtns\SafeFonts\FontManager
     */
    private $font_manager;

    /**
     * Constructor
     *
     * @param \Chrmrtns\SafeFonts\FontManager $font_manager Font manager instance
     */
    public function __construct($font_manager) {
        $this->font_manager = $font_manager;
    }

    /**
     * Render the CSS reference page
     *
     * @return void
     */
    public function render() {
        ?>
        <div class="wrap safefonts-css-reference-page">
            <h1><?php esc_html_e('CSS Reference', 'safefonts'); ?></h1>

            <p class="description" style="margin-bottom: 30px;">
                <?php esc_html_e('This page provides comprehensive CSS reference documentation for using SafeFonts in your custom code and themes.', 'safefonts'); ?>
            </p>

            <?php
            $fonts_by_family = $this->font_manager->get_fonts_by_family();

            if (empty($fonts_by_family)):
            ?>
                <div class="notice notice-warning">
                    <p>
                        <strong><?php esc_html_e('No fonts found.', 'safefonts'); ?></strong>
                        <?php esc_html_e('Please upload fonts to see CSS reference documentation.', 'safefonts'); ?>
                    </p>
                </div>
            <?php
            else:
                $this->render_css_variables_section($fonts_by_family);
                $this->render_gutenberg_classes_section($fonts_by_family);
                $this->render_general_info_section();
            endif;
            ?>
        </div>
        <?php
    }

    /**
     * Render CSS variables section
     *
     * @param array $fonts_by_family Fonts grouped by family
     * @return void
     */
    private function render_css_variables_section($fonts_by_family) {
        ?>
        <div class="safefonts-section safefonts-css-variables-section">
            <h2><?php esc_html_e('CSS Variables (Custom Properties)', 'safefonts'); ?></h2>

            <p class="description">
                <?php esc_html_e('SafeFonts automatically generates CSS variables for all your fonts. Use these in custom CSS or any theme that supports CSS custom properties.', 'safefonts'); ?>
            </p>

            <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th style="width: 25%;"><?php esc_html_e('Font Family', 'safefonts'); ?></th>
                        <th style="width: 30%;"><?php esc_html_e('CSS Variable Name', 'safefonts'); ?></th>
                        <th style="width: 45%;"><?php esc_html_e('Usage in CSS', 'safefonts'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fonts_by_family as $family => $variants):
                        $slug = sanitize_title($family);
                        $variable = '--safefonts-' . $slug;
                        $usage = 'var(' . $variable . ')';
                        $fallback = $this->font_manager->get_font_fallback($family);
                        $full_value = "'" . $family . "'" . $fallback;
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html($family); ?></strong></td>
                        <td>
                            <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; font-size: 12px;">
                                <?php echo esc_html($variable); ?>
                            </code>
                        </td>
                        <td>
                            <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 12px;"
                                  onclick="navigator.clipboard.writeText('<?php echo esc_js($usage); ?>'); this.style.background='#d4edda'; setTimeout(() => this.style.background='#f0f0f1', 2000);"
                                  title="<?php esc_attr_e('Click to copy', 'safefonts'); ?>">
                                <?php echo esc_html($usage); ?>
                            </code>
                            <span style="font-size: 11px; color: #666; margin-left: 8px;">
                                <?php esc_html_e('(click to copy)', 'safefonts'); ?>
                            </span>
                            <br>
                            <small style="color: #666; margin-top: 4px; display: inline-block;">
                                <?php
                                printf(
                                    /* translators: %s: font-family CSS value */
                                    esc_html__('Resolves to: %s', 'safefonts'),
                                    '<code style="font-size: 11px;">' . esc_html($full_value) . '</code>'
                                );
                                ?>
                            </small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="notice notice-info inline" style="margin-top: 20px;">
                <h3><?php esc_html_e('How to Use in Custom CSS', 'safefonts'); ?></h3>
                <p><?php esc_html_e('Use CSS variables in your theme\'s custom CSS (Appearance → Customize → Additional CSS) or child theme stylesheet:', 'safefonts'); ?></p>
                <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>.my-custom-heading {
    font-family: var(--safefonts-your-font-slug);
}

/* Example with fallback */
.another-element {
    font-family: var(--safefonts-your-font-slug, Arial, sans-serif);
}</code></pre>

                <h3 style="margin-top: 20px;"><?php esc_html_e('Advanced Theme Integration', 'safefonts'); ?></h3>
                <p><?php esc_html_e('Use CSS variables in your child theme or custom theme development:', 'safefonts'); ?></p>
                <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>/* In your child theme style.css */
body {
    font-family: var(--safefonts-your-font-slug);
}

h1, h2, h3 {
    font-family: var(--safefonts-heading-font-slug);
}</code></pre>
            </div>
        </div>
        <?php
    }

    /**
     * Render Gutenberg classes section
     *
     * @param array $fonts_by_family Fonts grouped by family
     * @return void
     */
    private function render_gutenberg_classes_section($fonts_by_family) {
        ?>
        <div class="safefonts-section safefonts-gutenberg-classes-section" style="margin-top: 40px;">
            <h2><?php esc_html_e('Gutenberg CSS Classes', 'safefonts'); ?></h2>

            <p class="description">
                <?php esc_html_e('SafeFonts automatically generates CSS classes for Gutenberg blocks. These classes are available when using the block editor in HTML mode or custom block development.', 'safefonts'); ?>
            </p>

            <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th style="width: 25%;"><?php esc_html_e('Font Family', 'safefonts'); ?></th>
                        <th style="width: 35%;"><?php esc_html_e('CSS Class', 'safefonts'); ?></th>
                        <th style="width: 40%;"><?php esc_html_e('Usage Example', 'safefonts'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fonts_by_family as $family => $variants):
                        $slug = sanitize_title($family);
                        $class = 'has-' . $slug . '-font-family';
                        $example = '<p class="' . $class . '">Your text</p>';
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html($family); ?></strong></td>
                        <td>
                            <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 12px;"
                                  onclick="navigator.clipboard.writeText('<?php echo esc_js($class); ?>'); this.style.background='#d4edda'; setTimeout(() => this.style.background='#f0f0f1', 2000);"
                                  title="<?php esc_attr_e('Click to copy', 'safefonts'); ?>">
                                <?php echo esc_html($class); ?>
                            </code>
                            <span style="font-size: 11px; color: #666; margin-left: 8px;">
                                <?php esc_html_e('(click to copy)', 'safefonts'); ?>
                            </span>
                        </td>
                        <td>
                            <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; font-size: 11px; display: block; margin-top: 4px;">
                                <?php echo esc_html($example); ?>
                            </code>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="notice notice-info inline" style="margin-top: 20px;">
                <h3><?php esc_html_e('How to Use in Gutenberg', 'safefonts'); ?></h3>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li><?php esc_html_e('Select a block in the Gutenberg editor', 'safefonts'); ?></li>
                    <li><?php esc_html_e('Click the three dots (Options) and select "Edit as HTML"', 'safefonts'); ?></li>
                    <li><?php esc_html_e('Add the CSS class to your block\'s HTML', 'safefonts'); ?></li>
                    <li><?php esc_html_e('Switch back to visual editing and the font will be applied', 'safefonts'); ?></li>
                </ol>

                <p><strong><?php esc_html_e('Note:', 'safefonts'); ?></strong> <?php esc_html_e('For regular Gutenberg usage, fonts are automatically available in the block editor Typography settings. These CSS classes are mainly for advanced use cases and custom block development.', 'safefonts'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Render general CSS information section
     *
     * @return void
     */
    private function render_general_info_section() {
        ?>
        <div class="safefonts-section safefonts-general-info-section" style="margin-top: 40px;">
            <h2><?php esc_html_e('General CSS Implementation', 'safefonts'); ?></h2>

            <div class="safefonts-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">

                <div class="safefonts-info-card" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px;">
                    <h3><?php esc_html_e('@font-face Declarations', 'safefonts'); ?></h3>
                    <p><?php esc_html_e('SafeFonts automatically generates @font-face declarations for all uploaded fonts. These are injected into the page <head> section.', 'safefonts'); ?></p>
                    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 11px; overflow-x: auto;"><code>@font-face {
  font-family: 'Your Font';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('/wp-content/uploads/safefonts/...');
}</code></pre>
                </div>

                <div class="safefonts-info-card" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px;">
                    <h3><?php esc_html_e('Performance Optimization', 'safefonts'); ?></h3>
                    <p><?php esc_html_e('SafeFonts uses font-display: swap for optimal performance. This ensures text remains visible during font loading.', 'safefonts'); ?></p>
                    <ul style="margin: 10px 0 0 20px;">
                        <li><?php esc_html_e('Fonts are served from your own server', 'safefonts'); ?></li>
                        <li><?php esc_html_e('No external font API requests', 'safefonts'); ?></li>
                        <li><?php esc_html_e('GDPR compliant by default', 'safefonts'); ?></li>
                        <li><?php esc_html_e('Reduced page load dependencies', 'safefonts'); ?></li>
                    </ul>
                </div>

                <div class="safefonts-info-card" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px;">
                    <h3><?php esc_html_e('Font File Location', 'safefonts'); ?></h3>
                    <p><?php esc_html_e('Uploaded font files are stored securely in:', 'safefonts'); ?></p>
                    <code style="background: #f0f0f1; padding: 8px; border-radius: 3px; display: block; margin: 10px 0;">
                        wp-content/uploads/safefonts/{font-family-slug}/
                    </code>
                    <p><?php esc_html_e('Each font family has its own folder with all weight and style variants.', 'safefonts'); ?></p>
                </div>

                <div class="safefonts-info-card" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px;">
                    <h3><?php esc_html_e('CSS File Generation', 'safefonts'); ?></h3>
                    <p><?php esc_html_e('A single fonts.css file is generated and cached for all fonts:', 'safefonts'); ?></p>
                    <code style="background: #f0f0f1; padding: 8px; border-radius: 3px; display: block; margin: 10px 0;">
                        wp-content/uploads/safefonts/fonts.css
                    </code>
                    <p><?php esc_html_e('This file is automatically regenerated when fonts are added, updated, or removed.', 'safefonts'); ?></p>
                </div>

            </div>

            <div class="notice notice-info inline" style="margin-top: 20px;">
                <h3><?php esc_html_e('Need Page Builder Integration?', 'safefonts'); ?></h3>
                <p>
                    <?php
                    printf(
                        /* translators: %s: link to SafeFonts Pro */
                        esc_html__('For automatic integration with popular page builders and themes (Elementor, Bricks, Beaver Builder, Divi, Oxygen, Brizy, Builderius, Astra, GeneratePress, Kadence, Blocksy, and more), check out %s.', 'safefonts'),
                        '<a href="https://safefonts.com" target="_blank">' . esc_html__('SafeFonts Pro', 'safefonts') . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
        <?php
    }
}
