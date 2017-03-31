(function($) {
	$(document).ready(function() {

		// Run tipTip
		function runTipTip() {
			// Remove any lingering tooltips
			$( '#tiptip_holder' ).removeAttr( 'style' );
			$( '#tiptip_arrow' ).removeAttr( 'style' );
			$( '.tips' ).tipTip({
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 200
			});
		}

		runTipTip();

		$('.ebdd-discounts').on('click','a.add-discount',function() {
			$(this).closest('.ebdd-discounts').find('.ebdd-table').append( $(this).data( 'row' ) );
			return false;
		});

		$('.ebdd-discounts').on('click','a.delete-discount', function() {
			$(this).closest('tr').remove();
			return false;
		});

	});
})(jQuery);
