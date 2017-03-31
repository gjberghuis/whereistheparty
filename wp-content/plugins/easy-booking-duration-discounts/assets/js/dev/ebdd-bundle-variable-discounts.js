(function($) {
	$(document).ready(function() {

		$('body').on( 'wceb_variation_found', '.variations_form', function( e, variation, $form ) {

			var $this = $(this);
			
			var product_id = variation.product_id,
				variation_id = variation.variation_id;

			var optional = $this.data('optional'),
				checked  = $('input[name="bundle_selected_optional_' + product_id + '"]').is(':checked');

			if ( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible || ! variation.is_bookable || ( optional && ! checked ) ) {
				$this.next('.ebdd_discounts').html('').slideUp(200);
			} else {
				display_product_discounts( variation_id, $this );
			}

		});

		$('body').on( 'reset_image', '.variations_form', function() {
			$(this).next('.ebdd_discounts').slideUp( 200 );
		});

		function display_product_discounts( variation_id, $form ) {
			var output = ajax.discounts[variation_id];
			$form.next('p.ebdd_discounts').html( output ).slideDown( 200 );
		}
		
	});
})(jQuery);