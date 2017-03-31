<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php $settings = new Easy_Booking_Discounts_Settings(); ?>

<div class="updated easy-booking-notice">
	<p>
		<?php _e( 'Save your license key to get automatic updates for Easy Booking : Duration Discounts.', 'easy_booking_discounts' ); ?>
		<form method="post" action="<?php echo admin_url(); ?>options.php">

			<?php if ( is_multisite() ) {
				settings_fields('easy_booking_global_settings');
				$settings->easy_booking_discounts_multisite_license_key();
			} else {
				settings_fields('easy_booking_discounts_settings');
				$settings->easy_booking_discounts_license_key();
			} ?>
			 
			<?php submit_button( __('Save', 'easy_booking_discounts'), 'button'); ?>

		</form>
	</p>
	<button type="button" class="notice-dismiss easy-booking-notice-close" data-notice="ebdd_license"></button>
</div>