<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="ebdd-discounts">

	
    <?php 

    //Table args
    $args = array(
    	'table_classes' => 'wp-list-table widefat striped',
    	'content' => 'discount',
    	'body_classes' => 'ebdd-table'
    );

    // Display table
    wceb_settings_table( $discounts, $columns, $args );

    ?>

</div>

<p class="description">

	<?php
		_e( 'These discounts will be applied to all your products. They will be overriden by any overlapping discount set on each product. E.g:', 'easy_booking_discounts');
	?>

</p>

<table class="description_table">

	<thead>

		<tr>

			<th>&nbsp;</th>
			<th><?php _e( 'Global discounts', 'easy_booking_discounts' ); ?></th>
			<th><?php _e( 'Product discount', 'easy_booking_discounts' ); ?></th>
			<th><?php _e( 'Applied discounts', 'easy_booking_discounts' ); ?></th>

		</tr>

	</thead>

	<tbody>

		<tr>

			<th><?php _e( 'Discount #1', 'easy_booking_discounts' ); ?></th>
			<td><?php _e( 'Reduction', 'easy_booking_discounts' ); ?> | 10 | <?php _e( '1 day &#37; discount', 'easy_booking_discounts' ); ?> | 10 | 20</td>
			<td><?php _e( 'Surcharge', 'easy_booking_discounts' ); ?> | 20 | <?php _e( '1 day &#37; discount', 'easy_booking_discounts' ); ?> | 12 | 20</td>
			<td><?php _e( 'Surcharge', 'easy_booking_discounts' ); ?> | 20 | <?php _e( '1 day &#37; discount', 'easy_booking_discounts' ); ?> | 12 | 20</td>

		</tr>

		<tr>

			<th><?php _e( 'Discount #2', 'easy_booking_discounts' ); ?></th>
			<td><?php _e( 'Reduction', 'easy_booking_discounts' ); ?> | 20 | <?php _e( '1 day &#37; discount', 'easy_booking_discounts' ); ?> | 21 | 0</td>
			<td>&nbsp;</td>
			<td><?php _e( 'Reduction', 'easy_booking_discounts' ); ?> | 20 | <?php _e( '1 day &#37; discount', 'easy_booking_discounts' ); ?> | 21 | 0</td>

		</tr>

	</tbody>

</table>