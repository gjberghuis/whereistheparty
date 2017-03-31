<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Deprecated
add_filter( 'easy_booking_get_new_item_price', 'ebdd_get_discounted_price', 10, 4 );
add_filter( 'easy_booking_get_new_grouped_item_price', 'ebdd_get_discounted_price', 10, 4 );

// Easy Booking 2.1.0 new filter
add_filter( 'easy_booking_two_dates_price', 'ebdd_get_discounted_price', 10, 4 );

/**
*
* Calculates and returns the discounted price on the front-end
*
* @param str $new_price - Product price for the selected duration, without discounts
* @param obj $product - Product object
* @param obj $_product - Product or variation or children object
* @param array $booking_data - Booking data
* @return str $new_price - Discounted price for the selected duration
*
**/
function ebdd_get_discounted_price( $new_price, $product, $_product, $booking_data ) {

    // Backward compatibility
    if ( is_array( $booking_data ) && isset( $booking_data['duration'] ) ) {
        $duration = $booking_data['duration'];
    } else {
        $duration = $booking_data;
    }
    

    $discount_data = array();
    
    if ( ! $_product ) {
        return;
    }

    // WooCommerce Product Bundles compatibility
    if ( $product->is_type( 'bundle' ) ) {

        if ( $product->id !== $_product->id ) {

            $apply_discounts = get_post_meta( $_product->id, '_apply_ebdd_discounts', true );

            if ( empty( $apply_discounts ) ) {
                return $new_price;
            }

        }

    }

    if ( $_product->is_type('variation') ) {

        // Get variation discounts
        $discount_data = ebdd_get_product_discounts( $_product->variation_id );

    } else if ( ! $_product->is_type( 'grouped' ) ) {

        // Get product discounts
        $discount_data = ebdd_get_product_discounts( $_product->id );

    }

    $settings         = get_option('easy_booking_discounts_settings'); // Plugin settings
    $discounts_mode   = $settings['easy_booking_discounts_mode']; // Normal or cumulative

    // If no discount is set, return the full price
    if ( empty( $discount_data ) ) {
        return $new_price;
    }
    
    $cumulated_discount = 0;
    $froms = array(); // Array of all the "From"
    $tos = array(); // Array of all the "To"
    $discounted_days = array(); // Array of all days including a discount
    if ( ! empty( $discount_data ) ) foreach ( $discount_data as $discount ) {

        // If discount is not complete, continue
        if ( empty( $discount['action'] ) || ( empty( $discount['amount'] ) && $discount['amount'] != '0' ) || empty( $discount['type'] ) ) {
            continue;
        }

        if ( $discounts_mode === 'cumulative' ) { // Cumulative mode

            $froms[] = $discount['from'];
            $tos[]   = $discount['to'];

            // If discount end is not 0
            if ( $discount['to'] != 0 ) {
                $from = $discount['from'] == 0 ? 1 : $discount['from'];
                for ( $i = $from; $i <= $discount['to']; $i++ ) {
                    $discounted_days[] = $i;
                }
            } else { // If discount end is 0
                $discounted_days[] = $discount['from'];
            }

            if ( $discount['to'] == 0 && $duration >= $discount['from'] ) {
                $d = $duration - $discount['from'];
                $d += 1;

                $applied_discount = ebdd_calc_discount( $discount, $new_price, $duration, $d );
                $cumulated_discount += $applied_discount;
            }

            if ( $discount['to'] != 0 && $duration >= $discount['to'] ) {
                $d = $discount['to'] - $discount['from'];

                if ( $discount['from'] != 0 ) {
                    $d += 1;
                }

                $applied_discount = ebdd_calc_discount( $discount, $new_price, $duration, $d );
                $cumulated_discount += $applied_discount;
            }

            if ( $duration >= $discount['from'] && $duration < $discount['to'] ) {
                $d = $duration - $discount['from'];
                
                if ( $discount['from'] != 0 )
                    $d += 1;

                $applied_discount = ebdd_calc_discount( $discount, $new_price, $duration, $d );
                $cumulated_discount += $applied_discount;
            }

        } else { // Normal mode

            if ( $discount['to'] == 0 ) { // If "To" is 0

                if ( ! ( $discount['from'] <= $duration ) ) {
                    continue;
                }

            } else { // If max. is set

                if ( $discount['from'] > $duration ) {
                    continue;
                }

                if ( $discount['to'] < $duration ) {
                    continue;
                }

            }

            $new_price = ebdd_calc_discount( $discount, $new_price, $duration );

        }

    }

    if ( $discounts_mode === 'cumulative' ) {

        if ( min( $tos ) == 0 && max( $tos ) == 0 && $duration < min( $froms ) ) {
            $my_values = range( 1, $duration );
        } else if ( min( $tos ) == 0 && $duration > max( $tos ) && $duration > max( $froms ) ) {
            $my_values = range( 1, max( $froms ) );
        } else {
            $my_values = range( 1, $duration );
        }

        // Get the days without discounts - i.e full price
        $full_days = count( array_diff( $my_values, $discounted_days ) );

        if ( $full_days > 0 ) {
            $applied_discount = ebdd_calc_discount( $discount = false, $new_price, $duration, $full_days );
            $cumulated_discount += $applied_discount;
        }

        if ( $cumulated_discount >= 0 ) {
            $new_price = $cumulated_discount;
        }
        
    }

    return wc_format_decimal( $new_price );

}

