/**
 * Discount Add / Edit screen JS
 */

jQuery(document).ready(function(){
    jQuery( '#product_request' ).change( function() {
    	var conditions = jQuery( '#edd-discount-product-conditions_new' );
    	if( $(this).val() ) {
		conditions.show();
	} else {
		conditions.hide();
	}
    });
 });
