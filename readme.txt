=== WPFIA Currency Switcher & Auto Geo-Detector ===
Contributors: tofayelamin
Donate link: https://wpfia.com/
Tags: woocommerce, currency switcher, multi currency, geo currency, currency converter
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ultra-lightweight multi-currency converter and automatic IP-based currency detector for WooCommerce. 100% Lifetime Free.

== Description ==

**WPFIA Currency Switcher & Auto Geo-Detector** is an ultra-fast, zero-bloat multi-currency converter engineered specifically for global WooCommerce stores.

Unlike traditional currency plugins that lock basic currencies behind expensive paywalls or slow down your checkout with heavy database calls, WPFIA Currency Switcher is **100% Lifetime Free with zero locks and zero limits.**

= Key Features =
* **Automatic Geo-IP Currency Detection:** Instantly detects visitor location using Cloudflare HTTP headers and ultra-fast IP lookup to display prices in their home currency.
* **Live Exchange Rates Auto-Sync:** Fetches accurate, real-time conversion rates directly from European Central Bank and open financial APIs.
* **Floating Switcher Pill:** Sleek, modern floating selector in the corner of your store allowing buyers to switch currencies in 1 click.
* **Shortcode Support:** Place `[wpfia_currency_switcher]` anywhere in your header menus, widget sidebars, or Gutenberg blocks.
* **Psychological Pricing Rounding:** Convert prices cleanly to `.99` or `.95` (e.g. $19.99 instead of $19.73).
* **WooCommerce HPOS & Blocks Ready:** Full compatibility with High-Performance Order Storage (HPOS) and WooCommerce Cart & Checkout Blocks.
* **100% Lifetime Free:** No premium edition, no subscription nag screens, no artificial restrictions.

== Installation ==

1. Upload the `wpfia-currency-switcher` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **WooCommerce > Currency Switcher** to configure your currencies and exchange rates.

== Frequently Asked Questions ==

= Does this plugin require an API key for live exchange rates? =
No. The plugin connects directly to reliable open exchange rate providers (including Frankfurter API backed by the European Central Bank) without requiring any API keys or paid accounts.

= Can I use a shortcode to place the switcher in my menu? =
Yes! Simply insert `[wpfia_currency_switcher]` in any page, widget, or template.

= Does this plugin support WooCommerce HPOS? =
Yes, full High-Performance Order Storage (HPOS) compatibility is declared and verified.

== Changelog ==

= 1.0.0 =
* Initial public release on WordPress.org.
* Auto Geo-IP detection and European Central Bank live rates sync.
* Floating switcher pill and shortcode support.
