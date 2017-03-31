<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Easy_Booking_Discounts_Admin_Assets' ) ) :

class Easy_Booking_Discounts_Admin_Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'ebdd_admin_scripts' ), 30 );
    }

    public function ebdd_admin_scripts() {
        $screen = get_current_screen();

        // Script to add or delete global discounts
        wp_register_script(
            'ebdd-discounts',
            wceb_get_file_path( 'admin', 'ebdd-admin-discounts', 'js', EBDD_PLUGIN_FILE ),
            array( 'jquery', 'jquery-tiptip' ),
            '1.0',
            true
        );

        // Script to add or delete product or variation discounts
        wp_register_script(
            'ebdd-product-discounts',
            wceb_get_file_path( 'admin', 'ebdd-product-discounts', 'js', EBDD_PLUGIN_FILE ),
            array( 'jquery' ),
            '1.0',
            true
        );

        // Discounts table styles
        wp_register_style(
            'ebdd-settings',
            wceb_get_file_path( 'admin', 'ebdd-settings', 'css', EBDD_PLUGIN_FILE ),
            array(),
            '1.0'
        );
        
        // Enqueue script on the product page only
        if ( in_array( $screen->id, array( 'product' ) ) ) {
            wp_enqueue_script( 'ebdd-product-discounts' );
        }

	}

}

return new Easy_Booking_Discounts_Admin_Assets();

endif;