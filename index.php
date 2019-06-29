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
require_once('classes/class-bsfeac-loader.php');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once (EDD_PLUGIN_DIR . 'includes/class-edd-discount.php');
require_once (EDD_PLUGIN_DIR . 'includes/admin/discounts/discount-actions.php');
require_once (EDD_PLUGIN_DIR . 'includes/discount-functions.php');

$content_path = content_url();

require_once EDD_PLUGIN_DIR . 'includes/class-edd-discount.php';
add_action('edd_add_discount_form_before_max_uses','edd_add_form');
add_action( 'edd_edit_discount_form_before_status', 'edd_fun',10,2);
add_action( 'init', 'edd_verify_nonce');
add_action('edd_is_discount_min_met','is_max_price_met');
	
function edd_add_form()
{
	?>
	<tr>
		<th scope="row" valign="top">
			<label for="edd-max-cart-amount"><?php _e( 'Maximum Amount', 'easy-digital-downloads' ); ?></label>
		</th>
		<td>
			<input type="text" id="edd-max-cart-amount" name="max_price" value=" " />
			<p class="description"><?php _e( 'The Maximum dollar amount that must be in the cart before this discount can be used. Leave blank for no Maximum.', 'easy-digital-downloads' ); ?></p>
		</td>
	</tr>
	<?php
}

function edd_fun( $discount_id, $discount ) {

	 //esc_attr($_POST['$max_price']); 
	//global $a=$discount_id;

		$max_price = get_post_meta( $discount_id, '_edd_discount_max_price',true );
	
	?>
	<tr>
		<th scope="row" valign="top">
			<label for="edd-max-cart-amount"><?php _e( 'Maximum Amount', 'easy-digital-downloads' ); ?></label>
		</th>
		<td>
			 <input type="text" id="edd-max-cart-amount" name="max_price" value="<?php echo esc_attr($max_price ); ?>" style="width: 40px "/>
			<p class="description"><?php _e( 'The MAX amount that must be purchased before this discount can be used. Leave blank for no MAX.', 'easy-digital-downloads' ); ?></p>
		</td>
	</tr>
	
	<?php
	}

function edd_verify_nonce() {

	// 	global $post;
	// $discount = new EDD_Discount();
	// var_dump( $discount_id );
	// wp_die();	
	// var_dump(get_the_id());
	$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;

	if ( 'edd-discounts' !== $page ) {
		return;
	}
	if (  isset( $_POST['edd-discount-nonce'] ) && wp_verify_nonce($_POST['edd-discount-nonce'], 'edd_discount_nonce' ) ) {
		//wp_die();
		$maxprice = $_POST['max_price'];
		var_dump($maxprice);
		update_post_meta(24,'_edd_discount_max_price',$maxprice);
	}

}


apply_filters('is_min_price_met', 'myfun',10,1);
function myfun( $set_error = true ) {
	// wp_die();
	// echo "ok";
	$max_price = get_post_meta(24,'_edd_discount_max_price');
		$return = false;

		$cart_amount = 500;

		if ( (float) $cart_amount >=  (float) $max_price) {
			$return = true;
		} else {
			edd_set_error( 'edd-discount-error', sprintf( __( 'Maximum order of %s not met.', 'easy-digital-downloads' ), edd_currency_filter( edd_format_amount( $max_price) ) ) );
		}
//return $return;
		
		return apply_filters( 'is_min_price_met', $return );
	}