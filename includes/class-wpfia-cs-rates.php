<?php
/**
 * WPFIA Currency Switcher - Real-time Exchange Rates Manager
 *
 * @package WPFIA_Currency_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFIA_CS_Rates {

    /**
     * Fetch live conversion rates from reliable free open APIs.
     *
     * @param string $base_currency Base ISO currency code (e.g. USD, EUR).
     * @return array|WP_Error Associative array of currency => rate
     */
    public static function fetch_live_rates( $base_currency = 'USD' ) {
        $base_currency = strtoupper( trim( $base_currency ) );

        // 1. Try European Central Bank Frankfurter API (Fast, Free, Open)
        $url = add_query_arg(
            array( 'from' => $base_currency ),
            'https://api.frankfurter.app/latest'
        );

        $response = wp_remote_get(
            $url,
            array(
                'timeout'    => 10,
                'user-agent' => 'WPFIA-Currency-Switcher/1.0.0; ' . home_url(),
            )
        );

        if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( ! empty( $data['rates'] ) && is_array( $data['rates'] ) ) {
                $rates = $data['rates'];
                $rates[ $base_currency ] = 1.0;
                return $rates;
            }
        }

        // 2. Fallback to Open ExchangeRate API (Zero API key required)
        $fallback_url = 'https://open.er-api.com/v6/latest/' . rawurlencode( $base_currency );
        $fallback_res = wp_remote_get(
            $fallback_url,
            array(
                'timeout'    => 10,
                'user-agent' => 'WPFIA-Currency-Switcher/1.0.0; ' . home_url(),
            )
        );

        if ( ! is_wp_error( $fallback_res ) && 200 === wp_remote_retrieve_response_code( $fallback_res ) ) {
            $fallback_data = json_decode( wp_remote_retrieve_body( $fallback_res ), true );
            if ( ! empty( $fallback_data['rates'] ) && is_array( $fallback_data['rates'] ) ) {
                return $fallback_data['rates'];
            }
        }

        return new WP_Error(
            'rate_fetch_failed',
            __( 'Unable to fetch exchange rates from live providers. Please check your internet connection or enter rates manually.', 'wpfia-currency-switcher' )
        );
    }

    /**
     * Update configured store currency rates in database.
     *
     * @return bool|WP_Error
     */
    public static function sync_configured_currency_rates() {
        $settings      = WPFIA_CS_Settings::get_instance();
        $base_currency = get_option( 'woocommerce_currency', 'USD' );
        $currencies    = $settings->get( 'currencies', array() );

        $live_rates = self::fetch_live_rates( $base_currency );
        if ( is_wp_error( $live_rates ) ) {
            return $live_rates;
        }

        $updated = false;
        foreach ( $currencies as $code => &$data ) {
            if ( $code === $base_currency ) {
                $data['rate'] = 1.0;
                $updated      = true;
            } elseif ( isset( $live_rates[ $code ] ) ) {
                $data['rate'] = (float) $live_rates[ $code ];
                $updated      = true;
            }
        }
        unset( $data );

        if ( $updated ) {
            $settings->set( 'currencies', $currencies );
            set_transient( 'wpfia_cs_last_sync_time', current_time( 'mysql' ), MONTH_IN_SECONDS );
            return true;
        }

        return false;
    }
}
