<?php

// If uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

delete_option( 'easy_booking_discounts_settings' );
delete_option( 'easy_booking_global_settings' );
delete_option( 'easy_booking_display_notice_ebdd_license' );

// Delete db entries
global $wpdb;

$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = %s", "_discount_data" ) );