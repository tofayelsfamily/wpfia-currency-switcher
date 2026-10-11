<?php
/**
 * Plugin Name:       WPFIA Currency Switcher & Auto Geo-Detector
 * Plugin URI:        https://wpfia.com/plugins/currency-switcher
 * Description:       Ultra-lightweight multi-currency converter and automatic IP-based currency detector for WooCommerce. 100% Lifetime Free.
 * Version:           1.0.0
 * Author:            WPFIA
 * Author URI:        https://wpfia.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpfia-currency-switcher
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * WC requires at least: 5.0
 * WC tested up to:   9.5
 *
 * @package WPFIA_Currency_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Global Definitions
define( 'WPFIA_CURRENCY_SWITCHER_VERSION', '1.0.0' );
define( 'WPFIA_CURRENCY_SWITCHER_FILE', __FILE__ );
define( 'WPFIA_CURRENCY_SWITCHER_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPFIA_CURRENCY_SWITCHER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Initialization Class.
 */
final class WPFIA_Currency_Switcher {

    /**
     * Singleton Instance.
     *
     * @var WPFIA_Currency_Switcher|null
     */
    private static $instance = null;

    /**
     * Get Singleton Instance.
     *
     * @return WPFIA_Currency_Switcher
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include core files.
     */
    private function includes() {
        require_once WPFIA_CURRENCY_SWITCHER_PATH . 'includes/class-wpfia-cs-settings.php';
        require_once WPFIA_CURRENCY_SWITCHER_PATH . 'includes/class-wpfia-cs-rates.php';
        require_once WPFIA_CURRENCY_SWITCHER_PATH . 'includes/class-wpfia-cs-geo.php';
        require_once WPFIA_CURRENCY_SWITCHER_PATH . 'includes/class-wpfia-cs-switcher.php';

        if ( is_admin() ) {
            require_once WPFIA_CURRENCY_SWITCHER_PATH . 'includes/class-wpfia-cs-admin.php';
        }
    }

    /**
     * Initialize WordPress & WooCommerce hooks.
     */
    private function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
    }

    /**
     * Load translation files.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'wpfia-currency-switcher', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Declare WooCommerce HPOS & Block Cart/Checkout compatibility.
     */
    public function declare_hpos_compatibility() {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
        }
    }

    /**
     * Enqueue frontend styling and JS scripts.
     */
    public function enqueue_frontend_assets() {
        $settings = WPFIA_CS_Settings::get_instance();
        if ( ! $settings->get( 'enabled', 1 ) ) {
            return;
        }

        $css_ver = file_exists( WPFIA_CURRENCY_SWITCHER_PATH . 'assets/css/switcher.css' ) 
            ? filemtime( WPFIA_CURRENCY_SWITCHER_PATH . 'assets/css/switcher.css' ) 
            : WPFIA_CURRENCY_SWITCHER_VERSION;
            
        $js_ver = file_exists( WPFIA_CURRENCY_SWITCHER_PATH . 'assets/js/switcher.js' ) 
            ? filemtime( WPFIA_CURRENCY_SWITCHER_PATH . 'assets/js/switcher.js' ) 
            : WPFIA_CURRENCY_SWITCHER_VERSION;

        wp_enqueue_style(
            'wpfia-cs-frontend',
            WPFIA_CURRENCY_SWITCHER_URL . 'assets/css/switcher.css',
            array(),
            $css_ver
        );

        wp_enqueue_script(
            'wpfia-cs-frontend',
            WPFIA_CURRENCY_SWITCHER_URL . 'assets/js/switcher.js',
            array( 'jquery' ),
            $js_ver,
            true
        );

        wp_localize_script(
            'wpfia-cs-frontend',
            'WPFIA_CS_Data',
            array(
                'ajax_url'         => admin_url( 'admin-ajax.php' ),
                'nonce'            => wp_create_nonce( 'wpfia_cs_frontend_nonce' ),
                'current_currency' => WPFIA_CS_Switcher::get_current_currency(),
            )
        );
    }
}

/**
 * Bootstrap the plugin.
 */
function wpfia_currency_switcher() {
    return WPFIA_Currency_Switcher::get_instance();
}

// Bootstrap immediately or on plugins_loaded
if ( did_action( 'plugins_loaded' ) ) {
    wpfia_currency_switcher();
} else {
    add_action( 'plugins_loaded', 'wpfia_currency_switcher', 10 );
}
