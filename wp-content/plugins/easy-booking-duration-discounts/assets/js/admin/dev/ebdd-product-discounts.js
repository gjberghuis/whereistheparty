(function($) {
	$(document).ready(function() {

		$('#woocommerce-product-data').on('click','.ebdd_product_discounts a.add-discount',function() {
			$(this).closest('.ebdd_product_discounts').find('tbody').append( $(this).data( 'row' ) );
			$(this).parents('.woocommerce_variation').addClass('variation-needs-update');
			$( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
			return false;
		});

		$('#woocommerce-product-data').on('click','.ebdd_product_discounts a.delete-discount',function() {
			$(this).parents('.woocommerce_variation').addClass('variation-needs-update');
			$( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
			$(this).closest('tr').remove();
			return false;
		});

		$( 'input#_per_product_pricing_active' ).change( function() {

			if ( $( 'select#product-type' ).val() === 'bundle' ) {

				if ( $(this).is( ':checked' ) ) {
					$('.show_if_per_product_pricing').show();
					$('.hide_if_per_product_pricing').hide();
				} else {
					$('.show_if_per_product_pricing').hide();
					$('.hide_if_per_product_pricing').show();
				}
				
			}

		}).change();

	});
})(jQuery);
