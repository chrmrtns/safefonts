=== SafeFonts ===
Contributors: chrmrtns
Tags: fonts, google fonts, custom fonts, typography, gutenberg
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Secure local font hosting for WordPress with Gutenberg integration and GDPR compliance. Upload custom fonts with advanced security validation.

== Description ==

**SafeFonts provides secure, GDPR-compliant font management for WordPress with seamless Gutenberg integration.**

If you need to host custom fonts locally on your WordPress site for performance, privacy, or GDPR compliance, SafeFonts makes it simple with industry-leading security validation and automatic integration with the WordPress block editor.

= Why Choose SafeFonts? =

**🔒 Security-First Approach**
* Magic byte validation for all font files
* MIME type verification
* File hash checking
* Configurable file size limits
* Protection against malicious uploads

**⚡ Fast & Lightweight**
* Custom database storage for instant queries
* No impact on page load speed
* Optimized font delivery
* Works with any theme or page builder

**✅ Gutenberg Integration**
* Automatic integration with block editor typography
* WordPress 6.5+ Font Library support
* Works with all blocks that support typography
* No configuration needed

**🎯 Simple Upload Process**
* Individual font file uploads (.woff2, .woff, .ttf, .otf)
* Specify font family, weight, and style
* Visual font previews in admin
* Drag-and-drop ready interface

**⚡ Performance Optimization**
* Font preloading support for faster page loads
* Automatic preload tag generation: `<link rel="preload" href="..." as="font">`
* User-selectable fonts for preloading (Settings tab)
* Best practice: Preload 1-2 critical fonts only
* Reduces flash of invisible text (FOIT)
* Improves Core Web Vitals scores

**🌍 GDPR Compliant**
* 100% local font hosting
* No external requests to Google or other CDNs
* Complete data privacy
* EU-ready out of the box

= Perfect For =

* Privacy-conscious websites requiring GDPR compliance
* Sites that need custom or premium fonts
* Agencies managing multiple client sites
* Anyone wanting better control over typography
* Performance-optimized websites

= How It Works =

1. **Upload Fonts**: Go to SafeFonts menu and upload your font files
2. **Configure Details**: Set font family name, weight (100-900), and style (normal/italic)
3. **Use in Gutenberg**: Your fonts automatically appear in the block editor
4. **That's It!** Fonts are served locally with optimal performance

= Supported Font Formats =

* **WOFF2** (recommended - best compression)
* **WOFF** (broad browser support)
* **TTF** (TrueType fonts)
* **OTF** (OpenType fonts)

= WordPress 6.5+ Font Library =

If you're using WordPress 6.5 or higher, SafeFonts automatically integrates with the native Font Library in the Site Editor, giving you a unified font management experience.

== Installation ==

= Automatic Installation (Recommended) =

1. Go to **Plugins > Add New** in your WordPress admin
2. Search for "SafeFonts"
3. Click **Install Now**, then **Activate**
4. Go to **SafeFonts** in the admin menu to start uploading fonts

= Manual Installation =

1. Download the plugin zip file
2. Go to **Plugins > Add New > Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Click **Activate Plugin**
5. Go to **SafeFonts** in the admin menu

= First Steps After Installation =

1. **Upload Your First Font**: Go to SafeFonts > Upload tab
2. **Fill in Font Details**: Enter font family name, select weight and style
3. **Upload File**: Choose your font file (.woff2 recommended)
4. **Use in Gutenberg**: Your font now appears in block typography settings

== Frequently Asked Questions ==

= What font formats are supported? =

SafeFonts supports WOFF2, WOFF, TTF, and OTF font files. We recommend WOFF2 for the best compression and performance.

= Is this GDPR compliant? =

Yes! SafeFonts stores all fonts locally on your server, so no data is sent to external services like Google Fonts. This makes it 100% GDPR compliant.

= Does this work with Gutenberg? =

Absolutely! Uploaded fonts automatically appear in:
* All block typography settings
* Paragraph and heading blocks
* Any block with font family support
* WordPress 6.5+ Font Library (if available)

= Can I use this with page builders? =

The free version works with Gutenberg. For page builder integration (Elementor, Bricks, Beaver Builder, Divi, Oxygen), check out SafeFonts Pro at https://safefonts.com

= How does the security validation work? =

SafeFonts performs multiple security checks:
* File extension validation
* MIME type verification
* Magic byte signature checking
* File size limits
* SHA-256 hash generation for integrity

