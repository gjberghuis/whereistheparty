<?php

namespace EasyBooking;

/**
*
* Cart action hooks and filters.
* @version 2.3.0
*
**/

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Cart' ) ) :

class Cart {

    public function __construct() {
        
        // Check that dates are set before adding to cart.
        add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 20, 5 );

        // Get cart item data from session.
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_booking_data_from_session' ), 98, 2 );

        // Add cart item booking data and price.
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_booking_data' ), 12, 4 );
        add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item_booking_price' ), 10, 1 );

        // Display formatted dates in cart.
        add_filter( 'woocommerce_get_item_data', array( $this, 'display_booking_dates_in_cart' ), 10, 2 );

        // Override prduct price in cart with booking price.
        add_filter( 'woocommerce_product_get_price', array( $this, 'set_cart_item_booking_price' ), 20, 2 );
        add_filter( 'woocommerce_product_variation_get_price', array( $this, 'set_cart_item_booking_price' ), 20, 2 );
        
    }

    /**
    *
    * Check that dates are set before adding to cart.
    *
    * @param bool - $passed
    * @param int - $product_id
    * @param int - $quantity
    * @param (optional) int - $variation_id
    * @param (optional) array - $variations
    * @return bool - $passed
    *
    **/
    public function add_to_cart_validation( $passed = true, $product_id, $quantity, $variation_id = '', $variations = array() ) {

        if ( ! $passed ) {
            return false;
        }

        $id = empty( $variation_id ) ? $product_id : $variation_id;

        $_product = wc_get_product( $id );

        if ( ! $_product ) {
            return false;
        }
        
        // If product is bookable
        if ( wceb_is_bookable( $_product ) ) {

            // Get booking session
            $booking_session = WC()->session->get( 'booking' );
            
            if ( isset( $booking_session[$id] ) && ! empty( $booking_session[$id] ) ) {

                $dates_format = wceb_get_product_number_of_dates_to_select( $_product );

                // If product is bundled, get the "parent" product data
                if ( isset( $booking_session[$id]['grouped_by'] ) ) {
                    $parent_product = wc_get_product( $booking_session[$id]['grouped_by'] );
                    $dates_format   = wceb_get_product_number_of_dates_to_select( $parent_product );
                }

                if ( $dates_format === 'one' ) {

                    // If start is not set, return false.
                    if ( ! isset( $booking_session[$id]['start'] ) ) {
                        wc_add_notice( esc_html__( 'Please choose a date', 'woocommerce-easy-booking-system' ), 'error' );
                        $passed = false;
                    }

                    // If end is set, return false.
                    if ( isset( $booking_session[$id]['end'] ) ) {
                        wc_add_notice( esc_html__( 'You can only select one date', 'woocommerce-easy-booking-system' ), 'error' );
                        $passed = false;
                    }

                } else if ( $dates_format === 'two' ) {
            
                    // If start and/or end are not set, return false.
                    if ( ! isset( $booking_session[$id]['start'] ) || ! isset( $booking_session[$id]['end'] ) ) {
                        wc_add_notice( esc_html__( 'Please choose two dates', 'woocommerce-easy-booking-system' ), 'error' );
                        $passed = false;
                    }

                }

            } else {

                // If booking session is not set for the product, return false.
                if ( $dates_format === 'one' ) {
                    wc_add_notice( esc_html__( 'Please choose a date', 'woocommerce-easy-booking-system' ), 'error' );
                } else if ( $dates_format === 'two' ) {
                    wc_add_notice( esc_html__( 'Please choose two dates', 'woocommerce-easy-booking-system' ), 'error' );
                }
                
                $passed = false;

            }

        }

        return $passed;

    }

    /**
    *
    * Get cart item booking data from session.
    *
    * @param array $session_data
    * @param array $values - cart_item_meta
    * @return array $session_data
    *
    **/
    function get_cart_item_booking_data_from_session( $session_data, $values ) {

        if ( isset( $values['_booking_price'] ) ) {
            $session_data['_booking_price'] = $values['_booking_price'];
        }

        if ( isset( $values['_booking_duration'] ) ) {
            $session_data['_booking_duration'] = $values['_booking_duration'];
        }

        // Start date yyyy-mm-dd
        if ( isset( $values['_booking_start_date'] ) ) {
            $session_data['_booking_start_date'] = $values['_booking_start_date'];
        }

        // End date yyyy-mm-dd
        if ( isset( $values['_booking_end_date'] ) ) {
            $session_data['_booking_end_date'] = $values['_booking_end_date'];
        }

        $this->add_cart_item_booking_price( $session_data );
        
        return $session_data;

    }

    /**
    *
    * Add cart item booking data.
    *
    * @param array - $cart_item_meta
    * @param int - $product_id
    * @param int - $variation_id
    * @param int - $quantity
    * @return array $cart_item_meta
    *
    **/
    function add_cart_item_booking_data( $cart_item_meta, $product_id, $variation_id, $quantity ) {

        // Get session
        $booking_session = WC()->session->get( 'booking' );
        $id              = empty( $variation_id ) ? $product_id : $variation_id;

        if ( isset( $booking_session[$id] ) && ! empty( $booking_session[$id] ) ) {

            if ( isset( $booking_session[$id]['new_price'] ) ) {
                $cart_item_meta['_booking_price'] = wc_format_decimal( $booking_session[$id]['new_price'] );
            }

            if ( isset( $booking_session[$id]['duration'] ) ) {
                $cart_item_meta['_booking_duration'] = absint( $booking_session[$id]['duration'] );
            }

            // Start date yyyy-mm-dd
            if ( isset( $booking_session[$id]['start'] ) ) {
                $cart_item_meta['_booking_start_date'] = sanitize_text_field( $booking_session[$id]['start'] );
            }

            // End date yyyy-mm-dd
            if ( isset( $booking_session[$id]['end'] ) ) {
                $cart_item_meta['_booking_end_date'] = sanitize_text_field( $booking_session[$id]['end'] );
            }

            // Reset session for this product ID
            unset( $booking_session[$id] );
            WC()->session->set( 'booking', $booking_session );

        }

        return apply_filters( 'easy_booking_add_cart_item_booking_data', $cart_item_meta );

    }

    /**
    *
    * Set cart item booking price.
    *
    * @param array $cart_item
    * @return array $cart_item
    *
    **/
    function add_cart_item_booking_price( $cart_item ) {

        if ( isset( $cart_item['_booking_price'] ) && $cart_item['_booking_price'] >= 0 ) {

            $cart_item['_booking_price'] = apply_filters(
                'easy_booking_set_booking_price',
                $cart_item['_booking_price'],
                $cart_item
            );

            // Filter for third-party plugins.
            $cart_item = apply_filters( 'easy_booking_cart_item', $cart_item );

            // Set product price.
            $cart_item['data']->set_price( (float) $cart_item['_booking_price'] );

            // Store booking price for later.
            $cart_item['data']->new_booking_price = (float) $cart_item['_booking_price'];

        }

        return $cart_item;

    }   

    /**
    *
    * Override any filters on the price with the booking price once the item is in the cart.
    *
    * @param str $price
    * @param WC_Product $_product
    * @return str $price
    *
    **/
    function set_cart_item_booking_price( $price, $_product ) {

        if ( isset( $_product->new_booking_price ) && ! empty( $_product->new_booking_price ) ) {
            $price = $_product->new_booking_price;
        }

        return $price;

    }
 
    /**
    *
    * Display formatted dates in cart.
    *
    * @param array $other_data
    * @param array $cart_item
    * @return array $other_data
    *
    **/
    function display_booking_dates_in_cart( $other_data, $cart_item ) {

        // For bundles, only display dates on parent product.
        if ( isset( $cart_item['bundled_by'] ) ) {
            return $other_data;
        }

        if ( isset( $cart_item['_booking_start_date'] ) && ! empty ( $cart_item['_booking_start_date'] ) ) {

            $other_data[] = array(
                'name'  => esc_html( wceb_get_start_text( $cart_item['data'] ) ),
                'value' => date_i18n( get_option( 'date_format' ), strtotime( $cart_item['_booking_start_date'] ) )
            );

        }

        if ( isset( $cart_item['_booking_end_date'] ) && ! empty ( $cart_item['_booking_end_date'] ) ) {

            $other_data[] = array(
                'name'  => esc_html( wceb_get_end_text( $cart_item['data'] ) ),
                'value' => date_i18n( get_option( 'date_format' ), strtotime( $cart_item['_booking_end_date'] ) )
            );

        }

        return $other_data;

    }

}

new Cart();

endif;