# SudoWP Geo Redirect (Security Fork)

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.2-777bb4.svg)
![License](https://img.shields.io/badge/License-GPLv2%2B-blue.svg)
![Status](https://img.shields.io/badge/Status-Security%20Hardened-green.svg)

**Contributors:** SudoWP, WP Republic  
**Original Authors:** Geolify  
**Tags:** geo redirect, security, patched, php8.2  
**Requires at least:** 5.8  
**Tested up to:** 6.7  
**Stable tag:** 2.1.1  
**License:** GPLv2 or later  

## Security Notice
This is a **security-hardened fork** of the "Geo Redirect" plugin. It addresses legacy code issues, PHP 8.2 incompatibility, and potential security weaknesses in header handling.

---

## Description

**SudoWP Geo Redirect** modernizes the original Geolify integration, making it secure, fast, and compatible with modern WordPress environments.

### Security Patches & Improvements
We have conducted a comprehensive security audit following OWASP best practices and applied the following fixes:

1.  **Enhanced Authorization & Access Control (OWASP A01:2021):**
    * Added capability checks in the `sanitize_settings` callback to prevent unauthorized modifications.
    * Implemented proper permission denial with `wp_die()` returning 403 status.
    * All admin functions verify `manage_options` capability before execution.

2.  **Strict Input Validation & Injection Prevention (OWASP A03:2021):**
    * Implemented `ctype_digit()` validation for IDs instead of `is_numeric()` to prevent type juggling vulnerabilities.
    * Added comprehensive validation with user feedback for invalid inputs.
    * All IDs are sanitized using `absint()` before use in URLs.
    * HTTP_REFERER validation enhanced with `filter_var()` before sanitization.

3.  **Output Escaping & XSS Prevention:**
    * Strict sanitization of HTTP_REFERER using `esc_url_raw()` and `filter_var()` with `FILTER_VALIDATE_URL`.
    * All URLs properly escaped with `esc_url()` before output.
    * Admin inputs sanitized using `sanitize_text_field()` and `esc_attr()`.
    * Used `rawurlencode()` for URL parameters to prevent encoding issues.

4.  **CSRF Protection:**
    * WordPress nonce verification handled automatically by `settings_fields()`.
    * Settings registration includes proper type and default value definitions.

5.  **PHP 8.2 Modernization:**
    * **Strict Typing:** Implemented `declare(strict_types=1);` across the codebase.
    * **Architecture:** Refactored from procedural to robust **Singleton Class** structure.
    * **Type Safety:** All method signatures include proper type hints.

6.  **Performance & Security:**
    * External scripts load with `defer` strategy to improve page load time.
    * Optimized `wp_enqueue_script` logic to load assets only when necessary.
    * Consistent use of `add_query_arg()` for URL construction.

## Installation

1.  Download the repository.
2.  **Important:** Deactivate and delete the original "Geo Redirect" plugin if installed.
3.  Upload the `sudowp-geo-redirect` folder to your `/wp-content/plugins/` directory.
4.  Activate the plugin.
5.  Navigate to **Settings > Geo Redirect** and enter your IDs.

## Changelog

### Version 2.1.1 (Security Hardening Update)
* **Security Enhancement:** Added capability checks in sanitize_settings callback.
* **Security Enhancement:** Implemented strict ID validation using ctype_digit().
* **Security Enhancement:** Enhanced HTTP_REFERER validation with filter_var().
* **Security Enhancement:** Added comprehensive input validation with user feedback.
* **Security Enhancement:** Improved output escaping for all URLs and admin forms.
* **Security Enhancement:** Used absint() for integer sanitization.
* **Security Enhancement:** Added proper permission denial with wp_die().
* **Improvement:** Added field ID attributes for better accessibility.
* **Improvement:** External scripts now load with defer strategy.
* **Improvement:** Consistent URL building with add_query_arg().
* **Security Audit:** No vulnerabilities found by CodeQL scanner.
* **Compliance:** Addresses OWASP Top 10 2021 security requirements.

### Version 2.1.0 (SudoWP Edition)
* **Security Fix:** Patched potential XSS vectors in referrer handling.
* **Update:** Complete refactor to PHP 8.2 standards.
* **Fix:** Replaced procedural code with SudoWP Singleton pattern.
* **Rebrand:** Forked as SudoWP Geo Redirect.

---
*Maintained by the SudoWP Security Project.*
