<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Order' ) ) :

class Order {

    public function __construct() {

        // Hide default booking dates and status.
        add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_booking_data' ), 10, 1 );
        
        // Display booking dates and status.
        add_action( 'woocommerce_before_order_itemmeta', array( $this, 'display_order_item_booking_data' ), 10, 3 );

    }
    
    /**
    *
    * Hides dates on the order page (to display a custom form instead).
    *
    * @param array $item_meta - Hidden values
    * @return array $item_meta
    *
    **/
    public function hide_order_item_booking_data( $item_meta ) {

        $item_meta[] = '_booking_start_date';
        $item_meta[] = '_booking_end_date';
        $item_meta[] = '_booking_status';

        return $item_meta;

    }

    /**
    *
    * Displays booked dates and a picker form on the order page
    *
    * @param int - $item_id
    * @param WC_Order_Item - $item
    * @param WC_Product || WC_Product_Variation - $product
    *
    **/
    public function display_order_item_booking_data( $item_id, $item, $product ) {
        global $wpdb;

        if ( ! $product || is_null( $product ) ) {
            return;
        }

        $start_date     = wc_get_order_item_meta( $item_id, '_booking_start_date' );
        $end_date       = wc_get_order_item_meta( $item_id, '_booking_end_date' );
        $booking_status = wc_get_order_item_meta( $item_id, '_booking_status' );

        // Backward compatibility 2.3.0
        if ( empty( $start_date ) ) {
            $start_date = wc_get_order_item_meta( $item_id, '_ebs_start_format' );
        }

        // Backward compatibility 2.3.0
        if ( empty( $end_date ) ) {
            $end_date = wc_get_order_item_meta( $item_id, '_ebs_end_format' );
        }

        $start_date_text = wceb_get_start_text( $product );
        $end_date_text   = wceb_get_end_text( $product );

        $item_order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        
        if ( ! empty( $start_date ) ) {

            // Localized start date
            $start_date_i18n = date_i18n( get_option( 'date_format' ), strtotime( $start_date ) );

            // Localized end date
            if ( ! empty( $end_date ) ) {
                $end_date_i18n = date_i18n( get_option( 'date_format' ), strtotime( $end_date ) );
            }

            include( 'views/order-items/html-wceb-order-item-meta.php' );

        }

        if ( wceb_is_bookable( $product ) ) {

            $meta_array = array(
                'start_date_meta_id'     => '_booking_start_date',
                'end_date_meta_id'       => '_booking_end_date',
                'booking_status_meta_id' => '_booking_status'
            );

            foreach ( $meta_array as $var => $meta_name ) {

                // Check if there's already an entry in the database.
                ${$var} = $wpdb->get_var( $wpdb->prepare(
                    "SELECT `meta_id` FROM $item_order_meta_table WHERE `order_item_id` = %d AND `meta_key` LIKE %s",
                    $item_id, $meta_name
                ));

                // Otherwise create order item meta.
                if ( is_null( ${$var} ) ) {
                    ${$var} = wc_add_order_item_meta( $item_id, $meta_name, '' );
                }

            }

            include( 'views/order-items/html-wceb-edit-order-item-meta.php' );

        }

    }

}

return new Order();

endif;