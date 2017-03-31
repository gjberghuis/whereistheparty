<?php
/*
Plugin Name: Easy Booking : Duration Discounts
Plugin URI: http://herownsweetcode.com/product/easy-booking-duration-discounts/
Description: WooCommerce Easy Booking add-on to add discounts to your products depending on the duration booked by your clients.
Version: 1.7.4
Author: @_Ashanna
Author URI: http://herownsweetcode.com
Licence : GPL
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Easy_Booking_Duration_Discounts' ) ) :

class Easy_Booking_Duration_Discounts {

    protected static $_instance = null;

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {

        // Check if WooCommerce Easy Booking is active
        if ( $this->ebdd_easy_booking_is_active() ) {

            $plugin = plugin_basename( __FILE__ );

            // Init plugin
            add_action( 'plugins_loaded', array( $this, 'ebdd_discounts_init'), 22 );

            // Add settings link
            add_filter( 'plugin_action_links_' . $plugin, array( $this, 'ebdd_add_settings_link' ) );

            // Admin notices
            add_action( 'admin_notices', array( $this, 'ebdd_add_notices' ) );
            add_action( 'network_admin_notices', array( $this, 'ebdd_add_network_notices' ) );

        }

    }

    /**
    *
    * Check if WooCommerce Easy Booking is active
    *
    * @return bool
    *
    **/
    private function ebdd_easy_booking_is_active() {

        $active_plugins = (array) get_option( 'active_plugins', array() );

        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        return ( array_key_exists( 'woocommerce-easy-booking-system/woocommerce-easy-booking.php', $active_plugins ) || in_array( 'woocommerce-easy-booking-system/woocommerce-easy-booking.php', $active_plugins ) );

    }

    public function ebdd_discounts_init() {

        // Don't init plugin if WooCommerce Easy Booking isn't at least version 1.9.
        if ( ! method_exists( WCEB(), 'wceb_get_version' ) ) {
            return;
        }

        // Define constants
        $this->ebdd_define_constants();
        
        // Load textdomain
        $this->ebdd_load_textdomain();

        // Common includes
        $this->ebdd_includes();

        // Admin includes
        if ( is_admin() ) {
           $this->ebdd_admin_includes(); 
        }
        
        // Frontend includes
        if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
            $this->ebdd_frontend_includes();
        }

    }

    /**
    *
    * Define constants
    *
    **/
    private function ebdd_define_constants() {
        define( 'EBDD_PLUGIN_FILE', __FILE__ );
    }

    /**
    *
    * Load textdomain
    *
    **/
    private function ebdd_load_textdomain() {
        load_plugin_textdomain( 'easy_booking_discounts', false, basename( dirname(__FILE__) ) . '/languages/' );
    }

    /**
    *
    * Add settings link
    *
    **/
    public function ebdd_add_settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=easy-booking-discounts">' . __('Settings', 'easy_booking_discounts') . '</a>';
        array_push( $links, $settings_link );

        return $links;
    }

    /**
    *
    * Common includes
    *
    **/
    private function ebdd_includes() {
        include_once( 'easy-booking-duration-discounts-update.php' );
        include_once( 'includes/ebdd-functions.php' );
    }

    /**
    *
    * Admin includes
    *
    **/
    private function ebdd_admin_includes() {
        include_once( 'includes/settings/class-ebdd-settings.php' );
        include_once( 'includes/admin/class-ebdd-admin-product.php' );
        include_once( 'includes/admin/class-ebdd-admin-assets.php' );
    }

    /**
    *
    * Frontend includes
    *
    **/
    private function ebdd_frontend_includes() {
        include_once( 'includes/class-ebdd-product-view.php' );
        include_once( 'includes/class-ebdd-assets.php' );
    }

    /**
    *
    * License key notice
    *
    **/
    public function ebdd_add_notices() {

        if ( is_multisite() ) {
            return;
        }

        // Don't init plugin if WooCommerce Easy Booking isn't at least version 1.9.
        if ( ! method_exists( WCEB(), 'wceb_get_version' ) ) {
            include_once( 'includes/admin/views/html-ebdd-notice-update.php' );
            return;
        }

        $settings    = get_option('easy_booking_discounts_settings');
        $license_key = isset( $settings['easy_booking_discounts_license_key'] ) ? $settings['easy_booking_discounts_license_key'] : false;

        if ( empty( $license_key ) ) {
            $license_key = false;
        }

        $screen = get_current_screen();

        if ( in_array( $screen->id, array( 'easy-booking_page_easy-booking-discounts' ) ) ) {
            return;
        }

        if ( ! is_multisite() && get_option( 'easy_booking_display_notice_ebdd_license' ) !== '1' && ! $license_key ) {
            include_once( 'includes/admin/views/html-ebdd-notice-license-key.php' );
        }
    }

    /**
    *
    * License key notice on network (for multisites)
    *
    **/
    public function ebdd_add_network_notices() {
        $settings    = get_option('easy_booking_global_settings');
        $license_key = ! isset( $settings['easy_booking_discounts_license_key'] ) || empty( $settings['easy_booking_discounts_license_key'] ) ? false : $settings['easy_booking_discounts_license_key'];

        $screen = get_current_screen();
        
        if ( in_array( $screen->id, array( 'toplevel_page_easy-booking-network' ) ) ) {
            return;
        }

        if ( get_option( 'easy_booking_display_notice_ebdd_license' ) !== '1' && ! $license_key ) {
            include_once( 'includes/admin/views/html-ebdd-notice-license-key.php' );
        }
    }

}

function EBDD() {
    return Easy_Booking_Duration_Discounts::instance();
}

EBDD();

endif;