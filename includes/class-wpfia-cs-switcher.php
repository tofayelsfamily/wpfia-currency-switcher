<?php
/**
 * WPFIA Currency Switcher - Core Switcher, Converter & Hooks
 *
 * @package WPFIA_Currency_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFIA_CS_Switcher {

    /**
     * Cookie name for storing selected currency.
     */
    const COOKIE_NAME = 'wpfia_selected_currency';

    /**
     * Bootstrap switcher hooks.
     */
    public static function init() {
        // Initialize currency session on startup
        add_action( 'init', array( __CLASS__, 'init_currency_session' ) );

        // WooCommerce Currency & Price Filters
        add_filter( 'woocommerce_currency', array( __CLASS__, 'filter_woocommerce_currency' ), 9999 );
        add_filter( 'woocommerce_currency_symbol', array( __CLASS__, 'filter_woocommerce_currency_symbol' ), 9999, 2 );
        add_filter( 'wc_price_args', array( __CLASS__, 'filter_price_args' ), 9999 );
        add_filter( 'raw_woocommerce_price', array( __CLASS__, 'convert_price' ), 9999 );

        // AJAX Switch Currency Handler
        add_action( 'wp_ajax_wpfia_cs_switch_currency', array( __CLASS__, 'ajax_switch_currency' ) );
        add_action( 'wp_ajax_nopriv_wpfia_cs_switch_currency', array( __CLASS__, 'ajax_switch_currency' ) );

        // Shortcode [wpfia_currency_switcher]
        add_shortcode( 'wpfia_currency_switcher', array( __CLASS__, 'render_switcher_shortcode' ) );

        // Auto Header Menu Switcher Item
        add_filter( 'wp_nav_menu_items', array( __CLASS__, 'append_to_menu' ), 99, 2 );

        // Topbar Header Switcher Strip
        add_action( 'wp_body_open', array( __CLASS__, 'render_topbar_switcher' ), 1 );

        // Floating Switcher in footer
        add_action( 'wp_footer', array( __CLASS__, 'render_floating_switcher' ) );
    }

    /**
     * Determine active currency from Cookie, Geo-IP, or default WooCommerce store currency.
     *
     * @return string ISO 3-letter currency code.
     */
    /**
     * Reentrancy guard to avoid recursive filter loops.
     */
    private static $is_filtering = false;

    /**
     * Determine active currency from Cookie, Geo-IP, or default WooCommerce store currency.
     *
     * @return string ISO 3-letter currency code.
     */
    public static function get_current_currency() {
        $settings = WPFIA_CS_Settings::get_instance();
        $base     = get_option( 'woocommerce_currency', 'USD' );

        if ( ! $settings->get( 'enabled', 1 ) ) {
            return $base;
        }

        // 1. User manual cookie selection
        if ( ! empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
            $cookie_cur = strtoupper( sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) ) );
            $currencies = $settings->get( 'currencies', array() );
            if ( isset( $currencies[ $cookie_cur ] ) && ! empty( $currencies[ $cookie_cur ]['active'] ) ) {
                return $cookie_cur;
            }
        }

        // 2. Auto Geo-Detector (if enabled)
        if ( $settings->get( 'geo_detection', 1 ) ) {
            $geo_cur = WPFIA_CS_Geo::detect_currency_for_visitor();
            if ( ! empty( $geo_cur ) ) {
                return $geo_cur;
            }
        }

        return $base;
    }

    /**
     * Initialize cookie if not set yet.
     */
    public static function init_currency_session() {
        if ( is_admin() && ! wp_doing_ajax() ) {
            return;
        }

        if ( empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
            $current = self::get_current_currency();
            if ( ! headers_sent() ) {
                setcookie( self::COOKIE_NAME, $current, time() + ( DAY_IN_SECONDS * 30 ), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), false );
            }
        }
    }

    /**
     * Filter WooCommerce active currency.
     */
    public static function filter_woocommerce_currency( $currency ) {
        if ( self::$is_filtering || ( is_admin() && ! wp_doing_ajax() ) ) {
            return $currency;
        }

        self::$is_filtering = true;
        $current = self::get_current_currency();
        self::$is_filtering = false;

        return $current;
    }

    /**
     * Filter WooCommerce currency symbol.
     */
    public static function filter_woocommerce_currency_symbol( $currency_symbol, $currency ) {
        $settings   = WPFIA_CS_Settings::get_instance();
        $currencies = $settings->get( 'currencies', array() );

        if ( isset( $currencies[ $currency ]['symbol'] ) ) {
            return $currencies[ $currency ]['symbol'];
        }
        return $currency_symbol;
    }

    /**
     * Filter decimals for specific currencies (e.g. JPY or BDT typically 0 decimals).
     */
    public static function filter_price_args( $args ) {
        $current    = self::get_current_currency();
        $settings   = WPFIA_CS_Settings::get_instance();
        $currencies = $settings->get( 'currencies', array() );

        if ( isset( $currencies[ $current ]['decimals'] ) ) {
            $args['decimals'] = (int) $currencies[ $current ]['decimals'];
        }
        return $args;
    }

    /**
     * Convert base price to currently selected currency.
     */
    public static function convert_price( $price ) {
        if ( ! is_numeric( $price ) || empty( $price ) ) {
            return $price;
        }

        $base       = get_option( 'woocommerce_currency', 'USD' );
        $current    = self::get_current_currency();
        $settings   = WPFIA_CS_Settings::get_instance();
        $currencies = $settings->get( 'currencies', array() );

        if ( $current === $base || empty( $currencies[ $current ]['rate'] ) ) {
            return $price;
        }

        $rate = (float) $currencies[ $current ]['rate'];
        if ( $rate <= 0 ) {
            return $price;
        }

        $converted = (float) $price * $rate;

        // Apply rounding rule
        $rounding = $settings->get( 'rounding_type', 'none' );
        switch ( $rounding ) {
            case '.99':
                $converted = floor( $converted ) + 0.99;
                break;
            case '.95':
                $converted = floor( $converted ) + 0.95;
                break;
            case 'round':
                $converted = round( $converted );
                break;
            case 'ceil':
                $converted = ceil( $converted );
                break;
        }

        return $converted;
    }

    /**
     * AJAX handler to switch currency.
     */
    public static function ajax_switch_currency() {
        check_ajax_referer( 'wpfia_cs_frontend_nonce', 'nonce' );

        $currency   = strtoupper( sanitize_text_field( wp_unslash( $_POST['currency'] ?? '' ) ) );
        $settings   = WPFIA_CS_Settings::get_instance();
        $currencies = $settings->get( 'currencies', array() );

        if ( ! isset( $currencies[ $currency ] ) || empty( $currencies[ $currency ]['active'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid currency selected.', 'wpfia-currency-switcher' ) ) );
        }

        // Set persistent 30-day cookie
        setcookie( self::COOKIE_NAME, $currency, time() + ( DAY_IN_SECONDS * 30 ), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), false );
        $_COOKIE[ self::COOKIE_NAME ] = $currency;

        wp_send_json_success( array(
            'currency' => $currency,
            'message'  => sprintf( __( 'Currency switched to %s.', 'wpfia-currency-switcher' ), $currency ),
        ) );
    }

    /**
     * Render Switcher Shortcode: [wpfia_currency_switcher]
     */
    public static function render_switcher_shortcode( $atts = array() ) {
        $settings   = WPFIA_CS_Settings::get_instance();
        $currencies = $settings->get( 'currencies', array() );
        $current    = self::get_current_currency();

        ob_start();
        ?>
        <div class="wpfia-cs-dropdown-wrap">
            <select class="wpfia-cs-select" aria-label="<?php esc_attr_e( 'Select Currency', 'wpfia-currency-switcher' ); ?>">
                <?php foreach ( $currencies as $code => $data ) : ?>
                    <?php if ( ! empty( $data['active'] ) ) : ?>
                        <option value="<?php echo esc_attr( $code ); ?>" <?php selected( $code, $current ); ?>>
                            <?php echo esc_html( $code . ' (' . ( $data['symbol'] ?? '$' ) . ')' ); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render floating bottom currency selector button.
     */
    public static function render_floating_switcher() {
        $settings = WPFIA_CS_Settings::get_instance();
        if ( ! $settings->get( 'enabled', 1 ) || ! $settings->get( 'floating_switcher', 1 ) ) {
            return;
        }

        $position   = $settings->get( 'floating_position', 'bottom-left' );
        $currencies = $settings->get( 'currencies', array() );
        $current    = self::get_current_currency();
        $sym        = $currencies[ $current ]['symbol'] ?? '$';
        ?>
        <div class="wpfia-cs-floating wpfia-cs-pos-<?php echo esc_attr( $position ); ?>" id="wpfia-cs-floating">
            <button type="button" class="wpfia-cs-floating-toggle" id="wpfia-cs-toggle" aria-expanded="false" aria-label="<?php esc_attr_e( 'Change Currency', 'wpfia-currency-switcher' ); ?>">
                <span class="wpfia-cs-pill-symbol"><?php echo esc_html( $sym ); ?></span>
                <span class="wpfia-cs-pill-code"><?php echo esc_html( $current ); ?></span>
                <span class="wpfia-cs-pill-arrow">▾</span>
            </button>
            <div class="wpfia-cs-floating-menu" id="wpfia-cs-menu">
                <div class="wpfia-cs-menu-header">
                    <span><?php esc_html_e( 'Select Currency', 'wpfia-currency-switcher' ); ?></span>
                </div>
                <ul class="wpfia-cs-currency-list">
                    <?php foreach ( $currencies as $code => $data ) : ?>
                        <?php if ( ! empty( $data['active'] ) ) : ?>
                            <li class="wpfia-cs-currency-item <?php echo $code === $current ? 'active' : ''; ?>" data-currency="<?php echo esc_attr( $code ); ?>">
                                <span class="wpfia-cs-item-code"><?php echo esc_html( $code ); ?></span>
                                <span class="wpfia-cs-item-sym"><?php echo esc_html( $data['symbol'] ?? '' ); ?></span>
                                <?php if ( $code === $current ) : ?>
                                    <span class="wpfia-cs-item-check">✓</span>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Automatically append a clean Currency Switcher dropdown to primary navigation menu.
     *
     * @param string   $items Menu HTML items.
     * @param stdClass $args  Menu arguments.
     * @return string
     */
    public static function append_to_menu( $items, $args = null ) {
        $settings = WPFIA_CS_Settings::get_instance();
        if ( ! $settings->get( 'enabled', 1 ) || ! $settings->get( 'menu_switcher', 1 ) ) {
            return $items;
        }

        // Only attach to main/primary header menus or when theme_location is not specified
        if ( isset( $args->theme_location ) && in_array( $args->theme_location, array( 'footer', 'footer-menu' ), true ) ) {
            return $items;
        }

        $current    = self::get_current_currency();
        $currencies = $settings->get( 'currencies', array() );
        $sym        = $currencies[ $current ]['symbol'] ?? '$';

        $menu_html  = '<li class="menu-item menu-item-has-children wpfia-cs-menu-item">';
        $menu_html .= '<a href="#" class="wpfia-cs-menu-toggle" onclick="return false;">';
        $menu_html .= '<span class="wpfia-cs-globe">🌐</span> ';
        $menu_html .= '<span class="wpfia-cs-menu-current">' . esc_html( $current ) . ' (' . esc_html( $sym ) . ')</span>';
        $menu_html .= ' <span class="wpfia-cs-arrow">▾</span></a>';
        $menu_html .= '<ul class="sub-menu wpfia-cs-sub-menu">';

        foreach ( $currencies as $code => $data ) {
            if ( ! empty( $data['active'] ) ) {
                $is_act     = ( $code === $current ) ? ' active' : '';
                $menu_html .= '<li class="wpfia-cs-currency-item' . $is_act . '" data-currency="' . esc_attr( $code ) . '">';
                $menu_html .= '<a href="#" onclick="return false;">';
                $menu_html .= '<span class="wpfia-cs-item-code">' . esc_html( $code ) . '</span> ';
                $menu_html .= '<span class="wpfia-cs-item-sym">' . esc_html( $data['symbol'] ?? '' ) . '</span>';
                if ( $code === $current ) {
                    $menu_html .= ' <span class="wpfia-cs-item-check">✓</span>';
                }
                $menu_html .= '</a></li>';
            }
        }

        $menu_html .= '</ul></li>';

        return $items . $menu_html;
    }

    /**
     * Render an ultra-slim, luxury Topbar Header strip at the top of the site.
     */
    public static function render_topbar_switcher() {
        $settings = WPFIA_CS_Settings::get_instance();
        if ( ! $settings->get( 'enabled', 1 ) || ! $settings->get( 'topbar_switcher', 0 ) ) {
            return;
        }

        $position   = $settings->get( 'topbar_position', 'right' );
        $text       = $settings->get( 'topbar_text', '' );
        $current    = self::get_current_currency();
        $currencies = $settings->get( 'currencies', array() );
        $sym        = $currencies[ $current ]['symbol'] ?? '$';
        ?>
        <div class="wpfia-cs-topbar wpfia-cs-topbar-<?php echo esc_attr( $position ); ?>" id="wpfia-cs-topbar">
            <div class="wpfia-cs-topbar-container">
                <div class="wpfia-cs-topbar-msg">
                    <?php if ( ! empty( $text ) ) : ?>
                        <span><?php echo esc_html( $text ); ?></span>
                    <?php else : ?>
                        <span>✨ <?php esc_html_e( 'Global Delivery & Multi-Currency Checkout', 'wpfia-currency-switcher' ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="wpfia-cs-topbar-ctrl">
                    <button type="button" class="wpfia-cs-topbar-btn" id="wpfia-cs-topbar-btn" aria-label="<?php esc_attr_e( 'Select Currency', 'wpfia-currency-switcher' ); ?>">
                        <span class="wpfia-cs-globe">🌐</span>
                        <span class="wpfia-cs-current-label"><?php echo esc_html( $current ); ?> (<?php echo esc_html( $sym ); ?>)</span>
                        <span class="wpfia-cs-arrow">▾</span>
                    </button>
                    <div class="wpfia-cs-topbar-menu" id="wpfia-cs-topbar-menu">
                        <ul class="wpfia-cs-currency-list">
                            <?php foreach ( $currencies as $code => $data ) : ?>
                                <?php if ( ! empty( $data['active'] ) ) : ?>
                                    <li class="wpfia-cs-currency-item <?php echo $code === $current ? 'active' : ''; ?>" data-currency="<?php echo esc_attr( $code ); ?>">
                                        <span class="wpfia-cs-item-code"><?php echo esc_html( $code ); ?></span>
                                        <span class="wpfia-cs-item-sym"><?php echo esc_html( $data['symbol'] ?? '' ); ?></span>
                                        <?php if ( $code === $current ) : ?>
                                            <span class="wpfia-cs-item-check">✓</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

// Start switcher hooks
WPFIA_CS_Switcher::init();
