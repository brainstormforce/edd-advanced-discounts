


// function myFunction() {

//     $('#product_request').change( function() {
     
//     	var conditions =$( '#edd-discount-product-conditions_new' );
//     	if( $(this).val() ) {
// 					conditions.show();
// 				} else {
// 					conditions.hide();
// 				}
//     });
    
// }



/**
	 * Discount add / edit screen JS
*/

$(document).ready(function(){
    $('#product_request').change( function() {
    	var conditions =$( '#edd-discount-product-conditions_new' );
    	if( $(this).val() ) {
					conditions.show();
				} else {
					conditions.hide();
				}
    });
 });