/**
*
* Calculates the discounted price
*
* @param array $discount - The discount applied
* @param int $new_price - The price to apply the discount
* @param int $duration - The selected duration
* @param int $d - The modified selected duration
* @return str $new_price - Discounted price for the selected duration
*
**/
function ebdd_calc_discount( $discount, $new_price, $duration, $d = false ) {

    $item_price = $new_price / $duration; // Price for one day

    if ( ! $d ) {
        $d = $duration;
    }

    if ( ! $discount ) {

        $new_price = $item_price * $d;

    } else if ( $d <= 0 ) {

        $new_price = $item_price * $duration;

    } else {

        $discount_action = $discount['action'];
        $discount_amount = (float) $discount['amount'];
        $discount_type   = $discount['type'];

        if ( $discount_type === 'ebdd_single_percent' ) {

            $applied_discount = $item_price * ( $discount_amount / 100 );
            $discounted_item_price = $discount_action === 'ebdd_surcharge' ? ( $item_price + $applied_discount ) : ( $item_price - $applied_discount );
            $new_price = $discounted_item_price * $d;

        } else if ( $discount_type === 'ebdd_percent' ) {

            $applied_discount = ( $item_price * $d ) * ( $discount_amount / 100 );
            $new_price = $discount_action === 'ebdd_surcharge' ? ( ( $item_price * $d ) + $applied_discount ) : ( ( $item_price * $d ) - $applied_discount );

        } else if ( $discount_type === 'ebdd_single_fixed' ) {

            $discounted_item_price = $discount_action === 'ebdd_surcharge' ? ( $item_price + $discount_amount ) : ( $item_price - $discount_amount );
            $new_price = $discounted_item_price * $d;

        } else if ( $discount_type === 'ebdd_fixed' ) {

            $new_price = $discount_action === 'ebdd_surcharge' ? ( ( $item_price * $d ) + $discount_amount ) : ( ( $item_price * $d ) - $discount_amount );

        }

        if ( $new_price < 0 ) {
            $new_price = 0;
        }

    }

    return $new_price;
}

