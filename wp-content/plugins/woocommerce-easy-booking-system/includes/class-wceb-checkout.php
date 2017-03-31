<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCEB_Checkout' ) ) :

class WCEB_Checkout {

    public function __construct() {

        add_action( 'woocommerce_add_order_item_meta', array( $this, 'wceb_add_order_meta' ), 10, 2 );
        add_filter( 'woocommerce_order_items_meta_display', array( $this, 'wceb_display_booking_item_meta' ), 10, 2 );
        add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'wceb_hide_formatted_date' ), 10, 1 );

    }

    /**
    *
    * Adds booked dates to the order item
    *
    * @param int $item_id
    * @param array $values - 
    *
    **/
    public function wceb_add_order_meta( $item_id, $values ) {

        if ( ! empty( $values['_start_date'] ) ) {
            wc_add_order_item_meta( $item_id, '_ebs_start_display', sanitize_text_field( $values['_start_date'] ) );
        }

        if ( ! empty( $values['_end_date'] ) ) {
            wc_add_order_item_meta( $item_id, '_ebs_end_display', sanitize_text_field( $values['_end_date'] ) );
        }

        if ( ! empty( $values['_ebs_start'] ) ) {
            wc_add_order_item_meta( $item_id, '_ebs_start_format', sanitize_text_field( $values['_ebs_start'] ) );
        }

        if ( ! empty( $values['_ebs_end'] ) ) {
            wc_add_order_item_meta( $item_id, '_ebs_end_format', sanitize_text_field( $values['_ebs_end'] ) );
        }

    }

    /**
    *
    * Display order item booking meta (start and (maybe) end date(s)), even if hidden
    *
    * @param str $output
    * @param WC_Order_Item $order_item
    * @param str $output
    *
    **/
    public function wceb_display_booking_item_meta( $output, $order_item ) {

        $order_item_meta = $order_item->meta;
        $product = $order_item->product;

        $start_text = esc_html( apply_filters( 'easy_booking_start_text', __( 'Start', 'easy_booking' ), $product ) );
        $end_text   = esc_html( apply_filters( 'easy_booking_end_text', __( 'End', 'easy_booking' ), $product ) );

        if ( isset( $order_item_meta['_ebs_start_display'] ) ) {

            foreach ( $order_item_meta['_ebs_start_display'] as $index => $meta ) {
                $output .= '<dl class="variation">' . wp_kses_post( $start_text . ': ' . $meta ) . '</dl>';
            }

        }

        if ( isset( $order_item_meta['_ebs_end_display'] ) ) {

            foreach ( $order_item_meta['_ebs_end_display'] as $index => $meta ) {
                $output .= '<dl class="variation">' . wp_kses_post( $end_text . ': ' . $meta ) . '</dl>';
            }

        }
        
        return $output;

    }
    
    /**
    *
    * Hides dates on the order page (to display a custom form instead)
    *
    * @param array $item_meta - Hidden values
    * @return array $item_meta
    *
    **/
    public function wceb_hide_formatted_date( $item_meta ) {

        $item_meta[] = '_ebs_start_display';
        $item_meta[] = '_ebs_end_display';
        $item_meta[] = '_ebs_start_format';
        $item_meta[] = '_ebs_end_format';

        return $item_meta;

    }

}

return new WCEB_Checkout();

endif;