<?php
/**
 * WPFIA Currency Switcher - Admin Dashboard Template
 *
 * @package WPFIA_Currency_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap" style="max-width: 1040px; margin: 24px auto 40px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- Executive Brand Header -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; padding: 28px 32px; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <span style="background: #2563eb; color: #fff; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px; letter-spacing: 0.5px;">WPFIA PLUGIN STUDIO</span>
                <span style="background: #10b981; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">100% LIFETIME FREE</span>
                <span style="color: #94a3b8; font-size: 12px; font-weight: 600;">v<?php echo esc_html( WPFIA_CURRENCY_SWITCHER_VERSION ); ?></span>
            </div>
            <h1 style="color: #fff; margin: 0; font-size: 24px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <span>💱</span> <?php esc_html_e( 'Instant Currency Switcher & Auto Geo-Detector', 'wpfia-currency-switcher' ); ?>
            </h1>
            <p style="color: #94a3b8; margin: 6px 0 0 0; font-size: 13.5px;">
                <?php esc_html_e( 'Deliver native multi-currency pricing to global buyers with automatic IP location detection and live rates sync.', 'wpfia-currency-switcher' ); ?>
            </p>
        </div>

        <!-- Top Action Buttons (Always Visible at Top) -->
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <button type="submit" form="wpfia-cs-settings-form" style="background: #10b981; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(16,185,129,0.3); transition: all 0.2s;">
                <span>💾</span> <?php esc_html_e( 'Save Settings', 'wpfia-currency-switcher' ); ?>
            </button>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin: 0;">
                <input type="hidden" name="action" value="wpfia_cs_sync_rates">
                <?php wp_nonce_field( 'wpfia_cs_sync_nonce' ); ?>
                <button type="submit" style="background: #2563eb; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(37,99,235,0.3); transition: all 0.2s;">
                    <span>🔄</span> <?php esc_html_e( 'Sync Live Rates', 'wpfia-currency-switcher' ); ?>
                </button>
            </form>
        </div>
    </div>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible" style="border-left-color: #10b981; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px;">
            <p style="font-weight: 600; margin: 0;"><?php esc_html_e( 'Settings updated successfully.', 'wpfia-currency-switcher' ); ?></p>
        </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['sync_result'] ) ) : ?>
        <?php if ( 'synced' === $_GET['sync_result'] ) : ?>
            <div class="notice notice-success is-dismissible" style="border-left-color: #10b981; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px;">
                <p style="font-weight: 600; margin: 0;"><?php esc_html_e( '✓ Live currency rates updated successfully from European Central Bank & Open Exchange APIs.', 'wpfia-currency-switcher' ); ?></p>
            </div>
        <?php else : ?>
            <div class="notice notice-error is-dismissible" style="border-left-color: #ef4444; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px;">
                <p style="font-weight: 600; margin: 0;"><?php esc_html_e( 'Could not update rates automatically. Please verify your server internet connection or enter rates manually.', 'wpfia-currency-switcher' ); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Main Settings Form -->
    <form method="post" action="" id="wpfia-cs-settings-form">
        <?php wp_nonce_field( 'wpfia_cs_settings_nonce', 'wpfia_cs_nonce' ); ?>
        <input type="hidden" name="wpfia_cs_save_settings" value="1">

        <!-- Card 1: Core Conversion & Professional Placements -->
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 24px;">
            <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <span>🌐</span> <?php esc_html_e( 'General & Geo-Detection Controls', 'wpfia-currency-switcher' ); ?>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 24px;">
                <div>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px; color: #1e293b; cursor: pointer;">
                        <input type="checkbox" name="enabled" value="1" <?php checked( $all['enabled'], 1 ); ?>>
                        <?php esc_html_e( 'Enable Multi-Currency Converter', 'wpfia-currency-switcher' ); ?>
                    </label>
                    <p style="color: #64748b; font-size: 12px; margin: 4px 0 0 24px;">
                        <?php esc_html_e( 'Master toggle for all frontend pricing conversion and switcher UI.', 'wpfia-currency-switcher' ); ?>
                    </p>
                </div>

                <div>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px; color: #1e293b; cursor: pointer;">
                        <input type="checkbox" name="geo_detection" value="1" <?php checked( $all['geo_detection'], 1 ); ?>>
                        <?php esc_html_e( 'Automatic IP Geo-Detection', 'wpfia-currency-switcher' ); ?>
                    </label>
                    <p style="color: #64748b; font-size: 12px; margin: 4px 0 0 24px;">
                        <?php esc_html_e( 'Detects buyer country via Cloudflare headers and IP to auto-select native currency.', 'wpfia-currency-switcher' ); ?>
                    </p>
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 13.5px; color: #1e293b; display: block; margin-bottom: 6px;">
                        <?php esc_html_e( 'Psychological Price Rounding', 'wpfia-currency-switcher' ); ?>
                    </label>
                    <select name="rounding_type" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px;">
                        <option value="none" <?php selected( $all['rounding_type'], 'none' ); ?>><?php esc_html_e( 'No Rounding (Exact Conversion)', 'wpfia-currency-switcher' ); ?></option>
                        <option value=".99" <?php selected( $all['rounding_type'], '.99' ); ?>><?php esc_html_e( 'Round to .99 (e.g. $19.99)', 'wpfia-currency-switcher' ); ?></option>
                        <option value=".95" <?php selected( $all['rounding_type'], '.95' ); ?>><?php esc_html_e( 'Round to .95 (e.g. $19.95)', 'wpfia-currency-switcher' ); ?></option>
                        <option value="round" <?php selected( $all['rounding_type'], 'round' ); ?>><?php esc_html_e( 'Round to Nearest Whole Number', 'wpfia-currency-switcher' ); ?></option>
                        <option value="ceil" <?php selected( $all['rounding_type'], 'ceil' ); ?>><?php esc_html_e( 'Always Round Up (Ceil)', 'wpfia-currency-switcher' ); ?></option>
                    </select>
                </div>
            </div>

            <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 20px 0 12px 0; border-top: 1px solid #f1f5f9; padding-top: 16px; display: flex; align-items: center; gap: 8px;">
                <span>✨</span> <?php esc_html_e( 'Professional Placement & Display Locations', 'wpfia-currency-switcher' ); ?>
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
                <!-- Placement 1: Primary Header Menu -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13.5px; color: #1e293b; cursor: pointer;">
                        <input type="checkbox" name="menu_switcher" value="1" <?php checked( $all['menu_switcher'] ?? 1, 1 ); ?>>
                        <?php esc_html_e( 'Primary Header Menu Dropdown (Recommended)', 'wpfia-currency-switcher' ); ?>
                    </label>
                    <p style="color: #64748b; font-size: 12px; margin: 6px 0 0 24px;">
                        <?php esc_html_e( 'Appends a clean globe dropdown (🌐 USD ▾) directly into your site main navigation menu without touching code.', 'wpfia-currency-switcher' ); ?>
                    </p>
                </div>

                <!-- Placement 2: Site Topbar Header -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13.5px; color: #1e293b; cursor: pointer;">
                        <input type="checkbox" name="topbar_switcher" value="1" <?php checked( $all['topbar_switcher'] ?? 0, 1 ); ?>>
                        <?php esc_html_e( 'Site Topbar Header Strip', 'wpfia-currency-switcher' ); ?>
                    </label>
                    <p style="color: #64748b; font-size: 12px; margin: 6px 0 10px 24px;">
                        <?php esc_html_e( 'Renders an ultra-slim, luxury header bar across the top of your entire website.', 'wpfia-currency-switcher' ); ?>
                    </p>
                    <div style="margin-left: 24px; display: flex; flex-direction: column; gap: 8px;">
                        <div>
                            <label style="font-size: 11.5px; font-weight: 600; color: #475569; display: block; margin-bottom: 2px;">
                                <?php esc_html_e( 'Topbar Position:', 'wpfia-currency-switcher' ); ?>
                            </label>
                            <select name="topbar_position" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 12px;">
                                <option value="right" <?php selected( $all['topbar_position'] ?? 'right', 'right' ); ?>><?php esc_html_e( 'Top Right (Standard)', 'wpfia-currency-switcher' ); ?></option>
                                <option value="left" <?php selected( $all['topbar_position'] ?? 'right', 'left' ); ?>><?php esc_html_e( 'Top Left', 'wpfia-currency-switcher' ); ?></option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 11.5px; font-weight: 600; color: #475569; display: block; margin-bottom: 2px;">
                                <?php esc_html_e( 'Optional Announcement Message:', 'wpfia-currency-switcher' ); ?>
                            </label>
                            <input type="text" name="topbar_text" value="<?php echo esc_attr( $all['topbar_text'] ?? '' ); ?>" placeholder="e.g. Free Worldwide Express Shipping on orders over $50" style="width: 100%; font-size: 12px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 4px 8px;">
                        </div>
                    </div>
                </div>

                <!-- Placement 3: Floating Corner Button -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13.5px; color: #1e293b; cursor: pointer;">
                        <input type="checkbox" name="floating_switcher" value="1" <?php checked( $all['floating_switcher'], 1 ); ?>>
                        <?php esc_html_e( 'Floating Screen Corner Pill', 'wpfia-currency-switcher' ); ?>
                    </label>
                    <p style="color: #64748b; font-size: 12px; margin: 6px 0 10px 24px;">
                        <?php esc_html_e( 'Sticky dark pill fixed to screen corner for instant mobile and desktop switching.', 'wpfia-currency-switcher' ); ?>
                    </p>
                    <div style="margin-left: 24px;">
                        <label style="font-size: 11.5px; font-weight: 600; color: #475569; display: block; margin-bottom: 2px;">
                            <?php esc_html_e( 'Corner Position:', 'wpfia-currency-switcher' ); ?>
                        </label>
                        <select name="floating_position" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 12px;">
                            <option value="bottom-right" <?php selected( $all['floating_position'], 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right (Standard)', 'wpfia-currency-switcher' ); ?></option>
                            <option value="bottom-left" <?php selected( $all['floating_position'], 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'wpfia-currency-switcher' ); ?></option>
                            <option value="top-right" <?php selected( $all['floating_position'], 'top-right' ); ?>><?php esc_html_e( 'Top Right', 'wpfia-currency-switcher' ); ?></option>
                            <option value="top-left" <?php selected( $all['floating_position'], 'top-left' ); ?>><?php esc_html_e( 'Top Left', 'wpfia-currency-switcher' ); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Configured Currencies & Exchange Rates Table -->
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 16px;">
                <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span>📊</span> <?php esc_html_e( 'Currency Rates & Decimal Rules', 'wpfia-currency-switcher' ); ?>
                </h2>
                <div style="font-size: 12px; color: #64748b;">
                    <?php esc_html_e( 'Base WooCommerce Store Currency:', 'wpfia-currency-switcher' ); ?> <strong><?php echo esc_html( $base_currency ); ?> (1.000)</strong>
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped" id="wpfia-cs-currencies-table" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                <thead>
                    <tr>
                        <th style="width: 70px; font-weight: 700;"><?php esc_html_e( 'Active', 'wpfia-currency-switcher' ); ?></th>
                        <th style="width: 100px; font-weight: 700;"><?php esc_html_e( 'Currency', 'wpfia-currency-switcher' ); ?></th>
                        <th style="width: 90px; font-weight: 700;"><?php esc_html_e( 'Symbol', 'wpfia-currency-switcher' ); ?></th>
                        <th style="font-weight: 700;"><?php esc_html_e( 'Exchange Rate (vs Base)', 'wpfia-currency-switcher' ); ?></th>
                        <th style="width: 90px; font-weight: 700;"><?php esc_html_e( 'Decimals', 'wpfia-currency-switcher' ); ?></th>
                        <th style="width: 60px; font-weight: 700;"><?php esc_html_e( 'Action', 'wpfia-currency-switcher' ); ?></th>
                    </tr>
                </thead>
                <tbody id="wpfia-cs-table-body">
                    <?php foreach ( $currencies as $code => $c_data ) : ?>
                        <tr id="wpfia-row-<?php echo esc_attr( $code ); ?>">
                            <td>
                                <input type="checkbox" name="currencies[<?php echo esc_attr( $code ); ?>][active]" value="1" <?php checked( ! empty( $c_data['active'] ) ); ?>>
                            </td>
                            <td>
                                <strong><?php echo esc_html( $code ); ?></strong>
                                <?php if ( $code === $base_currency ) : ?>
                                    <span style="font-size: 10px; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-weight: 700; margin-left: 4px;">BASE</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="text" name="currencies[<?php echo esc_attr( $code ); ?>][symbol]" value="<?php echo esc_attr( $c_data['symbol'] ?? '$' ); ?>" style="width: 60px; text-align: center;">
                            </td>
                            <td>
                                <input type="number" step="0.000001" min="0" name="currencies[<?php echo esc_attr( $code ); ?>][rate]" value="<?php echo esc_attr( $c_data['rate'] ?? 1.0 ); ?>" style="width: 130px;" <?php readonly( $code === $base_currency ); ?>>
                                <span style="font-size: 11px; color: #94a3b8; margin-left: 6px;">1 <?php echo esc_html( $base_currency ); ?> = <?php echo esc_html( $c_data['rate'] ?? 1.0 ); ?> <?php echo esc_html( $code ); ?></span>
                            </td>
                            <td>
                                <input type="number" min="0" max="4" name="currencies[<?php echo esc_attr( $code ); ?>][decimals]" value="<?php echo esc_attr( $c_data['decimals'] ?? 2 ); ?>" style="width: 60px;">
                            </td>
                            <td>
                                <?php if ( $code !== $base_currency ) : ?>
                                    <button type="button" class="button" style="color: #ef4444; padding: 0 6px;" onclick="document.getElementById('wpfia-row-<?php echo esc_attr( $code ); ?>').remove();" title="<?php esc_attr_e( 'Remove Currency', 'wpfia-currency-switcher' ); ?>">✕</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Add New Currency Row Bar -->
            <div style="margin-top: 16px; padding: 12px 16px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="font-weight: 700; font-size: 13px; color: #334155;">➕ <?php esc_html_e( 'Add Another Currency:', 'wpfia-currency-switcher' ); ?></span>
                <input type="text" id="wpfia-new-code" placeholder="e.g. CAD, AUD, JPY" maxlength="3" style="width: 120px; text-transform: uppercase; font-weight: 700; border-radius: 6px; border: 1px solid #cbd5e1; padding: 4px 8px;">
                <input type="text" id="wpfia-new-symbol" placeholder="Symbol (e.g. $)" maxlength="4" style="width: 100px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 4px 8px;">
                <input type="number" id="wpfia-new-rate" placeholder="Rate (vs Base)" step="0.0001" style="width: 110px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 4px 8px;">
                <button type="button" class="button" id="wpfia-btn-add-currency" style="background: #0f172a; color: #fff; border: none; font-weight: 700; border-radius: 6px; padding: 4px 12px; cursor: pointer;">
                    <?php esc_html_e( '+ Add to Table', 'wpfia-currency-switcher' ); ?>
                </button>
                <span style="font-size: 11px; color: #64748b;"><?php esc_html_e( '(Click "+ Add to Table", then click Save Settings to keep)', 'wpfia-currency-switcher' ); ?></span>
            </div>

            <p style="font-size: 11.5px; color: #64748b; margin: 12px 0 0 0;">
                <?php esc_html_e( 'Tip: Want to display the currency switcher inside your header menu, sidebar or Gutenberg block? Use shortcode:', 'wpfia-currency-switcher' ); ?>
                <code style="background: #f1f5f9; padding: 3px 6px; border-radius: 4px; font-weight: 700;">[wpfia_currency_switcher]</code>
            </p>
        </div>

        <!-- Card 3: Promotion / WPFIA Ecosystem Hub -->
        <div style="background: #0f172a; border: 1px solid #1e293b; border-radius: 12px; padding: 20px 24px; color: #cbd5e1; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 24px;">
            <div>
                <div style="font-weight: 700; color: #60a5fa; font-size: 13px; margin-bottom: 2px;">⚡ <?php esc_html_e( 'Looking to scale your WooCommerce store conversions?', 'wpfia-currency-switcher' ); ?></div>
                <div style="font-size: 12px; color: #94a3b8;"><?php esc_html_e( 'Discover high-converting tools engineered by WPFIA: QuickCart, SellBot Sales Assistant, Safe SVG, and QuickInvoice.', 'wpfia-currency-switcher' ); ?></div>
            </div>
            <a href="https://wpfia.com/plugins" target="_blank" rel="noopener noreferrer" style="background: #1e293b; color: #38bdf8; border: 1px solid #0284c7; padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">
                <?php esc_html_e( 'Explore WPFIA Suite', 'wpfia-currency-switcher' ); ?> →
            </a>
        </div>

        <!-- Bottom Save Button -->
        <div style="display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div>
                <span style="font-weight: 700; font-size: 14px; color: #0f172a;"><?php esc_html_e( 'Ready to apply currency changes?', 'wpfia-currency-switcher' ); ?></span>
                <p style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;"><?php esc_html_e( 'All changes will take effect immediately across your WooCommerce store.', 'wpfia-currency-switcher' ); ?></p>
            </div>
            <button type="submit" class="button button-primary" style="background: #10b981; border-color: #059669; font-size: 14.5px; padding: 10px 30px; height: auto; font-weight: 800; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.3); cursor: pointer;">
                💾 <?php esc_html_e( 'Save All Settings Now', 'wpfia-currency-switcher' ); ?>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btnAdd = document.getElementById('wpfia-btn-add-currency');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            var codeInput = document.getElementById('wpfia-new-code');
            var symInput  = document.getElementById('wpfia-new-symbol');
            var rateInput = document.getElementById('wpfia-new-rate');

            var code = (codeInput.value || '').trim().toUpperCase();
            var sym  = (symInput.value || '$').trim();
            var rate = parseFloat(rateInput.value || 1.0);

            if (code.length !== 3) {
                alert('Please enter a valid 3-letter currency ISO code (e.g. CAD, AUD, JPY).');
                return;
            }

            if (document.getElementById('wpfia-row-' + code)) {
                alert('Currency ' + code + ' already exists in the table.');
                return;
            }

            var tbody = document.getElementById('wpfia-cs-table-body');
            var tr = document.createElement('tr');
            tr.id = 'wpfia-row-' + code;

            tr.innerHTML = '<td><input type="checkbox" name="currencies[' + code + '][active]" value="1" checked></td>' +
                '<td><strong>' + code + '</strong></td>' +
                '<td><input type="text" name="currencies[' + code + '][symbol]" value="' + sym + '" style="width: 60px; text-align: center;"></td>' +
                '<td><input type="number" step="0.000001" min="0" name="currencies[' + code + '][rate]" value="' + rate + '" style="width: 130px;"></td>' +
                '<td><input type="number" min="0" max="4" name="currencies[' + code + '][decimals]" value="2" style="width: 60px;"></td>' +
                '<td><button type="button" class="button" style="color: #ef4444; padding: 0 6px;" onclick="document.getElementById(\'wpfia-row-' + code + '\').remove();">✕</button></td>';

            tbody.appendChild(tr);

            codeInput.value = '';
            symInput.value  = '';
            rateInput.value = '';

            alert('Added ' + code + '! Click "Save Settings" at top or bottom to store changes.');
        });
    }
});
</script>