/**
*
* Gets the HTML to display the product discounts.
*
* @param int $product_id
* @return mixed | str $output or array $discounts_html (for variable products)
*
**/
function ebdd_get_product_discounts_html( $product_id, $child_id = false ) {
    $product = wc_get_product( $product_id );

    if ( ! $product ) {
    	return;
    }

    if ( $product->is_type('variable') || ( $product->is_type( 'bundle' ) && true === $product->contains( 'priced_individually' ) ) ) {

        if ( $product->is_type('variable') ) {

            $variation_ids = $product->get_children();

        } else if ( $product->is_type( 'bundle' ) ) {

            $bundled = $product->get_bundled_item_ids();
            $variation_ids = array();

            if ( $bundled ) foreach ( $bundled as $bundled_item_id ) {

                $bundled_item = $product->get_bundled_item( $bundled_item_id );
                $_product = $bundled_item->product;

                if ( $_product->is_type( 'variable' ) ) {

                    if ( $_product->get_children() ) foreach ( $_product->get_children() as $child ) {
                        $variation_ids[] = $child;  
                    }
                    
                }
                
            }

        }
        
        $discounts_html = array();
        if ( ! empty( $variation_ids ) ) foreach ( $variation_ids as $variation_id ) {
            $output = ebdd_generate_product_discounts_html( $variation_id );
            $discounts_html[$variation_id] = $output;
        }

        if ( $product->is_type( 'bundle' ) ) {
            $discounts_html[$product_id] = ebdd_generate_product_discounts_html( $product_id, $child_id );
        }   

    } else {

        $discounts_html[$product_id] = ebdd_generate_product_discounts_html( $product_id );

    }

    return $discounts_html;

}

