=== SudoWP Geo Redirect (Security Fork) ===
Contributors: SudoWP, WP Republic
Original Authors: Geolify
Tags: geo redirect, geolify, security-fork, patched, php8.2
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 2.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A security-hardened fork of the "Geo Redirect" plugin. Modernized for PHP 8.2 and patched against XSS/Referrer spoofing.

== Description ==

This is SudoWP Geo Redirect, a community-maintained and security-hardened fork of the original "Geo Redirect" plugin by Geolify.

**Why this fork?**
The original plugin relied on outdated procedural code, lacked PHP 8.2 compatibility (causing depreciation notices), and handled HTTP Referrers insecurely, creating potential XSS/Spoofing vectors.

**Original Plugin:** Geo Redirect by Geolify

**Security Patches & Improvements in SudoWP Edition:**
* **Enhanced Authorization (OWASP A01):** Capability checks in sanitize_settings callback and proper permission denial with wp_die().
* **Strict Input Validation (OWASP A03):** Implemented ctype_digit() validation for IDs to prevent type juggling vulnerabilities.
* **Output Escaping:** All URLs properly escaped with esc_url() and admin inputs sanitized using sanitize_text_field().
* **HTTP_REFERER Security:** Enhanced validation with filter_var() and FILTER_VALIDATE_URL before sanitization.
* **CSRF Protection:** WordPress nonce verification via settings_fields() with proper type definitions.
* **PHP 8.2 Compliance:** Strict Typing (declare(strict_types=1)) and modern Singleton Class architecture.
* **User Feedback:** Comprehensive input validation with detailed error messages for invalid entries.
* **Performance:** External scripts load with defer strategy for improved page performance.

**Key Features Preserved:**
* Redirect visitors based on Country, State, City, or IP.
* V1 and V2 Redirect support (Geolify API).
* Option to pass query strings.
* Lightweight script execution.

== Installation ==

1. Upload the `sudowp-geo-redirect` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Settings > Geo Redirect** in your WordPress admin.
4. Enter your Geo Redirect IDs provided by your Geolify dashboard.

== Frequently Asked Questions ==

= Is this compatible with the original Geo Redirect? =
Yes, it functions as a drop-in replacement but with a cleaner codebase. You should replace the old plugin entirely.

= Do I still need a Geolify account? =
Yes. This plugin is the WordPress integration connector. The redirection logic (IDs) is still managed via the Geolify dashboard.

== Changelog ==

= 2.1.1 (Security Hardening Update) =
* Security Enhancement: Added capability checks in sanitize_settings callback.
* Security Enhancement: Implemented strict ID validation using ctype_digit().
* Security Enhancement: Enhanced HTTP_REFERER validation with filter_var().
* Security Enhancement: Added comprehensive input validation with user feedback.
* Security Enhancement: Improved output escaping for all URLs and admin forms.
* Security Enhancement: Used absint() for integer sanitization.
* Security Enhancement: Added proper permission denial with wp_die().
* Improvement: Added field ID attributes for better accessibility.
* Improvement: External scripts now load with defer strategy.
* Improvement: Consistent URL building with add_query_arg().
* Security Audit: No vulnerabilities found by CodeQL scanner.
* Compliance: Addresses OWASP Top 10 2021 security requirements.

= 2.1.0 (SudoWP Edition) =
* Security Fix: Secured Referrer handling to prevent potential XSS vectors.
* Update: Full Refactor to PHP 8.2 standards (Strict Types).
* Update: Converted to Singleton Class structure.
* Maintenance: Rebranded as SudoWP Geo Redirect.