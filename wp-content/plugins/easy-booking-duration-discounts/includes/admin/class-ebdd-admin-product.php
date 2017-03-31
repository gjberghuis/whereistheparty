<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Easy_Booking_Discounts_Admin_Product' ) ) :

class Easy_Booking_Discounts_Admin_Product {

	public function __construct() {

        // Filter product types
        if ( WCEB()->allowed_types ) foreach ( WCEB()->allowed_types as $type ) {

            // If product is grouped, the discounts are set on the children (simple) products
            if ( $type !== 'grouped' ) {
                
                add_action( 'easy_booking_after_' . $type . '_booking_options', array( $this, 'ebdd_set_discounts' ), 20, 1 );
                add_action( 'woocommerce_process_product_meta_' . $type, array( $this, 'ebdd_save_product_discounts' ), 10, 1 );
            }
            
        }
        
        // Variation discounts
        add_action( 'easy_booking_after_variation_booking_options', array( $this, 'ebdd_set_variable_discounts' ), 20, 2 );

        // Save variation discounts
        add_action( 'woocommerce_save_product_variation', array( $this, 'ebdd_save_product_variation_discounts' ), 10, 2 );

        // Add product bundles options
        add_action( 'woocommerce_bundled_product_admin_config_html', array( $this, 'ebdd_bundled_product_options' ), 10, 4 );

	}

    /**
    *
    * Displays table to add discounts for simple (or custom) product
    *
    * @param obj $product
    *
    **/
    public function ebdd_set_discounts( $product ) {
        $discounts = get_post_meta( $product->id, '_discount_data', true );
        
        // Format discount amount to the right localized price format
        if ( $discounts ) foreach (  $discounts as $index => $discount ) {
            $discounts[$index]['amount'] = wc_format_localized_price( $discount['amount'] );
        }

        include_once( 'views/html-ebdd-product-discounts.php' );
    }

    /**
    *
    * Displays table to add discounts for variable product
    *
    * @param int $loop
    * @param array $variation_data
    * @param obj $variation
    *
    **/
    public function ebdd_set_variable_discounts( $loop, $variation ) {
        $discounts = get_post_meta( $variation->ID, '_discount_data', true );

        // Format discount amount to the right localized price format
        if ( $discounts ) foreach (  $discounts as $index => $discount ) {
            $discounts[$index]['amount'] = wc_format_localized_price( $discount['amount'] );
        }

        include( 'views/html-ebdd-variable-product-discounts.php' );
    }

    public function ebdd_bundled_product_options( $loop, $product_id, $item_data, $post_id ) {

        $product = wc_get_product( $product_id );
        $apply_discounts = get_post_meta( $product_id, '_apply_ebdd_discounts', true );

        if ( ! wceb_is_bookable( $product ) ) {
            return;
        }

        ?>

        <div class="show_if_per_product_pricing">

            <div class="form-field">
                <label for="_apply_ebdd_discounts[<?php echo $loop; ?>]">
                    <?php _e( 'Apply Easy Booking: Duration Discounts pricing?', 'easy_booking_discounts' ); ?>
                </label>
                <input type="checkbox" name="_apply_ebdd_discounts[<?php echo $loop; ?>]" id="_apply_ebdd_discounts[<?php echo $loop; ?>]" class="checkbox" value="" <?php checked( $apply_discounts, 'yes' ); ?>>
                <?php echo wc_help_tip( __( 'Check to apply the product\'s duration discounts.', 'easy_booking_discounts' ) ); ?>
            </div>

        </div>

        <?php

    }

    /**
    *
    * Saves discounts for simple product
    *
    * @param int $post_id - Post ID
    *
    **/
    public function ebdd_save_product_discounts( $post_id ) {

        // Discounts
        $discounts = array(
            'actions' => isset( $_POST['_ebdd_discount_action'] ) ? $_POST['_ebdd_discount_action'] : '',
            'amounts' => isset( $_POST['_ebdd_discount_amount'] ) ? $_POST['_ebdd_discount_amount'] : '',
            'types'   => isset( $_POST['_ebdd_discount_type'] ) ? $_POST['_ebdd_discount_type'] : '',
            'from'    => isset( $_POST['_ebdd_discount_from'] ) ? $_POST['_ebdd_discount_from'] : '',
            'to'      => isset( $_POST['_ebdd_discount_to'] ) ? $_POST['_ebdd_discount_to'] : '',
        );

        // Format and sanitize discounts
        $discount_data = ebdd_format_discounts_for_saving( $discounts, $post_id );

        // Save or delete (if empty) discount data
        ( ! empty( $discount_data ) ) ? update_post_meta( $post_id, '_discount_data', $discount_data ) : delete_post_meta( $post_id, '_discount_data' );

        if ( isset( $_POST['_apply_ebdd_discounts'] ) ) {

            foreach ( $_POST['bundle_data'] as $index => $value ) {
                $id = absint( $_POST['bundle_data'][$index]['product_id'] );

                if ( isset( $_POST['_apply_ebdd_discounts'][$index] ) && $_POST['bundle_data'][$index]['priced_individually'] === '1' ) {
                    update_post_meta( $id, '_apply_ebdd_discounts', 'yes' );
                } else {
                    delete_post_meta( $id, '_apply_ebdd_discounts' );
                }
            }

        }

    }

    /**
    *
    * Saves discounts for variable product
    *
    * @param int $variation_id - Variation ID
    * @param int $v - Loop
    *
    **/
    public function ebdd_save_product_variation_discounts( $variation_id , $v ) {

        // Discounts
        $discounts = array(
            'actions' => isset( $_POST['_var_ebdd_discount_action'][$v] ) ? $_POST['_var_ebdd_discount_action'][$v] : '',
            'amounts' => isset( $_POST['_var_ebdd_discount_amount'][$v] ) ? $_POST['_var_ebdd_discount_amount'][$v] : '',
            'types'   => isset( $_POST['_var_ebdd_discount_type'][$v] ) ? $_POST['_var_ebdd_discount_type'][$v] : '',
            'from'    => isset( $_POST['_var_ebdd_discount_from'][$v] ) ? $_POST['_var_ebdd_discount_from'][$v] : '',
            'to'      => isset( $_POST['_var_ebdd_discount_to'][$v] ) ? $_POST['_var_ebdd_discount_to'][$v] : '',
        );

        // Format and sanitize discounts
        $discount_data = ebdd_format_discounts_for_saving( $discounts, $variation_id );

        // Save or delete (if empty) discount data
        ( ! empty( $discount_data ) ) ? update_post_meta( $variation_id, '_discount_data', $discount_data ) : delete_post_meta( $variation_id, '_discount_data' );

    }

}

return new Easy_Booking_Discounts_Admin_Product();

endif;