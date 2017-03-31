<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Easy_Booking_Discounts_Assets' ) ) :

class Easy_Booking_Discounts_Assets {

	public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'ebdd_enqueue_scripts' ), 20 );
	}

	public function ebdd_enqueue_scripts() {
        global $post;

        // Return if not on a single product page
        if ( ! is_product() ) {
            return;
        }

        $ebdd_settings = get_option('easy_booking_discounts_settings');

        // Return if discounts are not displayed
        if ( empty( $ebdd_settings['easy_booking_discounts_display'] ) ) {
            return;
        }

        $post_id = $post->ID;
        $product = get_product( $post_id );

        // Enqueue script only if product is bookable and variable
        if ( wceb_is_bookable( $product ) && $product->is_type( 'variable' ) ) {

            wp_enqueue_script( 'easy_booking_variation_discounts', wceb_get_file_path( '', 'ebdd-variation-discounts', 'js', EBDD_PLUGIN_FILE ), array( 'jquery' ), '1.0', true );

            wp_localize_script( 'easy_booking_variation_discounts', 'ajax', array(
                    'discounts' => ebdd_get_product_discounts_html( $product->id )
                )
            );

        }

        if ( wceb_is_bookable( $product ) && $product->is_type( 'bundle' ) ) {

            wp_enqueue_script( 'easy_booking_bundle_discounts', wceb_get_file_path( '', 'ebdd-bundle-variable-discounts', 'js', EBDD_PLUGIN_FILE ), array( 'jquery' ), '1.0', true );

            wp_localize_script( 'easy_booking_bundle_discounts', 'ajax', array(
                    'discounts' => ebdd_get_product_discounts_html( $product->id )
                )
            );

        }
    }
}

return new Easy_Booking_Discounts_Assets();

endif;