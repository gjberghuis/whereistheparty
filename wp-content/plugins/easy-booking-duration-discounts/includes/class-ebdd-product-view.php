<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'EBDD_Product_View' ) ) :

class EBDD_Product_View {

	public function __construct() {
        // Get plugin options values
        $this->options = get_option( 'easy_booking_discounts_settings' );

	//	add_action( 'woocommerce_single_product_summary', array( $this, 'ebdd_display_discounts' ), 15 );
        add_action( 'woocommerce_bundled_item_details', array( $this, 'ebdd_display_discounts' ), 28, 2 );
        add_filter( 'woocommerce_get_price_html', array( $this, 'ebdd_display_grouped_discounts' ), 20, 2 );
     //   add_filter( 'easy_booking_display_average_price', array( $this, 'ebdd_display_average_price' ), 10, 2 );
	}

    /**
    *
    * Displays the product discount (if the option is checked)
    *
    **/
    public function ebdd_display_discounts( $bundled_item = false ) {
        global $product;

        // Avoid conflicts
        $_product = $product;
        $bundled_item_id = false;

        // WooCommerce Product Bundles compatibility
        if ( $bundled_item && ! empty( $bundled_item ) ) {
            
            $bundled_item_id = $bundled_item->product_id;

            $apply_discounts = get_post_meta( $bundled_item_id, '_apply_ebdd_discounts', true );

            if ( empty( $apply_discounts ) || ! $bundled_item->is_priced_individually() ) {
                return;
            }

            if ( $bundled_item->product->product_type === 'variable' ) {
                echo '<p class="ebdd_discounts"></p>';
                return;
            }

        }
        
        $_product_id = $_product->id;

        // If product is not bookable or discounts are not displayed, return
        if ( ! wceb_is_bookable( $_product ) || empty( $this->options['easy_booking_discounts_display'] ) ) {
            return;
        }

        if ( ! $_product->is_type( 'variable' ) && wceb_get_product_booking_dates( $_product ) === 'one' ) {
            return;
        }

        echo '<p class="ebdd_discounts">';

            // Display only if product is simple and not grouped or bundled product
            if ( ( $_product->is_type( 'simple' ) && ! $_product->get_parent() ) || $_product->is_type( 'bundle' ) ) {
                $discounts_html = ebdd_get_product_discounts_html( $_product_id, $bundled_item_id );
                echo $discounts_html[$_product_id];
            }

        echo '</p>';
    }

    /**
    *
    * Displays the product discount (if the option is checked) for each grouped product
    *
    **/
    public function ebdd_display_grouped_discounts( $content, $product ) {

        // If product has no parent product, return normal price
        if ( ! $product->get_parent() ) {
            return $content;
        }

        // If not on the product page, return normal price
        if ( ! is_product() ) {
            return $content;
        }

        // If product is not bookable, return normal price
        if ( ! wceb_is_bookable( $product ) ) {
            return $content;
        }

        // If discounts are not displayed, return normal price
        if ( empty( $this->options['easy_booking_discounts_display'] ) || wceb_get_product_booking_dates( $product ) === 'one' ) {
            return $content;
        }
        
        $product_id = $product->id;
        $discounts_html = ebdd_get_product_discounts_html( $product_id );
        $content .= '<p class="ebbd_discounts">' . $discounts_html[$product_id] . '</p>';

        return $content;
    }

    public function ebdd_display_average_price( $display, $id ) {

        // Get product discounts
        $product_discounts = get_post_meta( $id, '_discount_data', true );

        // If the product has discounts, return the average price
        if ( ! empty( $product_discounts ) ) {
            $display = true;
        }

        return $display;
    }

}

return new EBDD_Product_View();

endif;