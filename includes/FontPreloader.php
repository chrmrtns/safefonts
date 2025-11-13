<?php
/**
 * Font Preloader
 *
 * Handles font preloading with per-weight selection
 *
 * @package SafeFonts
 */

namespace Chrmrtns\SafeFonts;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * FontPreloader class
 */
class FontPreloader {

    /**
     * Font Manager instance
     *
     * @var FontManager
     */
    private $font_manager;

    /**
     * Constructor
     *
     * @param FontManager $font_manager Font manager instance
     */
    public function __construct($font_manager) {
        $this->font_manager = $font_manager;
    }

    /**
     * Add font preload tags to <head>
     *
     * @return void
     */
    public function add_preload_tags() {
        $preload_fonts = get_option('chrmrtns_safefonts_preload_fonts', array());

        if (empty($preload_fonts)) {
            return;
        }

        $fonts = $this->font_manager->get_fonts();

        foreach ($fonts as $font) {
            // Create identifier: family-weight (e.g., "Roboto-400" or "Roboto-400italic")
            $font_identifier = $this->get_font_identifier($font);

            // Check if this specific font (family + weight + style) is marked for preloading
            if (!in_array($font_identifier, $preload_fonts, true)) {
                continue;
            }

            // Use relative path from database
            $font_url = CHRMRTNS_SAFEFONTS_ASSETS_URL . $font->file_path;
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
     * Get font identifier (family-weight-style)
     *
     * @param object $font Font object
     * @return string Identifier (e.g., "Roboto-400" or "Roboto-700italic")
     */
    private function get_font_identifier($font) {
        $identifier = $font->font_family . '-' . $font->font_weight;

        // Add italic suffix if italic style
        if ($font->font_style === 'italic') {
            $identifier .= 'italic';
        }

        return $identifier;
    }

    /**
     * Migrate old format (families only) to new format (family-weight)
     *
     * @return void
     */
    public function migrate_old_format() {
        $preload_fonts = get_option('chrmrtns_safefonts_preload_fonts', array());

        if (empty($preload_fonts)) {
            return;
        }

        // Check if already migrated (new format has hyphens)
        $needs_migration = false;
        foreach ($preload_fonts as $font) {
            if (strpos($font, '-') === false) {
                $needs_migration = true;
                break;
            }
        }

        if (!$needs_migration) {
            return;
        }

        // Migrate: Convert each family to family-weight for all available weights
        $new_format = array();
        $fonts = $this->font_manager->get_fonts();

        foreach ($fonts as $font) {
            // If this font's family was in old preload list, add it with weight
            if (in_array($font->font_family, $preload_fonts, true)) {
                $new_format[] = $this->get_font_identifier($font);
            }
        }

        // Update to new format
        update_option('chrmrtns_safefonts_preload_fonts', array_unique($new_format));
    }

    /**
     * Get fonts grouped by family with weight info
     *
     * @return array Fonts grouped by family
     */
    public function get_fonts_grouped() {
        $fonts = $this->font_manager->get_fonts();
        $grouped = array();

        foreach ($fonts as $font) {
            $family = $font->font_family;

            if (!isset($grouped[$family])) {
                $grouped[$family] = array();
            }

            $extension = strtolower(pathinfo($font->file_path, PATHINFO_EXTENSION));

            $grouped[$family][] = array(
                'identifier' => $this->get_font_identifier($font),
                'weight' => $font->font_weight,
                'style' => $font->font_style,
                'format' => $extension,
                'label' => $this->get_weight_label($font->font_weight, $font->font_style, $extension)
            );
        }

        return $grouped;
    }

    /**
     * Get human-readable weight label
     *
     * @param string $weight Font weight (100-900)
     * @param string $style Font style (normal/italic)
     * @param string $format File format (woff2, woff, ttf, otf)
     * @return string Label (e.g., "400 (Regular) - woff2" or "700 (Bold) Italic - woff")
     */
    private function get_weight_label($weight, $style, $format) {
        $weight_names = array(
            '100' => 'Thin',
            '200' => 'Extra Light',
            '300' => 'Light',
            '400' => 'Regular',
            '500' => 'Medium',
            '600' => 'Semi Bold',
            '700' => 'Bold',
            '800' => 'Extra Bold',
            '900' => 'Black'
        );

        $name = isset($weight_names[$weight]) ? $weight_names[$weight] : 'Regular';
        $label = $weight . ' (' . $name . ')';

        if ($style === 'italic') {
            $label .= ' Italic';
        }

        $label .= ' - ' . strtoupper($format);

        return $label;
    }
}