= Will this slow down my site? =

No! SafeFonts uses a custom database table (not WordPress post meta) for lightning-fast queries. Fonts are served as static CSS files with no JavaScript overhead.

= Can I upload Google Fonts? =

Yes! Download the font files from Google Fonts, then upload them individually through SafeFonts. For bulk imports from our Google Fonts Downloader tool, check out SafeFonts Pro.

= What's the difference between SafeFonts Free and Pro? =

SafeFonts Free includes:
* Individual font file uploads
* Gutenberg integration
* Security validation
* WordPress 6.5+ Font Library support

SafeFonts Pro adds:
* Page builder integration (Elementor, Bricks, Beaver Builder, Divi, Oxygen)
* Bulk ZIP package imports from Google Fonts Downloader
* Professional admin interface with drag & drop
* REST API endpoints
* Advanced font management tools

Learn more at https://safefonts.com

= Where are the font files stored? =

Font files are stored in `/wp-content/uploads/safefonts/` organized by font family (e.g., `/roboto/`, `/open-sans/`) with proper security protection. Font metadata is stored in a custom database table for fast retrieval.

= Can I delete fonts? =

Yes! Each font has a delete button in the SafeFonts admin interface. Deleting a font removes both the file and database entry.

== Screenshots ==

1. SafeFonts admin interface - Upload and manage fonts
2. Font upload form with security validation
3. Installed fonts list with previews
4. Settings page for file size and type configuration
5. System information and WordPress 6.5+ compatibility check
6. Fonts automatically available in Gutenberg typography settings

== Changelog ==

= 1.1.0 =
* NEW: Font family folder organization - fonts now stored in dedicated family folders (e.g., /roboto/, /open-sans/)
* NEW: Added family_slug column to database for faster family-based queries
* NEW: Automatic migration on plugin update - existing fonts moved to family folders seamlessly
* NEW: Automatic cleanup of empty family folders when last font is deleted
* IMPROVED: Admin UI - Font weight badge now uses yellow color instead of red to avoid confusion with delete button
* IMPROVED: File path handling - relative paths now include family folder structure
* Technical: Database schema version bump for migration support

= 1.0.9 =
* Fixed: Removed manual load_plugin_textdomain() - WordPress.org handles translations automatically
* Improved: Help & Documentation page now uses CSS tabs for better organization and navigation
* Improved: Cleaner tabbed interface without long scrolling page
* Added: Translation support with POT file (translatable strings ready)
* Added: Full internationalization (i18n) support for translators

= 1.0.8 =
* Added: Gutenberg block editor integration - uploaded fonts now appear in font picker dropdown
* Added: register_fonts_with_editor() method to inject fonts into editor settings
* Improved: Fonts automatically available in typography controls for all blocks
* Technical: Uses block_editor_settings_all filter to register custom font families

= 1.0.7 =
* Improved: Keyboard accessibility - Upload button now fully accessible via Tab navigation
* Added: ARIA labels for better screen reader support on upload and delete buttons
* Added: Explicit tabindex on upload submit button for consistent keyboard navigation
* Added: Enter key handler for upload button to ensure keyboard submission works
* Improved: Delete buttons now have descriptive ARIA labels including font family name

= 1.0.6 =
* Fixed: Critical - File extension detection now uses original filename instead of temporary upload path
* Fixed: Font uploads were failing because temp files (like /tmp/phpXXXXXX) have no extension
* Improved: validate_font_file() now accepts original filename parameter for accurate validation
* Improved: import_single_font() method signature updated to handle original filenames

= 1.0.5 =
* Fixed: MIME type validation now accepts all common font MIME type variations
* Fixed: Corrupted or empty allowed_types settings are now automatically repaired
* Improved: Better error messages that specify exact validation failure (extension or MIME type)
* Improved: MIME type arrays now include application/octet-stream for broader compatibility
* Added: Automatic detection and repair of misconfigured settings

= 1.0.4 =
* Fixed: Critical - Font upload directory (assets/fonts/) was not being created during refactoring
* Fixed: Incorrect CSS file paths pointing to assets/fonts/css/ instead of assets/css/
* Fixed: Font uploads now work correctly after plugin activation
* Added: Security files (.htaccess, index.php) to fonts directory
* Improved: Directory creation logic in activation hook

