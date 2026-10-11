<?php
/**
 * WPFIA Currency Switcher - Automatic Geo-IP Location Detector
 *
 * @package WPFIA_Currency_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFIA_CS_Geo {

    /**
     * Map country ISO codes to standard currency ISO codes.
     */
    private static $country_currency_map = array(
        'US' => 'USD', 'CA' => 'CAD', 'GB' => 'GBP', 'AU' => 'AUD', 'NZ' => 'NZD',
        'BD' => 'BDT', 'IN' => 'INR', 'PK' => 'PKR', 'AE' => 'AED', 'SA' => 'SAR',
        'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR', 'NL' => 'EUR',
        'JP' => 'JPY', 'CN' => 'CNY', 'SG' => 'SGD', 'MY' => 'MYR', 'BR' => 'BRL',
        'ZA' => 'ZAR', 'CH' => 'CHF', 'SE' => 'SEK', 'NO' => 'NOK', 'DK' => 'DKK',
        'TR' => 'TRY', 'TH' => 'THB', 'VN' => 'VND', 'ID' => 'IDR', 'PH' => 'PHP',
    );

    /**
     * Detect visitor's country ISO code based on IP or Cloudflare/Server headers.
     *
     * @return string Country 2-letter uppercase ISO code, or '' if undetected.
     */
    public static function detect_visitor_country() {
        // 1. Cloudflare IP Geolocation Header (fastest & zero request)
        if ( ! empty( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
            $cf_country = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) );
            if ( 2 === strlen( $cf_country ) && 'XX' !== $cf_country && 'T1' !== $cf_country ) {
                return $cf_country;
            }
        }

        // 2. Native WooCommerce Geolocation if available
        if ( class_exists( 'WC_Geolocation' ) ) {
            $geo = WC_Geolocation::geolocate_ip();
            if ( ! empty( $geo['country'] ) ) {
                return strtoupper( sanitize_text_field( $geo['country'] ) );
            }
        }

        // 3. Resolve client IP address
        $ip = self::get_client_ip();
        if ( empty( $ip ) || '127.0.0.1' === $ip || '::1' === $ip || false !== strpos( $ip, '192.168.' ) ) {
            return '';
        }

        // 4. Transient Cache for IP lookup to avoid redundant API hits
        $cache_key = 'wpfia_cs_country_' . md5( $ip );
        $cached    = get_transient( $cache_key );
        if ( false !== $cached ) {
            return $cached;
        }

        // 5. Query fast free IP API
        $response = wp_remote_get(
            'https://ipapi.co/' . rawurlencode( $ip ) . '/country/',
            array(
                'timeout'    => 4,
                'user-agent' => 'WPFIA-Currency-Switcher/1.0.0; ' . home_url(),
            )
        );

        if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
            $country = strtoupper( trim( wp_remote_retrieve_body( $response ) ) );
            if ( 2 === strlen( $country ) ) {
                set_transient( $cache_key, $country, DAY_IN_SECONDS * 7 );
                return $country;
            }
        }

        return '';
    }

    /**
     * Match detected country to configured currency in store.
     *
     * @return string|null Currency code if supported, or null.
     */
    public static function detect_currency_for_visitor() {
        $country = self::detect_visitor_country();
        if ( empty( $country ) || ! isset( self::$country_currency_map[ $country ] ) ) {
            return null;
        }

        $mapped_currency = self::$country_currency_map[ $country ];
        $settings        = WPFIA_CS_Settings::get_instance();
        $currencies      = $settings->get( 'currencies', array() );

        if ( isset( $currencies[ $mapped_currency ] ) && ! empty( $currencies[ $mapped_currency ]['active'] ) ) {
            return $mapped_currency;
        }

        return null;
    }

    /**
     * Retrieve visitor client IP safely.
     *
     * @return string
     */
    private static function get_client_ip() {
        $ip = '';
        if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $parts = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $ip    = trim( $parts[0] );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }
        return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
    }
}
