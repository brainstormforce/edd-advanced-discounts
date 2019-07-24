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
require_once BSF_EAC_ABSPATH . '/classes/class-bsfeac-loader.php';
//require_once BSF_EAC_ABSPATH . '/includes/cart/class-edd-cart.php';
define( 'BSF_EAC_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'BSF_EAC_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
//wp_enqueue_style('bsf_eac_as_style');
//wp_enqueue_script('bsf_eac_js');


add_action( 'admin_notices', 'is_edd_active' );
function is_edd_active()
 {
 	 if ( ! is_plugin_active ( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {?>
 	 	<div id="message" class="notice notice-error">
      		<p>
      			The <strong>EDD Advanced plugin</strong> requires Easy Digital Downloads plugin installed & activated.
      		</p>
    		</div>
    		<?php
        return false ;	
    }
 }

/**
 * Add Discount Page
 */
add_action('edd_add_discount_form_bottom','add_new_option',10);
function add_new_option()
{ 
	?>
	<table class="form-table">
		<tbody>

		<tr class="new_option">
			<th scope="row" valign="top">
				<label for="edd-products"><?php printf( __( 'Product Requirements', 'easy-digital-downloads' ), edd_get_label_singular() ); ?></label>
			</th>
			<td>
				<p>
					<?php echo EDD()->html->product_dropdown( array(
						'name'        => 'product_request[]',
						'id'          => 'product_request',
						'multiple'    => true,
						'chosen'      => true,
						'variations'  => true,
						'placeholder' => sprintf( __( 'Select one or more %s', 'easy-digital-downloads' ), edd_get_label_plural() ),
					) ); ?><br/>
				</p>
				<div id="edd-discount-product-conditions_new" style="display: none;" >
							<p>
								<select id="edd-product-new-condition" name="product_condition">
									<option value="all"><?php printf( __( 'Cart must contain all selected %s', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></option>
									<option value="any"><?php printf( __( 'Cart needs one or more of the selected %s', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></option>
								</select>
							</p>
							<p>
								<label>
									<input type="radio" class="tog" name="not_global" value="0" checked="checked"/>
									<?php _e( 'Apply discount to entire purchase.', 'easy-digital-downloads' ); ?>
								</label><br/>
								<label>
									<input type="radio" class="tog" name="not_global" value="1"/>
									<?php printf( __( 'Apply discount only to selected Downloads.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?>
								</label>
							</p>
				</div>
				<p class="description"><?php printf( __( 'Select products relevant to this discount. If left blank, this discount can be used on any product.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
			</td>
		</tr>	

		<tr>
			<th scope="row" valign="top">
				<label for="edd-excluded-products"><?php printf( __( 'Excluded Products', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></label>
			</th>
			<td>
				<?php echo EDD()->html->product_dropdown( array(
					'name'        => 'product_excluded[]',
					'id'          => 'product_excluded[]',
					'selected'    => array(),
					'multiple'    => true,
					'chosen'      => true,
					'variations'  => true,
					'placeholder' => sprintf( __( 'Select one or more %s', 'easy-digital-downloads' ), edd_get_label_plural() ),
				) ); ?><br/>
				<p class="description"><?php printf( __( 'Products that this discount code cannot be applied to.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top">
				<label for="edd-max-cart-amount"><?php _e( 'Maximum Amount', 'easy-digital-downloads' ); ?></label>
			</th>
			<td>
				<input type="text" id="edd-max-cart-amount" name="max_price" value=" " />
				<p class="description"><?php _e( 'The maximum amount below which this discount can be used. Leave blank for no maximum.', 'easy-digital-downloads' ); ?></p>
			</td>
		</tr>
			
		</tbody>
	</table>

	<?php
}


/**
 * Edit Discount Page
 */
add_action('edd_edit_discount_form_bottom','edit_new_option',10,2);
function edit_new_option($discount_id,$discount)
{
	$product_request =(array)get_post_meta($discount_id, '_edd_discount_product_request',true);
	$product_request = array_filter( array_values($product_request));	

	$product_excluded = (array)get_post_meta($discount_id, '_edd_discount_product_excluded',true);
	$product_excluded = array_filter( array_values($product_excluded));	

	$max_price = get_post_meta( $discount_id, '_edd_discount_max_price',true );
	$condition_dis = empty( $product_request ) ? 'style="display:none;"' : '';
	//vl($product_excluded);

	?>
	<table class="form-table">
		<tbody>
		<tr class="new_option">
			<th scope="row" valign="top">
				<label for="edd-products"><?php printf( __( 'Product Requirements', 'easy-digital-downloads' ), edd_get_label_singular() ); ?></label>
			</th>
			<td>
				<p>
					<?php
					
					echo EDD()->html->product_dropdown( array(
						'name'        => 'product_request[]',
						'id'          => 'product_request',
						'selected'    => $product_request,
						'multiple'    => true,
						'chosen'      => true,
						'variations'  => true,
						'placeholder' => sprintf( __( 'Select one or more %s', 'easy-digital-downloads' ), edd_get_label_plural() )
					) ); ?><br/>
				</p>
				<div id="edd-discount-product-conditions_new"  <?php echo $condition_dis; ?> >
							<p>
								<select id="edd-product-condition" name="product_condition">
									<option value="all"<?php selected( 'all', edd_get_discount_product_condition($discount_id) ); ?>><?php printf( __( 'Cart must contain all selected %s', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></option>
									<option value="any"<?php selected( 'any',edd_get_discount_product_condition($discount_id) ); ?>><?php printf( __( 'Cart needs one or more of the selected %s', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></option>
								</select>
							</p>
							<p>
								<label>
									<input type="radio" class="tog" name="not_global" value="0"<?php checked( false, edd_is_discount_not_global( $discount_id ) ); ?>/>
									<?php _e( 'Apply discount to entire purchase.', 'easy-digital-downloads' ); ?>
								</label><br/>
								<label>
									<input type="radio" class="tog" name="not_global" value="1"<?php checked( true, edd_is_discount_not_global( $discount_id ) ); ?>/>
									<?php printf( __( 'Apply discount only to selected %s.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?>
								</label>
							</p>
				</div>

				<p class="description"><?php printf( __( 'Select products relevant to this discount. If left blank, this discount can be used on any product.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top">
				<label for="edd-excluded-products"><?php printf( __( 'Excluded Products', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></label>
			</th>
			<td>
				<?php echo EDD()->html->product_dropdown( array(
					'name'        => 'product_excluded[]',
					'id'          => 'product_excluded',
					'selected'    => $product_excluded,
					'multiple'    => true,
					'chosen'      => true,
					'variations'  => true,
					'placeholder' => sprintf( __( 'Select one or more products', 'easy-digital-downloads' ), edd_get_label_plural() )
				) ); ?><br/>
				<p class="description"><?php printf( __( 'Products that this discount code cannot be applied to.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top">
				<label for="edd-max-cart-amount"><?php _e( 'Maximum Amount', 'easy-digital-downloads' ); ?></label>
			</th>
			<td>
				<input type="text" id="edd-max-cart-amount" name="max_price" value="<?php echo esc_attr($max_price); ?>" style="width: 40px "/>
				<p class="description"><?php _e( 'The maximum amount below which this discount can be used. Leave blank for no maximum.', 'easy-digital-downloads' ); ?></p>
			</td>	
		</tr>


		</tbody>
	</table>
<?php
}


/**
			 * Filters the metadata before being inserted into the database.
			 *
			 * @since 2.7
			 *
			 * @param array $meta Discount meta.
			 * @param int   $ID   Discount ID.
			 */
add_filter( 'edd_insert_discount', 'verify_add_nonce', 1, 20);
function verify_add_nonce($meta) {
	
	 $maxprice   =  (! empty($_POST['max_price'] )) ? $_POST['max_price'] : 0;
	 $productrequest =  (! empty($_POST['product_request'] )) ? $_POST['product_request'] : 0;
	 $productexcluded =  (! empty($_POST['product_excluded'] )) ? $_POST['product_excluded'] : 0;
	 $arr = array(
		'max_price' => $maxprice,
		'product_request' => $productrequest,
		'product_excluded' =>$productexcluded
	 );

	$meta = array_merge($arr,$meta);
	return $meta;
}


add_action( 'init', 'edd_verify_nonce');
function edd_verify_nonce() {
		
	$id=(!empty($_GET['discount']) ? $_GET['discount'] : '' );
	$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;	
	if ( 'edd-discounts' !== $page ) {
		return;
	}
	if (  isset( $_POST['edd-discount-nonce'] ) && wp_verify_nonce($_POST['edd-discount-nonce'], 'edd_discount_nonce' ) ) {
		
	    $maxprice = (!empty($_POST['max_price']) ? $_POST['max_price'] : 0);
		update_post_meta($id,'_edd_discount_max_price',$maxprice);

		$productrequest= (!empty($_POST['product_request']) ? $_POST['product_request'] : 0);
		update_post_meta($id,'_edd_discount_product_request',$productrequest);

		$productexcluded= (!empty($_POST['product_excluded']) ? $_POST['product_excluded'] : 0);
		update_post_meta($id,'_edd_discount_product_excluded',$productexcluded);	

	}

}


/**
		 * Filters if the minimum cart amount has been met to satisify the discount.
		 *
		 * @since 2.7
		 *
		 * @param bool $return Is the minimum cart amount met or not.
		 * @param int  $ID     Discount ID.
		 */
add_filter('edd_is_discount_min_met', 'is_max_met',11,2);
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


/**
		 * Filters whether the product requirements are met for the discount to hold.
		 *
		 * @since 2.7
		 *
		 * @param bool   $return            Are the product requirements met or not.
		 * @param int    $ID                Discount ID.
		 * @param string $product_condition Product condition.
*/
add_filter('edd_is_discount_products_req_met','is_product_request_met',11,2);
function is_product_request_met($return = false, $discount_id = null)
{
	if($return)
	{
		$return=false;
		$product_condition=edd_get_discount_product_condition($discount_id);

		$product_request =(array) get_post_meta($discount_id,'_edd_discount_product_request',true);
		$product_request = array_filter( array_values( $product_request ) );	

		$product_excluded = (array) get_post_meta($discount_id,'_edd_discount_product_excluded',true);
		$product_excluded = array_filter( array_values( $product_excluded ) );
	
		$cart_items   = edd_get_cart_contents();
	
		//$cart_items = array_filter( array_values( $cart_items) );
		//vl($cart_items);

		// product ids with variation

		$cart_ids=array();
		foreach ($cart_items as $item) {
			if(isset($item['options']['price_id'])){
				array_push($cart_ids,implode('_',array($item['id'],$item['options']['price_id'])));

			} else {
				array_push($cart_ids,$item['id']);
			}

		}
		$cart_ids = array_values( $cart_ids );

		// core product ids

		 $cart_coreids     = $cart_items ? wp_list_pluck( $cart_items, 'id' ) : null;
		 $cart_coreids     = array_map( 'absint', $cart_coreids );
		 $cart_coreids     = array_values( $cart_coreids );

	
		if ( empty( $product_request ) && empty( $product_excluded ) ) {
			$return = true;
		}

		if (  ! $return && ! empty( $product_request ) )
		  {
			switch ( $product_condition ) {
				case 'all' :
					// Default back to true
					$return = true;

					foreach ( $product_request as $download_id ) {

						if ( empty( $download_id ) ) {
							continue;
						}

						if ( ! edd_item_in_cart( $download_id )   ) {
							$return = false;
							if ( ! $return ) {
								edd_set_error( 'edd-discount-error', __( 'The product requirements for this discount are not met.', 'easy-digital-downloads' ) );
							}
							break;

						}

					}

					break;

				default :

					foreach ( $product_request as $download_id ) {
						if ( empty( $download_id ) ) {
							continue;
						}
						if ( edd_item_in_cart( $download_id ) ) {
							$return = true;
							break;
						}	

					}
				
					if ( ! $return  ) {
						edd_set_error( 'edd-discount-error', __( 'The product requirements for this discount are not met.', 'easy-digital-downloads' ) );
					}

					break;

			}

		} else {
			$return = true;

		}

		//For Excluded products
	 
		if ( !empty( $product_excluded ) ) {

			//  count( array_intersect( $cart_coreids, $product_excluded ) ) == count( $cart_coreids ) ||
			if (
 				 count( array_intersect( $cart_ids, $product_excluded ) ) == count( $cart_ids ) ) {
					$return = false;
					if ( !$return ) {
					edd_set_error( 'edd-discount-error', __( 'This discounts is not valid for the cart contents.', 'easy-digital-downloads' ) );
				   }
			}
		}

		return $return;
	}
}



// add_filter( 'edd_get_cart_item_discounted_amount','get_amount',10,2);
// function get_amount($item=array(),$discount=false)
// {	
// 		//$cart = new EDD_Cart();
// 		global $edd_is_last_cart_item, $edd_flat_discount_total;
// 		$cart_items=edd_get_cart_contents();

// 		//vl($cart_items);
// 		foreach ($cart_items as $item) {

// 			if ( empty( $item ) || empty( $item ['id'] ) ) {
				
// 				return 0;
// 			}

// 			// Quantity is a requirement of the cart options array to determine the discounted price
// 			if ( empty( $item['quantity'] ) ) { 		
// 				return 0;
// 			}

// 			if ( ! isset( $item ['options'] ) ) { 			
// 				$item['options'] = array();
// 			}
		
		
// 		// If we're not meeting the requirements of the $key array, return or set them
// 			$amount           = 0;
// 			$price            =edd_get_cart_item_price( $item['id'], $item['	options'] );
// 			$discounted_price = $price;
// 		}	
	
// 		$discounts = false === $discount ? edd_get_discounts() : array( $discount );
// 		// If discounts exist, only apply them to non-free cart items
// 		if ( ! empty( $discounts ) && 0.00 != $price ) {
// 			foreach ( $discounts as $discount ) {
// 				$code_id = edd_get_discount_id_by_code( $discount );

// 				// Check discount exists
// 				if( ! $code_id ) {
// 					continue;
// 				}

// 				$reqs              = edd_get_discount_product_reqs( $code_id );
// 				$excluded_products = edd_get_discount_excluded_products( $code_id );
// 				//vl($excluded_products);	
// 				// Make sure requirements are set and that this discount shouldn't apply to the whole cart
// 				if ( ! empty( $reqs ) && edd_is_discount_not_global( $code_id ) ) {
// 					// This is a product(s) specific discount
// 					foreach ( $reqs as $download_id ) {
// 						if ( $download_id == $item['id'] && ! in_array( $item['id'], $excluded_products ) ) {
// 							$discounted_price -= $price - edd_get_discounted_amount( $discount, $price );
// 						}
// 					}
// 				} else {
// 					// This is a global cart discount
// 					if( ! in_array( $item['id'], $excluded_products ) ) {
// 						if( 'flat' === edd_get_discount_type( $code_id ) ) {
// 							/* *
// 							 * In order to correctly record individual item amounts, global flat rate discounts
// 							 * are distributed across all cart items. The discount amount is divided by the number
// 							 * of items in the cart and then a portion is evenly applied to each cart item
// 							 */
// 							$items_subtotal    = 0.00;
// 							$cart_items        = edd_get_cart_contents();
// 							foreach ( $cart_items as $cart_item ) {
// 								if ( ! in_array( $cart_item['id'], $excluded_products ) ) {
// 									$item_price      = edd_get_cart_item_price( $cart_item['id'], $cart_item['options'] );
// 									$items_subtotal += $item_price * $cart_item['quantity'];
// 								}
// 							}

// 							$subtotal_percent  = ( ( $price * $item['quantity'] ) / $items_subtotal );
// 							$code_amount       = edd_get_discount_amount( $code_id );
// 							$discounted_amount = $code_amount * $subtotal_percent;
// 							$discounted_price -= $discounted_amount;

// 							$edd_flat_discount_total += round( $discounted_amount, edd_currency_decimal_filter() );

// 							if ( $edd_is_last_cart_item && $edd_flat_discount_total < $code_amount ) {
// 								$adjustment = $code_amount - $edd_flat_discount_total;
// 								$discounted_price -= $adjustment;
// 							}
// 						} else {
// 							$discounted_price -= $price - edd_get_discounted_amount( $discount, $price );
// 						}
// 					}
// 				}

// 				if ( $discounted_price < 0 ) {
// 					$discounted_price = 0;
// 				}
// 			}

// 			$amount = round( ( $price - apply_filters( 'edd_get_cart_item_discounted_amount', $discounted_price, $discounts, $item, $price ) ), edd_currency_decimal_filter() );

// 			if ( 'flat' !== edd_get_discount_type( $code_id ) ) {
// 				$amount = $amount * $item['quantity'];
// 			}
// 		}

// 		return $amount;

// }


/**
	 * Checks to see if an item is in the cart.
	 *
	 * @since 2.7
	 *
	 * @param int   $download_id Download ID of the item to check.
 	 * @param array $options
	 * @return bool
 */
add_filter('edd_item_in_cart','item_in_cart',5,3);
function item_in_cart($ret=true,$download_id=0, $options = array())
{
if(!$ret || $ret){
$cart = edd_get_cart_contents();
//$product_excluded = (array) get_post_meta($discount_id,'_edd_discount_product_excluded',true);
//$product_excluded = array_filter( array_values( $product_excluded ) );
$ret = false;
if ( is_array( $cart ) ) {
			foreach ( $cart as $item ) {
				//vl($download_id);
				$pid=explode('_', $download_id);
				if ( $item['id'] == $pid[0]) {
					if ( isset( $pid[1] )  && isset( $item['options']['price_id'] ) ) {
						if ( $pid[1] == $item['options']['price_id'] ) {
							$ret = true;
							break;
						}
					} else {
						$ret = true;
						break;
					}
				}
			}
		}
	return $ret;
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

	// if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
	// 	return $product_reqs;
	// }
	if( isset( $_GET['page'] ) && 'edd-discounts' === $_GET['page'] ) {
   		return $product_reqs;	
	}	
	
	$product_request = (array) get_post_meta($ID,'_edd_discount_product_request',true);
	$product_request = array_map( 'absint', $product_request );
	$product_request = array_filter( array_values( $product_request) );	
	asort( $product_request );

	if( empty( $product_request ) ) {
		return $product_reqs;
	}
	return array_unique( wp_parse_args( $product_reqs, $product_request ) );
}



/**
		 * Filters the excluded downloads.
		 *
		 * @since 2.7
		 *
		 * @param array $excluded_products IDs of excluded products.
		 * @param int   $ID                Discount ID.
 */
add_filter( 'edd_get_discount_excluded_products','get_excluded_products',10,2); 
function get_excluded_products($excluded_products, $ID ) {					
	if( isset( $_GET['page'] ) && 'edd-discounts' === $_GET['page'] ) {
   		return $excluded_products;	
	}	

	$product_excluded = (array) get_post_meta($ID,'_edd_discount_product_excluded',true);
	$product_excluded = array_map( 'absint', $product_excluded );
	asort( $product_excluded );
	$product_excluded = array_filter( array_values( $product_excluded ) );

	if( empty( $product_excluded ) ) {
		return $excluded_products;
	}

	foreach ($excluded_products as $key => $main_product_id) {
		if( in_array( $main_product_id, $product_excluded) ) {
			unset( $excluded_products[$key] );
		}
	}
		return $product_excluded;
} 






// add_action('wp_head',function(){

// $a=array(array3=>'1',array3=>'2',);
// array_keys($a);
// vl($a);
// // if(in_array('abc', $a))
// // {
// // 	echo "okkkkkkkk";
// // }


// });