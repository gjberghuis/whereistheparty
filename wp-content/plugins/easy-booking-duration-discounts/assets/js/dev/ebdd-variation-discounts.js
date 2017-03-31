(function($) {
	$(document).ready(function() {

		$('body').on( 'found_variation', '.variations_form', function( e, variation ) {

			if ( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible || ! variation.is_bookable || variation.has_dates === 'one' ) {
				$('.ebdd_discounts').html('').slideUp(200);
			} else {
				var variation_id = variation.variation_id;
				display_product_discounts( variation_id );
			}

		});

		$('body').on( 'reset_image', '.variations_form', function() {
			$('.ebdd_discounts').slideUp( 200 );
		});

		function display_product_discounts( variation_id ) {
			var output = ajax.discounts[variation_id];
			$('p.ebdd_discounts').html( output ).slideDown( 200 );
		}
		
	});
})(jQuery);