<?php
/**
 * WPFIA Currency Switcher - Settings Manager
 *
 * @package WPFIA_Currency_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFIA_CS_Settings {

    private static $instance = null;
    private $options_key = 'wpfia_cs_settings';
    private $options = array();

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->options = get_option( $this->options_key, array() );
    }

    public function get( $key, $default = null ) {
        if ( isset( $this->options[ $key ] ) ) {
            return $this->options[ $key ];
        }
        $defaults = $this->get_defaults();
        return isset( $defaults[ $key ] ) ? $defaults[ $key ] : $default;
    }

    public function set( $key, $value ) {
        $this->options[ $key ] = $value;
        return update_option( $this->options_key, $this->options );
    }

    public function get_all() {
        return wp_parse_args( $this->options, $this->get_defaults() );
    }

    public function get_defaults() {
        $base = get_option( 'woocommerce_currency', 'USD' );

        $base_symbol = '$';
        if ( 'EUR' === $base ) {
            $base_symbol = '€';
        } elseif ( 'GBP' === $base ) {
            $base_symbol = '£';
        } elseif ( 'BDT' === $base ) {
            $base_symbol = '৳';
        } elseif ( 'INR' === $base ) {
            $base_symbol = '₹';
        } elseif ( 'JPY' === $base ) {
            $base_symbol = '¥';
        }

        return array(
            'enabled'              => 1,
            'base_currency'        => $base,
            'geo_detection'        => 1,
            'api_provider'         => 'frankfurter', // Free, unlimited, zero-auth Open API
            'auto_update_rates'    => 'daily',       // hourly, twicedaily, daily, manual
            'menu_switcher'        => 1,             // 1 = Auto-add dropdown to Header Nav Menu (Recommended)
            'topbar_switcher'      => 0,             // 1 = Show slim Topbar Header strip
            'topbar_position'      => 'right',       // 'right' or 'left'
            'topbar_text'          => '',            // Optional topbar announcement text
            'floating_switcher'    => 1,             // 1 = Show floating corner button
            'floating_position'    => 'bottom-right',// bottom-right, bottom-left, top-right, top-left
            'show_flags'           => 1,
            'show_symbols'         => 1,
            'rounding_type'        => 'none',        // none, .99, .95, round, ceil
            'currencies'           => array(
                $base  => array(
                    'rate'     => 1.0,
                    'symbol'   => $base_symbol,
                    'decimals' => 2,
                    'active'   => 1,
                ),
                'EUR'  => array(
                    'rate'     => 0.92,
                    'symbol'   => '€',
                    'decimals' => 2,
                    'active'   => 1,
                ),
                'GBP'  => array(
                    'rate'     => 0.79,
                    'symbol'   => '£',
                    'decimals' => 2,
                    'active'   => 1,
                ),
                'BDT'  => array(
                    'rate'     => 121.50,
                    'symbol'   => '৳',
                    'decimals' => 0,
                    'active'   => 1,
                ),
                'INR'  => array(
                    'rate'     => 86.80,
                    'symbol'   => '₹',
                    'decimals' => 2,
                    'active'   => 1,
                ),
            ),
        );
    }
}
