(function($) {
	$(document).ready(function() {

		$('input#_bookable').change( function() {

			if ( $(this).is(':checked') ) {
				$('.show_if_bookable').show();
			} else {
				$('.show_if_bookable').hide();
				$('input.variation_is_bookable').attr('checked', false).change();
			}

			if ( $('.bookings_tab').is('.active') ) {
				$( 'ul.wc-tabs li:visible' ).eq(0).find( 'a' ).click();
			}

		}).change();

		$( '#variable_product_options' ).on( 'change', 'input.variation_is_bookable', function () {
			$( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_bookable' ).hide();
			if ( $( this ).is( ':checked' ) ) {
				$( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_bookable' ).show();
			}
		}).change();

		// Simple and variable parent products
		$('#booking_product_data').find('.booking_dates').on( 'change', function() {

			var dates = $(this).val();

			if ( dates === 'global' ) {
				dates = wceb_admin_product.number_of_dates;
			}

			if ( dates === 'two' ) {
				$('.show_if_two_dates').show();
			} else {
				$('.show_if_two_dates').hide();
			}

		}).change();

		// Variations
		$( '#variable_product_options' ).on( 'change', '.booking_dates', function() {

			var $this   = $(this),
				dates   = $this.val(),
				$parent = $this.parents('.booking_variation_data');

			if ( dates === 'parent' ) {

				dates = $('#booking_product_data').find('.booking_dates').val();

				if ( dates === 'global' ) {
					dates = wceb_admin_product.number_of_dates;
				}

			}

			if ( dates === 'two' ) {
				$parent.find('.show_if_two_dates').show();
			} else {
				$parent.find('.show_if_two_dates').hide();
			}
			
		}).change();

		// Simple and variable parent products
		$('#booking_product_data').find('.booking_duration').on('change', function() {

			var bookingDuration     = $(this).val(),
				customDurationField = $('.custom_booking_duration_field'),
				unitField           = $('.wceb_unit');

			updateBookingFields( bookingDuration, unitField, customDurationField );

		}).change();

		// Variations
		$( '#variable_product_options' ).on( 'change', '.booking_duration', function() {
			var $this               = $(this),
				bookingDuration     = $this.val(),
				parent              = $this.parents('.booking_variation_data'),
				customDurationField = parent.find('.custom_booking_duration_field'),
				unitField           = parent.find('.wceb_unit');

			if ( bookingDuration === 'parent' ) {
				var parentProductBookingDuration = $('#booking_product_data').find('select[name=_booking_duration]').val();
				var bookingDuration = parentProductBookingDuration;
			}

			updateBookingFields( bookingDuration, unitField, customDurationField );
		}).change();

		$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function() {
			$('#variable_product_options').find('input.variation_is_bookable').change();
			$('#variable_product_options').find('.booking_dates').change();
			$('#variable_product_options').find('.booking_duration').change();
		});

		function updateBookingFields( bookingDuration, unitField, customDurationField ) {

			if ( bookingDuration === 'global' ) {
				unitField.html( wceb_admin_product.booking_duration_text );
			} else {

				if ( bookingDuration === 'weeks' ) {
					unitField.html( wceb_admin_product.weekly_duration_text );
				} else if ( bookingDuration === 'custom' ) {
					unitField.html( wceb_admin_product.custom_duration_text )
				} else {
					unitField.html( wceb_admin_product.daily_duration_text );
				}
				
			}

			if ( bookingDuration === 'custom' ) {
				customDurationField.show();
				customDurationField.find('input').prop('disabled', false); 
			} else {
				customDurationField.hide();
				customDurationField.find('input').prop('disabled', true);
			}
			
			return false;
		}

	});
})(jQuery);