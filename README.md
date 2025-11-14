# SafeFonts

**Secure, GDPR-compliant font management for WordPress with seamless Gutenberg integration and CSS variables support.**

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-6.2%2B-blue.svg)](https://wordpress.org/plugins/safefonts/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%20v2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.1.9-orange.svg)](https://github.com/chrmrtns/safefonts/releases)

SafeFonts allows you to self-host custom fonts on your WordPress site for GDPR compliance and better performance. Your fonts automatically integrate with the Gutenberg block editor and WordPress 6.5+ Font Library.

## 🚀 Features

- **🔒 GDPR Compliant** - 100% local font hosting, no external requests
- **✨ Gutenberg Integration** - Fonts automatically appear in block editor typography settings
- **🎨 WordPress 6.5+ Font Library** - Full integration with the Site Editor
- **🎯 CSS Variables Support** - Use `var(--safefonts-font-slug)` in custom CSS for manual integration
- **📖 CSS Reference Page** - Comprehensive documentation for CSS variables and implementation
- **🛡️ Secure Upload Validation** - Magic byte verification, MIME type checking, file hashing
- **⚡ Font Preloading** - Reduce FOIT (Flash of Invisible Text) for better performance
- **🌍 Translation Ready** - Full i18n support with POT file included
- **📱 Responsive Admin Interface** - Clean, modern UI with tabbed navigation

## 📋 Requirements

- WordPress 6.2 or higher
- PHP 7.4 or higher
- Write permissions for `wp-content/uploads/safefonts/` directory

## 🎯 Supported Font Formats

- **WOFF2** (recommended) - Best compression and modern browser support
- **WOFF** - Good fallback for older browsers
- **TTF** - TrueType fonts, widely compatible
- **OTF** - OpenType fonts with advanced typography features

## 💡 How It Works

1. **Upload Fonts** - Go to SafeFonts → Upload and add your font files
2. **Configure Details** - Set font family name, weight (100-900), and style (normal/italic)
3. **Use Everywhere** - Fonts automatically appear in:
   - Gutenberg block editor typography controls
   - WordPress 6.5+ Site Editor Font Library
   - Global Styles typography settings
   - CSS variables for custom CSS usage (check CSS Reference page)

## 📦 Installation

### From WordPress Dashboard

1. Go to **Plugins > Add New**
2. Search for "SafeFonts"
3. Click **Install Now**, then **Activate**
4. Go to **SafeFonts** in the admin menu to start uploading fonts

### Manual Installation

1. Download the plugin zip file
2. Go to **Plugins > Add New > Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Click **Activate Plugin**

## 🎨 Usage

### Uploading Fonts

1. Go to **SafeFonts → Upload**
2. Enter the font family name (e.g., "Open Sans")
3. Select the font weight (100-900)
4. Choose the font style (Normal or Italic)
5. Upload your font file
6. Click **Upload Font**

### Using Fonts in Gutenberg

1. Edit any post or page
2. Select a block with text (Paragraph, Heading, etc.)
3. Open Typography settings in the block sidebar
4. Your SafeFonts will appear in the font dropdown

### Using Fonts in Site Editor (WordPress 6.5+)

1. Go to **Appearance → Editor**
2. Click **Styles → Typography**
3. Your fonts appear in the Font Library
4. Click "Manage font library" to see all your SafeFonts

## ⚙️ Settings

### Maximum File Size
Set the maximum size for font uploads (1-50 MB). Default is 2 MB.

### Allowed Font Types
Choose which font formats can be uploaded (WOFF2, WOFF, TTF, OTF).

### Font Preloading
Enable preloading for 1-2 critical fonts to improve page load performance. Only preload fonts used above the fold.

### Data Management
Choose whether to keep or delete plugin data when uninstalling.

## 🔧 For Developers

### Template Functions

```php
// Get main plugin instance
safefonts();

// Get all fonts from database
safefonts()->font_manager->get_fonts();

// Get fonts grouped by family
safefonts()->font_manager->get_fonts_by_family();
```

### Database Structure

**Table:** `wp_chrmrtns_safefonts`

- `id` - Font ID
- `font_family` - Font family name
- `font_style` - normal or italic
- `font_weight` - 100-900
- `file_path` - Filename only (files in uploads/safefonts/)
- `file_hash` - SHA-256 hash for integrity
- `file_size` - File size in bytes
- `mime_type` - Validated MIME type
- `created_at` - Upload timestamp
- `updated_at` - Last update timestamp

### Architecture

SafeFonts uses modern PHP namespaces and PSR-4 autoloading:

- `Chrmrtns\SafeFonts\Core` - Main plugin class
- `Chrmrtns\SafeFonts\FontManager` - Font validation and management
- `Chrmrtns\SafeFonts\Admin\AdminInterface` - Admin UI

## 🆙 Upgrade to SafeFonts Pro

Want more features? **[SafeFonts Pro](https://safefonts.com)** includes:

- ✅ **14 Page Builder & Theme Integrations** - Elementor, Bricks, Beaver Builder, Divi, Oxygen, Brizy, Builderius, Astra, GeneratePress, Kadence, Blocksy, Kadence Blocks, Spectra, GenerateBlocks
- ✅ **Automatic Font Dropdown Integration** - Fonts appear directly in builder typography controls
- ✅ **Bulk ZIP Import** - Upload entire font families at once
- ✅ **Automatic Font Detection** - Family, weight, and style auto-detected from filenames
- ✅ **Priority Support** - Direct help from our team

Upgrade seamlessly - all fonts and settings are preserved!

## ❓ FAQ

### Is SafeFonts GDPR compliant?

Yes! SafeFonts hosts all fonts locally on your server at `wp-content/uploads/safefonts/`, so no data is sent to external services like Google Fonts. This makes it fully GDPR compliant.

### Which font format should I use?

WOFF2 is recommended for best compression and modern browser support. You can also upload WOFF as a fallback for older browsers.

### How many fonts can I upload?

There's no limit, but keep in mind that too many fonts can affect site performance. Only upload fonts you actually use.

### Can I use these fonts in page builders?

SafeFonts Free works with Gutenberg and provides CSS variables that can be manually used with any builder or theme supporting CSS custom properties (like Builderius, Bricks, Oxygen, and others). Simply use `var(--safefonts-font-slug)` in typography fields or custom CSS.

For automatic integration where fonts appear directly in builder dropdowns (14 integrations including Elementor, Bricks, Beaver Builder, Divi, Oxygen, Brizy, and more), upgrade to [SafeFonts Pro](https://safefonts.com).

### Where are fonts stored?

Fonts are stored in `wp-content/uploads/safefonts/` directory and registered in the WordPress database. This location survives plugin updates and reinstallation.

## 🐛 Bug Reports & Support

- **Support Forum:** [WordPress.org Support](https://wordpress.org/support/plugin/safefonts/)
- **Bug Reports:** [GitHub Issues](https://github.com/chrmrtns/safefonts/issues)
- **Website:** [safefonts.com](https://safefonts.com)

## 📝 Changelog

### 1.1.9
- NEW: CSS variables output for all fonts - enables manual font selection in custom CSS and builders supporting CSS custom properties
- NEW: CSS Reference page - comprehensive documentation for CSS variables, Gutenberg classes, and implementation details
- Fix: Auto-regeneration now works correctly for both single plugin updates and bulk updates
- Fix: CSS regeneration hook now properly detects updates from all update methods

### 1.1.2
- Fix: Fonts now display correctly in Gutenberg block editor
- NEW: Generate .has-{slug}-font-family CSS classes for Gutenberg typography controls
- NEW: Intelligent font fallback detection (serif, sans-serif, monospace, cursive)
- Fix: Database migration now properly adds family_slug column for users upgrading from v1.0.x
- Improved: Enhanced error handling for font uploads with detailed error messages
- Improved: Better directory writability checks before file operations

### 1.0.9
- Fixed: Removed manual load_plugin_textdomain() per WordPress.org guidelines
- Improved: Help & Documentation page now uses CSS tabs for better organization
- Improved: Cleaner tabbed interface without long scrolling page
- Added: Translation support with POT file (translatable strings ready)
- Added: Full internationalization (i18n) support for translators

[View full changelog](https://github.com/chrmrtns/safefonts/blob/main/readme.txt)

## 📄 License

SafeFonts is licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

## 👨‍💻 Author

**Chris Martens**
Website: [chris-martens.com](https://chris-martens.com)
Plugin: [safefonts.com](https://safefonts.com)

---

Made with ❤️ for the WordPress community
