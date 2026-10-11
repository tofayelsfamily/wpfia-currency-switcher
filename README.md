# WPFIA Instant Currency Switcher & Auto Geo-Detector for WooCommerce

[![WordPress](https://img.shields.io/badge/WordPress-6.0+-21759B?style=for-the-badge&logo=wordpress&logoColor=white)](https://wordpress.org)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-6.0+-96588A?style=for-the-badge&logo=woocommerce&logoColor=white)](https://woocommerce.com)
[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-GPLv2-green?style=for-the-badge)](LICENSE)
[![Author](https://img.shields.io/badge/Author-WPFIA-blue?style=for-the-badge)](https://wpfia.com)

**WPFIA Instant Currency Switcher** is an ultra-fast, lightweight, and modern multi-currency conversion plugin engineered specifically for high-conversion WooCommerce stores. Designed with zero external dependencies and local caching, it delivers instantaneous price conversions without slowing down store checkout.

---

## ✨ Key Features

- **⚡ Zero-Reload AJAX Switching:** Customers switch currencies instantaneously without tedious full-page reloads.
- **🌍 Auto Geo-Location Detector:** Automatically detects visitor country and presents prices in their local currency.
- **🍔 Navigation Menu Switcher:** Effortlessly integrates into your store's primary navigation menu with clean flags and symbols.
- **📌 Executive Topbar Strip:** Optional sleek topbar header strip for desktop & mobile viewports.
- **💱 Real-Time Exchange Rates:** Automatically fetches reliable rates with configurable manual fallback overrides.
- **🚀 Ultra-Lightweight & Clean:** Pure native WooCommerce integration, zero bloated libraries, and 100% PHP 8+ compatible.
- **🛡️ Reentrancy Guard Protection:** Hardened against infinite recursion loops on `get_woocommerce_currency`.

---

## 🚀 Installation

1. Download or clone this repository:
   ```bash
   git clone https://github.com/tofayelsfamily/wpfia-currency-switcher.git
   ```
2. Upload the `wpfia-currency-switcher` directory to `/wp-content/plugins/`.
3. Activate the plugin via **WordPress Admin > Plugins**.
4. Configure your preferred currencies, exchange rates, and switcher positions under **WooCommerce > Settings > Currency Switcher**.

---

## 🛠️ Architecture & Hooks

Developed following strict WordPress Plugin Check (PCP) and WordPress VIP coding standards:
- All options, transients, and functions strictly prefixed with `wpfia_cs_`.
- Nonce and capability verification on all AJAX handlers.
- Safe output escaping using `esc_html()`, `esc_attr()`, and `esc_url()`.

---

## 🏢 About WPFIA

Developed with precision by **[WPFIA Solutions](https://wpfia.com)**.  
Explore our complete suite of high-performance WordPress & WooCommerce tools at [wpfia.com](https://wpfia.com).

**License:** GNU General Public License v2 or later.
