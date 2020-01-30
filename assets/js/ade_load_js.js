
/**
	 * Discount Add / Edit screen JS
*/

jQuery(document).ready(function($){
    $('#product_request').change( function() {
    	var conditions = $( '#edd-discount-product-conditions_new' );
    	if( $(this).val() ) {
				conditions.show();
			} else {
				conditions.hide();
			}
    });
 });