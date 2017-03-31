<?php

$columns = array(
    array(
        'title'   => __( 'Action', 'easy_booking_discounts' ),
        'name'    => 'action',
        'content' => array(
            'function' => 'wceb_settings_select',
            'data'     => array(
                'name'    => '_var_ebdd_discount_action[' . $loop . '][]',
                'options' => array(
                    'ebdd_reduction' => __('Reduction', 'easy_booking_discounts'),
                    'ebdd_surcharge' => __('Surcharge', 'easy_booking_discounts')
                ),
                'echo'    => false
            )
        )
    ),
    array(
        'title'   => __( 'Amount', 'easy_booking_discounts' ),
        'name'    => 'amount',
        'content' => array(
            'function' =>'wceb_settings_input',
            'data'     => array(
                'type'              => 'text',
                'name'              => '_var_ebdd_discount_amount[' . $loop . '][]',
                'class'             => 'input_text wc_input_price',
                'placeholder'       => __( 'Discount Amount', 'easy_booking_discounts' ),
                'custom_attributes' => array(
                    'min' => 0
                ),
                'echo'              => false
            )
        )
    ),
    array(
        'title'   => __( 'Type', 'easy_booking_discounts' ),
        'tip'     => __( 'Choose whether a percent discount or a fixed price discount.', 'easy_booking_discounts' ),
        'name'    => 'type',
        'content' => array(
            'function' => 'wceb_settings_select',
            'data'     => array(
                'name'    => '_var_ebdd_discount_type[' . $loop . '][]',
                'options' => array(
                    'ebdd_single_percent' => __( '1 day &#37; discount', 'easy_booking_discounts'),
                    'ebdd_percent' => __( 'Total &#37; discount', 'easy_booking_discounts'),
                    'ebdd_single_fixed' => __( '1 day fixed discount', 'easy_booking_discounts'),
                    'ebdd_fixed' => __( 'Total fixed discount', 'easy_booking_discounts' )
                ),
                'echo'    => false
            )
        )
    ),
    array(
        'title'   => __( 'From &hellip;', 'easy_booking_discounts' ) . ' <span class="wceb_unit">' . __('days', 'easy_booking') . '</span>',
        'tip'     => __( 'Day/week/custom period to start discount. Leave 0 or empty to start at first day/week/custom period.', 'easy_booking_discounts' ),
        'name'    => 'from',
        'content' => array(
            'function' => 'wceb_settings_input',
            'data'     => array(
                'type'              => 'number',
                'name'              => '_var_ebdd_discount_from[' . $loop . '][]',
                'class'             => 'input_text',
                'placeholder'       => __( 'From &hellip; day(s)', 'easy_booking_discounts' ),
                'custom_attributes' => array(
                    'min' => 0
                ),
                'echo'              => false
            )
        )
    ),
    array(
        'title'   => __( 'To &hellip;', 'easy_booking_discounts' ) . ' <span class="wceb_unit">' . __('days', 'easy_booking') . '</span>',
        'tip'     => __( 'Day/week/custom period to end discount. Leave 0 or empty to set no limitation.', 'easy_booking_discounts' ),
        'name'    => 'to',
        'content' => array(
            'function' => 'wceb_settings_input',
            'data'     => array(
                'type'              => 'number',
                'name'              => '_var_ebdd_discount_to[' . $loop . '][]',
                'class'             => 'input_text',
                'placeholder'       => __( 'To &hellip; day(s)', 'easy_booking_discounts' ),
                'custom_attributes' => array(
                    'min' => 0
                ),
                'echo'              => false
            )
        )
    )
);

?>

<div class="form-field ebdd_product_discounts downloadable_files show_if_variation_bookable show_if_two_dates">
    <label><?php _e( 'Duration discounts or surcharges', 'easy_booking_discounts' ); ?>:</label>
    <?php $args = array( 'table_classes' => 'widefat', 'content' => 'discount', 'sortable' => true );
    wceb_settings_table( $discounts, $columns, $args ); ?>
</div>