/**
*
* Generates the HTML to display the product discounts.
*
* @param int $product_id
* @return str $output
*
**/
function ebdd_generate_product_discounts_html( $product_id, $variation_id = '' ) {
    
    $_product = wc_get_product( $product_id );

    $id = ( empty( $variation_id ) ) ? $product_id : $variation_id;

    $product = wc_get_product( $id ); // Product or variation

    if ( ! $product ) {
        return;
    }

    // Get Easy Booking settings
    $wceb_settings = get_option('easy_booking_settings');

    // Get Easy Booking: Duration Discounts settings
    $ebdd_settings = get_option('easy_booking_discounts_settings');
    
    // Get product discounts
    $discount_data = ebdd_get_product_discounts( $id );

    $output = '';
    if ( ! empty( $discount_data ) ) :

        $mode               = $wceb_settings['easy_booking_calc_mode'];
        $display_mode       = $ebdd_settings['easy_booking_discounts_display_mode'];
        $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
        $prices_include_tax = get_option('woocommerce_prices_include_tax');

        $item_prices = wceb_get_product_price( $_product, $product, false, 'array' );

        $calc_mode = wceb_get_price_html( $product );

        $booking_duration = wceb_get_product_booking_duration( $product );
        $custom_booking_duration = wceb_get_product_custom_booking_duration( $product );

        foreach ( $discount_data as $discount ) :

            $price = $item_prices['price'];

            if ( isset( $item_prices['regular_price'] ) && ! empty( $item_prices['regular_price'] ) ) {
                $regular_price = $item_prices['regular_price'];
            }

            // If the duration is custom, multiply from and to to get the number of days
            if ( $booking_duration === 'custom' ) {
                
                $discount['from'] *= $custom_booking_duration;
                $discount['to'] *= $custom_booking_duration;

                if ( $discount['from'] != $discount['to'] ) {
                    $discount['from'] -= $custom_booking_duration;
                    $discount['from'] += 1; 
                }

                if ( $discount['from'] <= 0 ) {
                    $discount['from'] = 1;
                }
                
            }

            if ( $booking_duration === 'weeks' ) {
                $mode_display = _n( 'week', 'weeks', $discount['to'], 'easy_booking_discounts' );
            } else {
                $mode_display = $mode === 'nights' ? _n( 'night', 'nights', $discount['to'], 'easy_booking_discounts' ) : _n( 'day', 'days', $discount['to'], 'easy_booking_discounts' );
            }

            $discount_action = $discount['action'] === 'ebdd_surcharge' ? '+' : '-';
            $discount_type   = $discount['type'] === 'ebdd_single_percent' || $discount['type'] === 'ebdd_percent' ? '%' : '';
            $discount_amount = esc_html( $discount['amount'] );

            if ( $discount['type'] === 'ebdd_single_fixed' || $discount['type'] === 'ebdd_fixed' ) {
                if ( $prices_include_tax === 'no' ) {
                    $discount_amount = $product->get_price_excluding_tax( 1, $discount_amount );
                } else if ( $prices_include_tax === 'yes' ) {
                    $discount_amount = $product->get_price_including_tax( 1, $discount_amount );
                }
            }

            // If the discount amount is 0, don't display it.
            if ( $discount_amount === '0' ) {
                continue;
            }

            if ( $discount['type'] === 'ebdd_single_percent' ) {

                if ( $discount['action'] === 'ebdd_surcharge' ) {

                    $price += ( $item_prices['price'] * ( $discount_amount / 100 ) );

                    if ( isset( $regular_price ) ) {
                        $regular_price += ( $item_prices['regular_price'] * ( $discount_amount / 100 ) );
                    }

                } else {

                    $price -= ( $item_prices['price'] * ( $discount_amount / 100 ) );

                    if ( isset( $regular_price ) ) {
                        $regular_price -= ( $item_prices['regular_price'] * ( $discount_amount / 100 ) );
                    }

                }

            } else if ( $discount['type'] === 'ebdd_single_fixed' ) {

                if ( $discount['action'] === 'ebdd_surcharge' ) {

                    $price += $discount_amount;

                    if ( isset( $regular_price ) ) {
                        $regular_price += $discount_amount;
                    }

                } else {

                    $price -= $discount_amount;

                    if ( isset( $regular_price ) ) {
                        $regular_price -= $discount_amount;
                    }

                }

            }

            if ( $price < 0 ) {
                $price = 0;
            }

            if ( isset( $regular_price ) && $regular_price < 0 ) {
                $regular_price = 0;
            }
            
            if ( $discount['type'] === 'ebdd_single_fixed' || $discount['type'] === 'ebdd_fixed' ) {

                // Get single or total fixed discount amount including or excluding tax
                $discount_amount = $tax_display_mode === 'incl' ? $product->get_price_including_tax( 1, $discount_amount ) : $product->get_price_excluding_tax( 1, $discount_amount );

                // Format discount amount
                $discount_amount = wc_price( $discount_amount );

            }

            // Output
            $output .= '<span class="ebdd_discount">';

            if ( $discount['to'] == 0 ) {
                $output .= $discount['from'] . ' ' . $mode_display . '+ : ';
            } else if ( $discount['from'] === $discount['to'] ) {
                $output .= $discount['from'] . ' ' . $mode_display . ' : ';
            } else {
                $output .= $discount['from'] . '-' . $discount['to'] . ' ' . $mode_display . ' : ';
            }

            if ( $discount['type'] === 'ebdd_percent' || $discount['type'] === 'ebdd_fixed' ) {
                $output .= $discount_action . $discount_amount . $discount_type . __( ' on total', 'easy_booking_discounts' );
            } else {

                if ( $display_mode === 'price' ) {
                    
                    $price = $tax_display_mode === 'incl' ? $product->get_price_including_tax( 1, $price ) : $product->get_price_excluding_tax( 1, $price );

                    if ( isset( $regular_price ) ) {
                        $regular_price = $tax_display_mode === 'incl' ? $product->get_price_including_tax( 1, $regular_price ) : $product->get_price_excluding_tax( 1, $regular_price );

                        $output .= '<del>' . wc_price( $regular_price ) . '</del> ' . wc_price( $price ) . $calc_mode;
                    } else {
                        $output .= wc_price( $price ) . $calc_mode;
                    } 

                } else {
                    $output .= $discount_action . $discount_amount . $discount_type . $calc_mode;
                }
                
            }

            $output .= '</span></br>';

        endforeach;

    endif;

    return $output;

}

