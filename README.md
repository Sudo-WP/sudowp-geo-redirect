# SudoWP Geo Redirect (Security Fork)

**Contributors:** SudoWP, WP Republic  
**Original Authors:** Geolify  
**Tags:** geo redirect, security, patched, php8.2  
**Requires at least:** 5.8  
**Tested up to:** 6.7  
**Stable tag:** 2.1.0  
**License:** GPLv2 or later  

## Security Notice
This is a **security-hardened fork** of the "Geo Redirect" plugin. It addresses legacy code issues, PHP 8.2 incompatibility, and potential security weaknesses in header handling.

---

## Description

**SudoWP Geo Redirect** modernizes the original Geolify integration, making it secure, fast, and compatible with modern WordPress environments.

### Security Patches & Improvements
We have conducted a full code audit and applied the following fixes:

1.  **Security Hardening (XSS/Spoofing):**
    * The original code handled `HTTP_REFERER` loosely. We implemented strict sanitization (`esc_url_raw`, `urlencode`) before passing it to the JavaScript redirector.
    * All Admin inputs are now strictly sanitized using `sanitize_text_field`.

2.  **PHP 8.2 Modernization:**
    * **Strict Typing:** Implemented `declare(strict_types=1);` across the codebase.
    * **Architecture:** Refactored the entire plugin from a flat procedural file to a robust **Singleton Class** structure to prevent global namespace pollution.

3.  **Performance:**
    * Optimized the `wp_enqueue_script` logic to load assets only when necessary.

## Installation

1.  Download the repository.
2.  **Important:** Deactivate and delete the original "Geo Redirect" plugin if installed.
3.  Upload the `sudowp-geo-redirect` folder to your `/wp-content/plugins/` directory.
4.  Activate the plugin.
5.  Navigate to **Settings > Geo Redirect** and enter your IDs.

## Changelog

### Version 2.1.0 (SudoWP Edition)
* **Security Fix:** Patched potential XSS vectors in referrer handling.
* **Update:** Complete refactor to PHP 8.2 standards.
* **Fix:** Replaced procedural code with SudoWP Singleton pattern.
* **Rebrand:** Forked as SudoWP Geo Redirect.

---
*Maintained by the SudoWP Security Project.*
