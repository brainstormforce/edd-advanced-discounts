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
 require_once( ABSPATH . '/wp-admin/includes/plugin.php' ) ;

 function is_edd_active()
 {
 	 if ( ! is_plugin_active ( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
 	 	echo '<div id="message" class="notice is-dismissible notice-success">
      		<p class="description">
      			EDD Advanced Coupons plugin requires Easy Digital Download Plugin to be active!
      		</p>
    		</div>';
        return false ;
    }
 } 
add_action( 'admin_notices', 'is_edd_active' );

add_action('edd_add_discount_form_before_excluded_products','add_products');
add_action('edd_edit_discount_form_before_excluded_products','edit_products',10,2);
add_action('edd_add_discount_form_before_max_uses','edd_add_form');
add_action( 'edd_edit_discount_form_before_status', 'edd_fun',10,2);
add_filter( 'edd_insert_discount', 'edd_verify_add_nonce', 1, 20);
add_filter('edd_is_discount_min_met', 'is_max_met',11,2);
add_filter('edd_is_discount_products_req_met','is_product_con_met',11,2);
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


function add_products(){
	
	?>
	<tr>
		<th scope="row" valign="top">
			<label for="edd-products"><?php printf( __( 'Product Requirements', 'easy-digital-downloads' ), edd_get_label_singular() ); ?></label>
		</th>
		<td>
			<p>
				<?php echo EDD()->html->product_dropdown( array(
					'name'        => 'products_con[]',
					'id'          => 'products_con',
					'multiple'    => true,
					'chosen'      => true,
					'variations'  => true,
					'placeholder' => sprintf( __( 'Select one or more %s', 'easy-digital-downloads' ), edd_get_label_plural() ),
				) ); ?><br/>
			</p>
			
			<p class="description"><?php printf( __( 'Select %s relevant to this discount. If left blank, this discount can be used on any product.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
		</td>
	</tr> 
	<?php
	}

function edit_products($discount_id,$discount)
{
	$products_con=get_post_meta($discount_id, '_edd_discount_products_con',true);
	?>
	<tr>
		<th scope="row" valign="top">
			<label for="edd-products"><?php printf( __( 'Product Requirements', 'easy-digital-downloads' ), edd_get_label_singular() ); ?></label>
		</th>
		<td>
			<p>
				<?php
				echo EDD()->html->product_dropdown( array(
					'name'        => 'products_con[]',
					'id'          => 'products_con',
					'selected'    => $products_con,
					'multiple'    => true,
					'chosen'      => true,
					'variations'  => true,
					'placeholder' => sprintf( __( 'Select one or more %s', 'easy-digital-downloads' ), edd_get_label_plural() )
				) ); ?><br/>
			</p>
			
			<p class="description"><?php printf( __( 'Select %s relevant to this discount. If left blank, this discount can be used on any product.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
		</td>
	</tr>
	<?php
}

function edd_verify_nonce() {
		
	$id=(!empty($_GET['discount']) ? $_GET['discount'] : '' );
	$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;	
	if ( 'edd-discounts' !== $page ) {
		return;
	}
	if (  isset( $_POST['edd-discount-nonce'] ) && wp_verify_nonce($_POST['edd-discount-nonce'], 'edd_discount_nonce' ) ) {
		
	    $maxprice = (!empty($_POST['max_price']) ? $_POST['max_price'] : 0);
		update_post_meta($id,'_edd_discount_max_price',$maxprice);

		$productcon= (!empty($_POST['products_con']) ? $_POST['products_con'] : 0);
		update_post_meta($id,'_edd_discount_products_con',$productcon);
		//vl($_POST);

	}

}

function edd_verify_add_nonce($meta) {
	
	 $maxprice   =  (! empty($_POST['max_price'] )) ? $_POST['max_price'] : 0;
	 $productcon =  (! empty($_POST['products_con'] )) ? $_POST['products_con'] : 0;
	 $arr = array(
		'max_price' => $maxprice,
		'products_con' => $productcon
	 );

	$meta = array_merge($arr,$meta);
		return $meta;

}

function is_max_met( $return = false, $discount_id = null ) {

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
function is_product_con_met($return = false, $discount_id = null)
{
	if($return)
	{
	
		$products_con = get_post_meta($discount_id,'_edd_discount_products_con',true);
		$excluded_ps  = $discount->excluded_products;
		$cart_items   = edd_get_cart_contents();
		$cart_ids     = $cart_items ? wp_list_pluck( $cart_items, 'id' ) : null;
		if ( empty( $products_con ) && empty( $excluded_ps ) ) {
			$return = true;
		}

		if( ! empty( $products_con))
		{
			foreach ( $products_con as $download_id ) {
						if ( empty( $download_id ) ) {
							continue;
						}
						if ( ! edd_item_in_cart( $download_id ) ) {

							if ( $set_error ) {
								edd_set_error( 'edd-discount-error', __( 'The product requirements for this discount are not met.', 'easy-digital-downloads' ) );
							}
						}
			}	


	}
}
}
/*function is_product_con_met($return = false, $discount_id = null)
{
	if( $return ){
		$discount = new EDD_Discount( $discount_id );
		//$discount->product_condition=true;
		$products_con = get_post_meta($discount_id,'_edd_discount_products_con',true);
		// $cart_items = edd_get_cart_contents();
		// if ( empty( $products_con ) && empty( $excluded_ps ) ) {
		// 	$return = true;
		// }
		// vl($cart_items);
		$excluded_ps  = $discount->excluded_products;
		$cart_items   = edd_get_cart_contents();
		$cart_ids     = $cart_items ? wp_list_pluck( $cart_items, 'id' ) : null;
		$return       = false;

		if ( empty( $products_con ) && empty( $excluded_ps ) ) {
			$return = true;
		}

		$products_con = array_map( 'absint', $products_con );
		asort( $products_con );
		$products_con = array_filter( array_values( $products_con ) );

		$excluded_ps  = array_map( 'absint', $excluded_ps );
		asort( $excluded_ps );
		$excluded_ps  = array_filter( array_values( $excluded_ps ) );
		//vl();

		$cart_ids     = array_map( 'absint', $cart_ids );
		asort( $cart_ids );
		$cart_ids     = array_values( $cart_ids );

		if ( ! $return && ! empty( $products_con ) ) {
			switch ( $discount->product_condition ) {
				case 'all' :

					// Default back to true
					$return = true;

					foreach ( $products_con as $download_id ) {
						if ( empty( $download_id ) ) {
							continue;
						}

						if ( ! edd_item_in_cart( $download_id ) ) {

							if ( $set_error ) {
								edd_set_error( 'edd-discount-error', __( 'The product requirements for this discount are not met.', 'easy-digital-downloads' ) );
							}

							$return = false;

							break;

						}

					}

					break;

				default :

					foreach ( $products_con as $download_id ) {

						if ( empty( $download_id ) ) {
							continue;
						}

						if ( edd_item_in_cart( $download_id ) ) {
							$return = true;
							break;
						}

					}

					if ( ! $return && $set_error ) {
						edd_set_error( 'edd-discount-error', __( 'The product requirements for this discount are not met.', 'easy-digital-downloads' ) );
					}

					break;

			}

		} else {

			$return = true;

		}

		if ( ! empty( $excluded_ps ) ) {
			if ( count( array_intersect( $cart_ids, $excluded_ps ) ) == count( $cart_ids ) ) {
				$return = false;

				if ( $set_error ) {
					edd_set_error( 'edd-discount-error', __( 'This discount is not valid for the cart contents.', 'easy-digital-downloads' ) );
				}
			}
		}

	
	return $return;
	}
		
}

*/



