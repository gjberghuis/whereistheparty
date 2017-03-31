<div class="wrap">

	<h2><?php _e( 'Easy Booking : Duration Discounts Settings', 'easy_booking_discounts' ); ?></h2>

	<form method="post" action="options.php">

		<?php settings_fields( 'easy_booking_discounts_settings' ); ?>
		<?php do_settings_sections( 'easy_booking_discounts_settings' ); ?>

		<?php submit_button(); ?>

	</form>

</div>