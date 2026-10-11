<?php
/**
 * WPFIA Currency Switcher - Admin Dashboard & Settings
 *
 * @package WPFIA_Currency_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFIA_CS_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_admin_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'save_settings' ) );
        add_action( 'admin_post_wpfia_cs_sync_rates', array( __CLASS__, 'handle_manual_sync' ) );
    }

    public static function register_admin_menu() {
        $capability = 'manage_woocommerce';
        if ( ! current_user_can( $capability ) && current_user_can( 'manage_options' ) ) {
            $capability = 'manage_options';
        }

        add_submenu_page(
            'woocommerce',
            __( 'WPFIA Currency Switcher', 'wpfia-currency-switcher' ),
            __( 'Currency Switcher', 'wpfia-currency-switcher' ),
            $capability,
            'wpfia-currency-switcher',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    public static function render_settings_page() {
        if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'wpfia-currency-switcher' ) );
        }

        $settings      = WPFIA_CS_Settings::get_instance();
        $all           = $settings->get_all();
        $currencies    = $all['currencies'];
        $base_currency = get_option( 'woocommerce_currency', 'USD' );
        $last_sync     = get_transient( 'wpfia_cs_last_sync_time' );

        include WPFIA_CURRENCY_SWITCHER_PATH . 'templates/admin-settings.php';
    }

    public static function save_settings() {
        if ( ! isset( $_POST['wpfia_cs_save_settings'] ) ) {
            return;
        }

        check_admin_referer( 'wpfia_cs_settings_nonce', 'wpfia_cs_nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $settings = WPFIA_CS_Settings::get_instance();

        $settings->set( 'enabled', ! empty( $_POST['enabled'] ) ? 1 : 0 );
        $settings->set( 'geo_detection', ! empty( $_POST['geo_detection'] ) ? 1 : 0 );
        $settings->set( 'menu_switcher', ! empty( $_POST['menu_switcher'] ) ? 1 : 0 );
        $settings->set( 'topbar_switcher', ! empty( $_POST['topbar_switcher'] ) ? 1 : 0 );
        $settings->set( 'topbar_position', sanitize_text_field( wp_unslash( $_POST['topbar_position'] ?? 'right' ) ) );
        $settings->set( 'topbar_text', sanitize_text_field( wp_unslash( $_POST['topbar_text'] ?? '' ) ) );
        $settings->set( 'floating_switcher', ! empty( $_POST['floating_switcher'] ) ? 1 : 0 );
        $settings->set( 'floating_position', sanitize_text_field( wp_unslash( $_POST['floating_position'] ?? 'bottom-right' ) ) );
        $settings->set( 'rounding_type', sanitize_text_field( wp_unslash( $_POST['rounding_type'] ?? 'none' ) ) );

        // Process currencies table
        $currencies_input = isset( $_POST['currencies'] ) && is_array( $_POST['currencies'] ) ? $_POST['currencies'] : array();
        $cleaned_currencies = array();

        foreach ( $currencies_input as $code => $data ) {
            $code = strtoupper( sanitize_text_field( $code ) );
            if ( strlen( $code ) !== 3 ) {
                continue;
            }

            $cleaned_currencies[ $code ] = array(
                'rate'     => floatval( $data['rate'] ?? 1.0 ),
                'symbol'   => sanitize_text_field( $data['symbol'] ?? '$' ),
                'decimals' => intval( $data['decimals'] ?? 2 ),
                'active'   => ! empty( $data['active'] ) ? 1 : 0,
            );
        }

        if ( ! empty( $cleaned_currencies ) ) {
            $settings->set( 'currencies', $cleaned_currencies );
        }

        wp_safe_redirect( add_query_arg( array( 'page' => 'wpfia-currency-switcher', 'saved' => '1' ), admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_manual_sync() {
        check_admin_referer( 'wpfia_cs_sync_nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'wpfia-currency-switcher' ) );
        }

        $res = WPFIA_CS_Rates::sync_configured_currency_rates();
        $msg = is_wp_error( $res ) ? 'error' : 'synced';

        wp_safe_redirect( add_query_arg( array( 'page' => 'wpfia-currency-switcher', 'sync_result' => $msg ), admin_url( 'admin.php' ) ) );
        exit;
    }
}

WPFIA_CS_Admin::init();