= 1.0.3 =
* Fixed: Database table name now includes vendor prefix (chrmrtns_safefonts) to prevent conflicts
* Fixed: Database table not being created on plugin activation
* Improved: Better namespace consistency throughout codebase
* Updated: Developer documentation with correct table name

= 1.0.2 =
* Fixed: WordPress 6.5+ Font Library validation error when no fonts installed
* Added: Extra safety check before registering empty font collection
* Improved: Better error handling for edge cases

= 1.0.1 =
* Added: Top-level admin menu (moved from Settings submenu)
* Added: Link to SafeFonts.com in upgrade notice
* Added: MIME type filter for font uploads
* Fixed: Font upload validation issues
* Improved: Better admin menu organization for future expansion

= 1.0.0 =
* Initial release
* Individual font file uploads (woff2, woff, ttf, otf)
* Security-focused validation (magic bytes, MIME types, file hashing)
* Custom database storage for performance
* Automatic Gutenberg integration
* WordPress 6.5+ Font Library support
* Font preview in admin interface
* Configurable file size limits
* Multiple font weight and style support

== Upgrade Notice ==

= 1.1.0 =
Font organization enhancement: Fonts are now organized in family-specific folders for better management. Automatic migration included - your existing fonts will be seamlessly moved to the new structure on update. UI improvement with clearer badge colors.

= 1.0.9 =
Important update: Font storage moved to wp-content/uploads/safefonts/ for WordPress best practices. All existing fonts will be automatically migrated on activation. Update recommended for better compatibility and font persistence.

= 1.0.8 =
Feature enhancement: Uploaded fonts now automatically appear in Gutenberg font picker. Makes custom fonts instantly usable in all blocks with typography controls.

= 1.0.7 =
Accessibility improvement: Enhanced keyboard navigation and screen reader support for better WCAG compliance.

= 1.0.6 =
Critical fix: Font uploads now work correctly! Fixed extension detection issue that was preventing all uploads. Required update for all users.

= 1.0.5 =
Important fix: Resolves MIME type validation errors during font uploads. Includes better error messages to diagnose upload issues. Recommended for all users experiencing upload problems.

= 1.0.4 =
Critical fix: Resolves font upload failures caused by missing directory structure. All users should update immediately and deactivate/reactivate to create proper directories.

= 1.0.3 =
Critical fix: Updates database table name to prevent conflicts with other plugins. Required for all users - please deactivate and reactivate the plugin after updating to create the new table.

= 1.0.2 =
Bug fix release: Fixes WordPress 6.5+ Font Library validation errors on fresh installs. Recommended for all users.

= 1.0.1 =
Adds MIME type support for font uploads and improves admin interface. Recommended update for all users.

= 1.0.0 =
Initial release of SafeFonts - Secure Font Manager for WordPress.

== Developer Documentation ==

= Template Functions =

SafeFonts provides helper functions for developers:

`safefonts()` - Get the main plugin instance
`safefonts()->font_manager->get_fonts()` - Get all fonts from database
`safefonts()->font_manager->get_fonts_by_family()` - Get fonts grouped by family

= Hooks & Filters =

**Filters:**
* `upload_mimes` - SafeFonts adds font MIME types automatically

= Database Structure =

**Table:** `wp_chrmrtns_safefonts`
* `id` - Font ID
* `font_family` - Font family name
* `family_slug` - Sanitized family slug for folder names (v1.1.0+)
* `font_style` - normal or italic
* `font_weight` - 100-900
* `file_path` - Relative path to font file (includes family folder v1.1.0+)
* `file_hash` - SHA-256 hash for integrity
* `file_size` - File size in bytes
* `mime_type` - Validated MIME type
* `created_at` - Upload timestamp
* `updated_at` - Last update timestamp

= Architecture =

SafeFonts uses modern PHP namespaces and PSR-4 autoloading:
* `Chrmrtns\SafeFonts\Core` - Main plugin class
* `Chrmrtns\SafeFonts\FontManager` - Font validation and management
* `Chrmrtns\SafeFonts\Admin\AdminInterface` - Admin UI

== Support ==

For support, feature requests, or bug reports, please visit:
* GitHub: https://github.com/chrmrtns/safefonts
* Website: https://safefonts.com

== Privacy Policy ==

SafeFonts does not collect, store, or transmit any personal data. All font files are stored locally on your WordPress installation. No data is sent to external services.
