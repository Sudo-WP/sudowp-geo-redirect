=== SudoWP Geo Redirect (Security Fork) ===
Contributors: SudoWP, WP Republic
Original Authors: Geolify
Tags: geo redirect, geolify, security-fork, patched, php8.2
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 2.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A security-hardened fork of the "Geo Redirect" plugin. Modernized for PHP 8.2 and patched against XSS/Referrer spoofing.

== Description ==

This is SudoWP Geo Redirect, a community-maintained and security-hardened fork of the original "Geo Redirect" plugin by Geolify.

**Why this fork?**
The original plugin relied on outdated procedural code, lacked PHP 8.2 compatibility (causing depreciation notices), and handled HTTP Referrers insecurely, creating potential XSS/Spoofing vectors.

**Original Plugin:** Geo Redirect by Geolify

**Security Patches & Improvements in SudoWP Edition:**
* **Security Hardening:** Implemented secure handling of `HTTP_REFERER` and input sanitization to prevent potential XSS attacks.
* **Modernization:** Full code refactor from procedural style to a clean Singleton Object-Oriented architecture.
* **PHP 8.2 Compliance:** Added Strict Typing (`declare(strict_types=1)`) and fixed all deprecated function calls.
* **Sanitization:** Enforced `sanitize_text_field` and `esc_url` on all inputs and outputs.

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

= 2.1.0 (SudoWP Edition) =
* Security Fix: Secured Referrer handling to prevent potential XSS vectors.
* Update: Full Refactor to PHP 8.2 standards (Strict Types).
* Update: Converted to Singleton Class structure.
* Maintenance: Rebranded as SudoWP Geo Redirect.