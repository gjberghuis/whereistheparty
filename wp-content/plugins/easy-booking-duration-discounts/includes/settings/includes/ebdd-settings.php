<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_setting(
	'easy_booking_discounts_settings',
	'easy_booking_discounts_settings', 
	array( $this, 'sanitize_values' )
);

// Don't show the license key field on multisite because it is on the network site
if ( ! is_multisite() ) {

	add_settings_field(
		'easy_booking_discounts_license_key',
		__( 'License Key', 'easy_booking_discounts' ),
		array( $this, 'easy_booking_discounts_license_key' ),
		'easy_booking_discounts_settings',
		'easy_booking_discounts_main_settings'
	);

}

add_settings_section(
	'easy_booking_discounts_main_settings',
	__( 'Settings', 'easy_booking_discounts' ),
	array( $this, 'easy_booking_discounts_section_general' ),
	'easy_booking_discounts_settings'
);

add_settings_field(
	'easy_booking_discounts_mode',
	__( 'Discounts mode', 'easy_booking_discounts' ),
	array( $this, 'easy_booking_discounts_mode' ),
	'easy_booking_discounts_settings',
	'easy_booking_discounts_main_settings'
);

add_settings_field(
	'easy_booking_discounts_display',
	__( 'Display discounts?', 'easy_booking_discounts' ),
	array( $this, 'easy_booking_discounts_display' ),
	'easy_booking_discounts_settings',
	'easy_booking_discounts_main_settings'
);

add_settings_field(
	'easy_booking_discounts_display_mode',
	__( 'Display mode', 'easy_booking_discounts' ),
	array( $this, 'easy_booking_discounts_display_mode' ),
	'easy_booking_discounts_settings',
	'easy_booking_discounts_main_settings'
);

add_settings_field(
	'easy_booking_discounts_global_discounts',
	__( 'Global discounts', 'easy_booking_discounts' ),
	array( $this, 'easy_booking_discounts_global_discounts' ),
	'easy_booking_discounts_settings',
	'easy_booking_discounts_main_settings'
);