/**
*
* Merges product discounts and global discounts, removing overlapping discounts.
*
* @param array $product_discounts - Product discounts
* @param array $global_discounts - Global discounts
* @return array $discount_data - Merged and filtered discounts
*
**/
function ebdd_merge_product_and_global_discounts( array $product_discounts, array $global_discounts ) {

    $discount_data = array();
    if ( ! empty( $product_discounts ) && ! empty( $global_discounts ) ) {

        $discount_data = array_merge( $product_discounts, $global_discounts ); // Merge product discounts and global discounts

        $discount_days = array();
        foreach ( $discount_data as $index => $discount ) :
            
            $data = ebdd_check_overlapping_discounts( $discount, $index, $discount_days ); // Check for overlapping discounts
            $discount_days = $data['days'];

            if ( true === $data['overlap'] ) {
                unset( $discount_data[$index] ); // If a similar discount is already set, remove it
            }

        endforeach;
        
    } else if ( ! empty( $product_discounts ) ) {
        $discount_data = $product_discounts;
    } else if ( ! empty( $global_discounts ) ) {
        $discount_data = $global_discounts;
    }

    // Sort discounts
    usort( $discount_data, 'ebdd_sort_discounts' ); // Sort discounts from first to last

    return $discount_data;
}

/**
*
* Sorts a discount table from first to last
*
* @param array $a
* @param array $b
* @return bool
*
**/
function ebdd_sort_discounts( $a, $b ) {

    if ( $a['from'] == $b['from'] ) {
        return 0;
    }

    return ( $a['from'] < $b['from'] ) ? -1 : 1;
}

/**
*
* Formats and sanitizes the discounts.
*
* @param array $discounts_to_save - Discounts to save
* @param int $post_id
* @return array $discount_data - Discounts sanitized and formatted
*
**/
function ebdd_format_discounts_for_saving( array $discounts_to_save, $post_id = false ) {
    $discount_actions = $discounts_to_save['actions'];
    $discount_amounts = $discounts_to_save['amounts'];
    $discount_types   = $discounts_to_save['types'];
    $discount_from    = $discounts_to_save['from'];
    $discount_to      = $discounts_to_save['to'];

    $discounts_number = count( $discount_amounts );

    if ( $discounts_number <= 0 ) {
        return;
    }

    if ( $post_id && empty( $discount_amounts ) && empty( $discount_from ) && empty( $discount_to ) ) {
        delete_post_meta( $post_id, '_discount_data' );
        return;
    }

    $discounts = array();
    for ( $i = 0; $i < $discounts_number; $i++ ) {

        if ( empty( $discount_actions[$i] ) ) { // If no amount is set, skip the discount
            WC_Admin_Meta_Boxes::add_error( __( 'You need to set the discount action.', 'easy_booking_discounts' ) );
            continue;
        }

        $discount_amount = (string) wc_clean( $discount_amounts[$i] );

        if ( empty( $discount_amount ) && $discount_amount !== '0' ) { // If no amount is set, skip the discount
            WC_Admin_Meta_Boxes::add_error( __( 'You need to set the discount amount.', 'easy_booking_discounts' ) );
            continue;
        }

        if ( empty( $discount_types[$i] ) ) { // If no type is set, skip the discount
            WC_Admin_Meta_Boxes::add_error( __( 'You need to set the discount type.', 'easy_booking_discounts' ) );
            continue;
        }

        if ( ! empty( $discount_actions[$i] ) ) {
            $discounts[$i]['action'] = sanitize_text_field( $discount_actions[$i] );
        }

        $discounts[$i]['amount'] = wc_format_decimal( $discount_amount );

        if ( ! empty( $discount_types[$i] ) ) {
            $discounts[$i]['type'] = sanitize_text_field( $discount_types[$i] );
        }

        $discounts[$i]['from'] = ! empty( $discount_from[$i] ) ? absint( $discount_from[$i] ) : 0;
        $discounts[$i]['to']   = ! empty( $discount_to[$i] ) ? absint( $discount_to[$i] ) : 0;
    }
    
    $discount_days = array();
    $discount_data = array();
    if ( ! empty( $discounts ) ) {

        foreach ( $discounts as $index => $discount ) {

            // Check if no "From" is superior to "To"
            if ( ( $discount['from'] > 0 && $discount['to'] > 0 ) && $discount['from'] > $discount['to'] ) {
                WC_Admin_Meta_Boxes::add_error( __( 'Discount start day must be inferior to discount end day.', 'easy_booking_discounts' ) );
                continue;
            }

            // Check for overlapping discounts, and remove them if necessary
            $data = ebdd_check_overlapping_discounts( $discount, $index, $discount_days );
            $discount_days = $data['days'];

            if ( true === $data['overlap'] ) {
                WC_Admin_Meta_Boxes::add_error( __( 'Discounted days can\'t overlap.', 'easy_booking_discounts' ) );
                continue;
            }

            $discount_data[] = array(
                'action' => $discount['action'],
                'amount' => $discount['amount'],
                'type'   => $discount['type'],
                'from'   => $discount['from'],
                'to'     => $discount['to']
            );

        }
    }

    return $discount_data;
}

