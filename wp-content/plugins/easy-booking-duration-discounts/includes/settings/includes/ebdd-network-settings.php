<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_settings_section(
	'easy_booking_discounts_multisite_settings',
	__( 'Easy Booking : Duration Discounts Settings', 'easy_booking_discounts' ),
	array( $this, 'easy_booking_discounts_multisite_settings' ),
	'easy_booking_global_settings'
);

add_settings_field(
	'easy_booking_discounts_license_key',
	__( 'License Key', 'easy_booking_discounts' ),
	array( $this, 'easy_booking_discounts_multisite_license_key' ),
	'easy_booking_global_settings',
	'easy_booking_discounts_multisite_settings'
);