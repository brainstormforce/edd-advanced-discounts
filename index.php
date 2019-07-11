<?php
/**
 * Plugin Name: EDD Advanced Coupons
 * Description: Discounting options to EDD
 * Version:     1.0.0
 * Author:      Brainstorm Force
 * Author URI:  https://brainstormforce.com
 * Text Domain: easy-digital-downloads
 * 
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
// require_once( ABSPATH . '/wp-admin/includes/plugin.php' ) ;
// require_once(EDD_PLUGIN_DIR . 'includes/cart/functions.php');
// require_once(EDD_PLUGIN_DIR . 'includes/cart/class-edd-cart.php');

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
add_action('edd_add_discount_form_before_max_uses','add_products');
add_action('edd_edit_discount_form_before_status','edit_products',20,2);
add_action('edd_add_discount_form_before_max_uses','edd_add_form');
add_action( 'edd_edit_discount_form_before_status', 'edd_fun',10,2);
add_filter( 'edd_insert_discount', 'edd_verify_add_nonce', 1, 20);
add_filter('edd_is_discount_min_met', 'is_max_met',11,2);
add_filter('edd_is_discount_products_req_met','is_product_con_met',11,2);
add_action( 'init', 'edd_verify_nonce');

function edd_add_form($discount_id=null)
{	
	?>
	<tr style="background-color:#FAF8FF;">
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
	<tr style="background-color:#FAF8FF;">
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
	<tr style="background-color:#FAF8FF;">
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
	<tr style="background-color:#FAF8FF;">
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

function is_product_con_met($return = false, $discount_id = null,$product_condition=null)
{
	if( $return )
	{	
		$discount = new EDD_Discount( $discount_id );
		$product_condition='any';
		$products_con = get_post_meta($discount_id,'_edd_discount_products_con',true);
		$cart = edd_get_cart_contents();
		$return=false;
		 //$products_con = array_map( 'absint', $products_con );
		 // asort( $products_con );
		$products_con = array_filter( array_values( $products_con ) );
		//vl($products_con);
		if ( empty( $products_con ) ) {
			$return = true;

		}
		if(  ! empty( $products_con))
		{
			foreach ( $products_con as $download_id ) {
				if ( empty( $download_id ) ) {
					continue;
				}

					if ( is_array( $cart ) ) {

					foreach ( $cart as $item ) {
						$pid=explode('_', $download_id);

							if ( $item['id'] == $pid[0] ) {

								if ( isset($pid[1]) && isset( $item['options']['price_id'] ) ) {

									if ( $pid[1] == $item['options']['price_id'] ) {

										$return = true;
									
										break 2;	

									} else{
											$return= false;
											edd_set_error( 'edd-discount-error', __( 'The product requirements for this discount are not met.', 'easy-digital-downloads' ) );
											break;
									} 
									
								} else{
										$return=true;
										break;
									}

							} else{
									
									$return=false;
									edd_set_error( 'edd-discount-error', __( 'The product requirements for this discount are not met.', 'easy-digital-downloads' ) );
								}
							
					}

				} 
			}		
				
		}
		return $return;		
	}
}

// Disabled the Download condition of previous option.
add_filter( 'edd_discount_is_not_global','prodcuct_is_not_global'); 
function prodcuct_is_not_global($return=false, $is_not_global=null)
{
	if(! $return || $return)
	{
		$is_not_global=true;
		return $is_not_global;
	}
}	


/**
 * Filters the download requirements.
 *
 * @param array $product_reqs IDs of required products.
 * @param int   $ID           Discount ID.
 */
add_filter( 'edd_get_discount_product_reqs','get_product_req',10,2); 
function get_product_req($product_reqs, $ID ) {

	if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		return $product_reqs;
	}

	$products_con = (array) get_post_meta($ID,'_edd_discount_products_con',true);
	
	$products_con = array_map( 'absint', $products_con );
	asort( $products_con );
	$products_con = array_filter( array_values( $products_con ) );

	// Is empty "Product Requirements" then return defaults.
	if( empty( $products_con ) ) {
		$product_reqs=true;
		return $product_reqs;
	}
	//vl(array_unique( wp_parse_args( $product_reqs, $products_con ) ));
	return array_unique( wp_parse_args( $product_reqs, $products_con ) );

}



// We have used filter from below funciton to add variation support.
				// @see add_filter( 'edd_get_discount_product_reqs', 'edd_ac_edd_item_in_cart', 10, 3 );
				// $test = array( "discount_id" => $discount_id );

				//writing my code in if condition......
				// if ( edd_item_in_cart( $download_id ) ) {
				// 	$return=true;
				// 	break;
				// }

// add_filter( 'edd_item_in_cart', 'edd_ac_edd_item_in_cart', 10, 3 );
// function edd_ac_edd_item_in_cart( $ret, $download_id, $options ) {

// 	if( $ret ) {
// 		$ret=false;
// 		$cart = edd_get_cart_contents();
// 		$discount  = edd_get_discount_by_code( $_POST['code'] );
// 		$products_con = get_post_meta($discount->id,'_edd_discount_products_con',true);
// 		//vl($products_con);
// 		//vl($cart);
// 		if ( is_array( $cart ) ) {
// 			foreach ( $cart as $item ) {
// 					if ( $item['id'] == $download_id ) {
// 						//vl($download_id);	
// 						//foreach ($products_con as $download_id) {		
// 							$pid=explode('_', $download_id);
// 							//vl($pid[1]);
// 								if ( $pid[1] == $item['options']['price_id'] ) {
// 									$ret = true;
// 									echo "okkkk";
// 									break;
// 								}
// 						//}

// 					}
// 			}			
// 		}	
// 		// if (($pos = strpos($products_con, "_")) !== FALSE) { 
//   		//   				$priceid = substr($products_con, $pos+1); 
// 		// }		
// 	}

// 	return $ret;
// }	


