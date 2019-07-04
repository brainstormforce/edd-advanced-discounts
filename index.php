<?php
/**
 * Plugin Name: EDD Advanced Coupons
 * Description: Discounting options to EDD
 * Version:     1.0.0
 * Author:      Brainstorm Force
 * Author URI:  https://brainstormforce.com
 * Text Domain: 
 * Main
 * 
 *
 * @category PHP
 * @package   
 * @author   Display Name <username@brainstormforce.com>
 * @license  https://brainstormforce.com
 * @link     https://brainstormforce.com
 */


define( 'BSF_EAC_ABSPATH', plugin_dir_path( __FILE__ ) );
// require_once('classes/class-bsfeac-loader.php');
// include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
// require_once (EDD_PLUGIN_DIR . 'includes/class-edd-discount.php');
// require_once (EDD_PLUGIN_DIR . 'includes/admin/discounts/discount-actions.php');
// require_once (EDD_PLUGIN_DIR . 'includes/discount-functions.php');
// require_once EDD_PLUGIN_DIR . 'includes/class-edd-discount.php';


add_action('edd_add_discount_form_before_max_uses','edd_add_form');
add_action( 'edd_edit_discount_form_before_status', 'edd_fun',10,2);
add_filter( 'edd_insert_discount', 'edd_verify_add_nonce', 1, 20);
add_action( 'init', 'edd_verify_nonce');

function edd_add_form($discount_id=null)
{	
	?>
	<tr>
		<th scope="row" valign="top">
			<label for="edd-max-cart-amount"><?php _e( 'Maximum Amount', 'easy-digital-downloads' ); ?></label>
		</th>
		<td>
			<input type="text" id="edd-max-cart-amount" name="max_price" value=" " />
			<p class="description"><?php _e( 'The maximum dollar amount below which this discount can be used. Leave blank for no maximum.', 'easy-digital-downloads' ); ?></p>
		</td>
	</tr>
	<?php
}

function edd_fun( $discount_id, $discount ) {

	$max_price = get_post_meta( $discount_id, '_edd_discount_max_price',true );
	
	?>
	<tr>
		<th scope="row" valign="top">
			<label for="edd-max-cart-amount"><?php _e( 'Maximum Amount', 'easy-digital-downloads' ); ?></label>
		</th>
		<td>
			<input type="text" id="edd-max-cart-amount" name="max_price" value="<?php echo esc_attr($max_price); ?>" style="width: 40px "/>
			<p class="description"><?php _e( 'The maximum dollar amount below which this discount can be used. Leave blank for no maximum.', 'easy-digital-downloads' ); ?></p>
		</td>	
	</tr>
	
	<?php
	}

function edd_verify_nonce() {
	
	//$discount_new= new EDD_Discount( (int) $data['discount-id'] );
	
	$id=(!empty($_GET['discount']) ? $_GET['discount'] : '' );
	
	$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;	
	if ( 'edd-discounts' !== $page ) {
		return;
	}
	if (  isset( $_POST['edd-discount-nonce'] ) && wp_verify_nonce($_POST['edd-discount-nonce'], 'edd_discount_nonce' ) ) {
		
	    $maxprice = (!empty($_POST['max_price']) ? $_POST['max_price'] : 0);
		update_post_meta($id,'_edd_discount_max_price',$maxprice);
	}

}


add_filter('edd_is_discount_min_met', 'myfun',11,2);
function edd_verify_add_nonce($meta) {
 $maxprice = (!empty($_POST['max_price']) ? $_POST['max_price'] : 0);

 $arr = array(
'max_price' => $maxprice
 );

$meta = array_merge($arr,$meta);
	return $meta;

}
function myfun( $return = false, $discount_id = null ) {

	if( $return ){

		$is_discount_max_met = false;
		$discount = new EDD_Discount( $discount_id );
		$max_price = get_post_meta($discount_id,'_edd_discount_max_price',true);
		$cart_amount = edd_get_cart_discountable_subtotal( $discount_id);

			if ( (float) $cart_amount <= (float)$max_price ) {
				$is_discount_max_met = true;
			} elseif ( (float) $max_price == 0) {
				$is_discount_max_met = true;	
			} else {
				edd_set_error( 'edd-discount-error', sprintf( __( 'Maximum order of %s not met.', 'easy-digital-downloads' ), edd_currency_filter( edd_format_amount( $max_price) ) ) );		
			}

		return $is_discount_max_met;
			
	}
}	

/********** Apply discount based on variable pricing *********/


/*add_filter( 'edd_product_dropdown_args',  'update_dropdown_args',1);
function update_dropdown_args($args)
{
	
	// $arr=array( 'variations'  => true);
	// $args = array_merge($arr,$args);
	$args['variations']  = true;
	// echo "<pre>";
	// print_r($args);
	// wp_die();
	return $args;	
}
*/
