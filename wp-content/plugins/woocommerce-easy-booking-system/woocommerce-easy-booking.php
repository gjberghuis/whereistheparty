<?php
/*
* Plugin Name: Woocommerce Easy Booking
* Plugin URI: http://herownsweetcode.com/easy-booking/
* Description: Easily rent or book your products with WooCommerce
* Version: 2.0.9
* Author: @_Ashanna
* Author URI: http://herownsweetcode.com
* Text domain: easy_booking
* Domain path: /languages
* Licence : GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Easy_booking' ) ) :

class Easy_booking {

    protected static $_instance = null;
    public $allowed_types;

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {
        $plugin = plugin_basename( __FILE__ );

        // Check if WooCommerce is active
        if ( $this->wceb_woocommerce_is_active() ) {

            // Init plugin
            add_action( 'plugins_loaded', array( $this, 'wceb_init' ), 10 );

            // Add settings link
            add_filter( 'plugin_action_links_' . $plugin, array( $this, 'wceb_add_settings_link' ) );

            // Add notices
            add_action( 'admin_notices', array( $this, 'wceb_add_notices' ) );

            // Update order data after admin init (only once)
            if ( is_admin() && get_option( 'easy_booking_data_needs_update' ) != '1' ) {
                add_action( 'admin_init', array( $this, 'wceb_order_backward_compatibility' ), 999 );
            }
            
        }

    }

    /**
    *
    * Get the current plugin version
    *
    * @return str
    *
    **/
    public function wceb_get_version() {
        return '2.0.8';
    }

    /**
    *
    * Check if WooCommerce is active
    *
    * @return bool
    *
    **/
    private function wceb_woocommerce_is_active() {

        $active_plugins = (array) get_option( 'active_plugins', array() );

        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        return ( array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) || in_array( 'woocommerce/woocommerce.php', $active_plugins ) );

    }

    /**
    *
    * Init plugin
    *
    **/
    public function wceb_init() {
        // Define constants
        $this->wceb_define_constants();

        // Load textdomain
        load_plugin_textdomain( 'easy_booking', false, basename( dirname( __FILE__ ) ) . '/languages/' );

        // Filter to extend allowed product types. Needs to be used in a plugin.
        $this->allowed_types = apply_filters(
            'easy_booking_allowed_product_types',
            array(
                'simple',
                'variable',
                'grouped',
                'bundle'
            )
        );

        // Common includes
        $this->wceb_includes();

        // Admin includes
        if ( is_admin() ) {
            $this->wceb_admin_includes();
        }
        
        // Frontend includes
        if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
            $this->wceb_frontend_includes();
        }

    }

    /**
    *
    * Define constants
    * WCEB_PLUGIN_FILE - Plugin directory
    * WCEB_LANG - Site language to load pickadate.js translations
    * WCEB_PATH - Path to assets (dev or not)
    * WCEB_SUFFIX - Suffix for the assets (minified or not)
    *
    **/
    private function wceb_define_constants() {
        // Plugin directory
        define( 'WCEB_PLUGIN_FILE', __FILE__ );

        // Get page language in order to load Pickadate translation
        $site_language = get_bloginfo( 'language' );
        $lang = str_replace( '-', '_', $site_language );

        // Site language
        define( 'WCEB_LANG', $lang );

        $path = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'dev/' : '';
        $min  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        // Path and suffix to load minified (or not) files
        define( 'WCEB_PATH', $path );
        define( 'WCEB_SUFFIX', $min );
    }

    /**
    *
    * Common includes
    *
    **/
    public function wceb_includes() {
        include_once( 'includes/wceb-functions.php');
        include_once( 'includes/wceb-product-functions.php');
        include_once( 'includes/class-wceb-ajax.php' );
        include_once( 'includes/class-wceb-checkout.php' );
    }

    /**
    *
    * Admin includes
    *
    **/
    public function wceb_admin_includes() {
        include_once( 'includes/settings/wceb-settings-functions.php' );
        include_once( 'includes/settings/class-wceb-settings.php' );
        include_once( 'includes/reports/class-wceb-list-reports.php' );
        include_once( 'includes/reports/class-wceb-reports.php' );
        include_once( 'includes/admin/class-wceb-admin-product-settings.php' );
        include_once( 'includes/admin/class-wceb-order.php' );
        include_once( 'includes/admin/class-wceb-admin-assets.php' );
    }

    /**
    *
    * Frontend
    *
    **/
    public function wceb_frontend_includes() {
        include_once( 'includes/class-wceb-product-archive-view.php' );
        include_once( 'includes/class-wceb-product-view.php' );
        include_once( 'includes/class-wceb-assets.php' );
        include_once( 'includes/class-wceb-cart.php' );
    }

    /**
    *
    * Display a notice on the admin to inform about the addons
    *
    **/
    public function wceb_add_notices() {
        if ( get_option( 'easy_booking_display_notice_wceb-addons' ) != 1 ) {
            include_once( 'includes/admin/views/wceb-html-notice-addons.php' );
        }
    }

    /**
    *
    * Add settings link
    *
    **/
    public function wceb_add_settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=easy-booking">' . __('Settings', 'easy_booking') . '</a>';
        array_push( $links, $settings_link );

        return $links;
    }
    
    /**
    *
    * Backward compatibility
    *
    **/
    public function easy_booking_is_bookable( $product_id, $variation_id = '' ) {
        $product = wc_get_product( empty( $variation_id ) ? $product_id : $variation_id );
        return wceb_is_bookable( $product );
    }

    /**
    *
    * Update all order metadata for bookable products, to serialize "Start" and "End" values
    * to hide them on the order page.
    *
    **/
    public function wceb_order_backward_compatibility() {

        // Query orders
        $args = array(
            'post_type'      => 'shop_order',
            'post_status'    => apply_filters( 
                                'easy_booking_get_order_statuses',
                                array(
                                    'wc-pending',
                                    'wc-processing',
                                    'wc-on-hold',
                                    'wc-completed',
                                    'wc-refunded'
                                ) ),
            'posts_per_page' => -1
        );

        $query_orders = new WP_Query( $args );

        $products = array();
        foreach ( $query_orders->posts as $post ) :

            // Get order
            $order_id = $post->ID;
            $order    = new WC_Order( $order_id );

            // Get order items
            $items = $order->get_items();

            // Loop through items
            if ( ! empty( $items ) ) foreach ( $items as $item_id => $item ) {

                // If start format is set
                if ( isset( $item['ebs_start_format'] ) ) {

                    // If start display is set
                    if ( isset( $item['ebs_start_display'] ) ) {

                        $start_display = $item['ebs_start_display'];

                    } else {

                        // If start display is not set (for orders passed before Easy Booking 1.4)
                        wc_update_order_item_meta(
                            $item_id,
                            $item['ebs_start_display'],
                            date('d F Y', strtotime( $item['ebs_start_format'] ) )
                        );

                    }

                }

                // If end format is set
                if ( isset( $item['ebs_end_format'] ) ) {

                    // If end display is set
                    if ( isset( $item['ebs_end_display'] ) ) {

                        $end_display = $item['ebs_end_display'];

                    } else {

                        // If end display is not set (for orders passed before Easy Booking 1.4)
                        wc_update_order_item_meta(
                            $item_id,
                            $item['ebs_end_display'],
                            date('d F Y', strtotime( $item['ebs_end_format'] ) )
                        );

                    }
                    
                }

                // Loop through item meta
                if ( ! empty( $item['item_meta'] ) ) foreach ( $item['item_meta'] as $key => $value ) {

                    // If start date is set
                    if ( isset( $start_display ) ) {

                        if ( $key !== '_ebs_start_display' && isset( $value[0] ) && ! empty( $value[0] ) ) {
                            if ( $value[0] === $start_display ) {
                                // Serialize meta value so it's not visible on the order page
                                wc_update_order_item_meta( $item_id, $key, serialize( $value[0] ) );
                            }
                        }
                        
                    }

                    // If end date is set
                    if ( isset( $end_display ) ) {

                        if ( $key !== '_ebs_end_display' && isset( $value[0] ) && ! empty( $value[0] ) ) {
                            if ( $value[0] === $end_display ) {
                                // Serialize meta value so it's not visible on the order page
                                wc_update_order_item_meta( $item_id, $key, serialize( $value[0] ) );
                            }
                        }
                        
                    }

                }

            }

        endforeach;

        update_option( 'easy_booking_data_needs_update', '1' );
        return false;

    }

}

function WCEB() {
    return Easy_booking::instance();
}

WCEB();

endif;