/**
*
* Check for overlapping discounts.
*
* @param array $discount - The checked discount
* @param int $index - Index of the discount on the checked array
* @param array $discount_days - The days which already have a discount applied.
* @return array $data - 'overlap' (true or false) and 'days' ($discount_days) updated
*
**/
function ebdd_check_overlapping_discounts( $discount, $index, $discount_days ) {
    $days = array();
    $overlap = false;

    if ( $discount['to'] == 0 ) {
        $days[] = $discount['from'];
        $days[] = $discount['to'];
    } else {
        for ( $day = $discount['from']; $day <= $discount['to']; $day++ ) {
            $days[] = $day;
        }
    }

    $discount_days[] = $days;

    foreach ( $days as $day ) :

        foreach ( $discount_days as $key => $discounted_days ) { // Loop through discounts

            if ( $key < $index ) {

                // If end is 0 and start is inferior to other discounts
                if ( $day == 0 && $days[0] < end( $discounted_days ) ) {

                    $overlap = true;

                } else if ( end( $discounted_days ) == 0 ) {

                    if ( $day == 0  && $days[0] < reset( $discounted_days ) ) {
                        $overlap = true;
                    }

                    if ( $day >= reset( $discounted_days ) ) {
                        $overlap = true;
                    }

                } else if ( reset( $discounted_days ) == 0 ) {

                    if ( in_array( reset( $days ), $discounted_days ) ) {
                        $overlap = true;
                    }

                } else if ( in_array( $day, $discounted_days ) ) { // If value is present in another discount

                    $overlap = true;

                }
            }

        }

    endforeach;

    return $data = array(
        'overlap' => $overlap,
        'days'    => $discount_days,
    );
}

/**
*
* Get product or variation discounts.
* Merge product, parent product (for variations) and global discounts.
*
* @param int $product_id
* @return array $discount_data - The product discounts
*
**/
function ebdd_get_product_discounts( $product_id ) {

    $discount_data = array();

    $product = wc_get_product( $product_id );

    if ( ! $product ) {
        return;
    }

    // Get plugin settings
    $ebdd_settings = get_option('easy_booking_discounts_settings');

    // Get product discounts
    $product_discounts = get_post_meta( $product_id, '_discount_data', true );

    if ( empty( $product_discounts ) ) {
        $product_discounts = array();
    }

    if ( $product->is_type( 'variation' ) ) {

        $parent = $product->parent;
        $parent_discounts = get_post_meta( $parent->id, '_discount_data', true );

        // Merge variation and parent product discounts
        if ( isset( $parent_discounts ) && ! empty( $parent_discounts ) ) {
            $product_discounts = ebdd_merge_product_and_global_discounts( $product_discounts, $parent_discounts );
        }

    }

    // Get global discounts
    $global_discounts = (array) $ebdd_settings['easy_booking_global_discounts'];
    
    // Merge product and global discounts
    $discount_data = ebdd_merge_product_and_global_discounts( $product_discounts, $global_discounts );

    return $discount_data;

}