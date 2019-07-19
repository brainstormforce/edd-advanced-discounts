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
define( 'BSF_EAC_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'BSF_EAC_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
//wp_enqueue_style('bsf_eac_as_style');


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
add_action('edd_add_discount_form_bottom','add_products_option',10);
add_action('edd_add_discount_form_bottom','add_excluded_products',15);
add_action('edd_add_discount_form_bottom','add_max_option',20);

add_action('edd_edit_discount_form_bottom','edit_products_option',10,2);
add_action('edd_edit_discount_form_bottom','edit_excluded_products',15,2);
add_action( 'edd_edit_discount_form_bottom', 'edit_max_option',20,2);

add_action( 'init', 'edd_verify_nonce');
add_filter( 'edd_insert_discount', 'verify_add_nonce', 1, 20);
add_filter('edd_is_discount_min_met', 'is_max_met',11,2);
add_filter('edd_is_discount_products_req_met','is_product_request_met',11,2);


function add_max_option($discount_id=null)
{	
	?>
	<table class="form-table">
		<tbody>
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

function edit_max_option( $discount_id, $discount ) {

	$max_price = get_post_meta( $discount_id, '_edd_discount_max_price',true );
	
	?>
	<table class="form-table">
		<tbody>
	<tr>
		<th scope="row" valign="top">
			<label for="edd-max-cart-amount"><?php _e( 'Maximum Amount', 'easy-digital-downloads' ); ?></label>
		</th>
		<td>
			<input type="text" id="edd-max-cart-amount" name="max_price" value="<?php echo esc_attr($max_price); ?>" style="width: 40px "/>
			<p class="description"><?php _e( 'The maximum amount below which this discount can be used. Leave blank for no maximum.', 'easy-digital-downloads' ); ?></p>
		</td>	
	</tr>
		</tbody >
	</table>
	
	<?php
}


function add_products_option(){ ?>

<table class="form-table">
	<tbody>
		<tr class="new_options">
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
			<div id="edd-discount-product-condition" >
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
			<p class="description"><?php printf( __( 'Select %s relevant to this discount. If left blank, this discount can be used on any product.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
		</td>
	</tr>
	</tbody >
</table> 
	<?php
}

function edit_products_option($discount_id,$discount)
{
	$product_request=get_post_meta($discount_id, '_edd_discount_product_request',true);
	?>
	<table class="form-table">
		<tbody>
		<tr class="new_options" >
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
			<div id="edd-discount-product-conditions">
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

			<p class="description"><?php printf( __( 'Select %s relevant to this discount. If left blank, this discount can be used on any product.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
		</td>
	</tr>
	</tbody>
</table>
	<?php
}

function add_excluded_products()
{ ?>
	<table class="form-table">
		<tbody>
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
					<p class="description"><?php printf( __( '%s that this discount code cannot be applied to.', 'easy-digital-downloads' ), edd_get_label_plural() ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

function edit_excluded_products($discount_id,$discount){

	$product_excluded=get_post_meta($discount_id, '_edd_discount_product_excluded',true);
	?>
	<table class="form-table">
		<tbody>
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
	</tbody>
	</table> <?php 
}

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

function is_product_request_met($return = false, $discount_id = null)
{
	if($return)
	{
		$return=false;
		$product_condition=edd_get_discount_product_condition($discount_id);
		$product_request = get_post_meta($discount_id,'_edd_discount_product_request',true);
		$product_request = array_filter( array_values( $product_request ) );	

		$product_excluded = get_post_meta($discount_id,'_edd_discount_product_excluded',true);
	//	$product_coreexcluded = array_map( 'absint', $product_excluded );
	//	$product_excluded=array_merge($product_excluded,$product_coreexcluded);
		//$product_excluded = array_map( 'absint', $product_excluded );
		$product_excluded = array_filter( array_values( $product_excluded ) );
		//vl($product_excluded);
		$cart_items   = edd_get_cart_contents();
		// echo "*******";
		// vl($product_excluded);

		$cart_ids=array();
		foreach ($cart_items as $item) {
			if(isset($item['options']['price_id'])){
				array_push($cart_ids,implode('_',array($item['id'],$item['options']['price_id'])));
				//array_push($cart_ids,$item['id']);

			} else {
				array_push($cart_ids,$item['id']);
			}

		}
		$cart_ids     = array_values( $cart_ids );

		// core product ids

		 $cart_coreids     = $cart_items ? wp_list_pluck( $cart_items, 'id' ) : null;
		 $cart_coreids     = array_map( 'absint', $cart_coreids );
		 $cart_coreids     = array_values( $cart_coreids );
	
		if ( empty( $product_request ) && empty( $product_excluded ) ) {
			$return = true;
		}

		if (  ! $return && ! empty( $product_request )
		 ) {
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

		// if(!empty($product_excluded)){
		// 	foreach ($product_excluded as $excluded) {
		// 		if(edd_item_in_cart($excluded)){
		// 			$return=false;
		// 			break;
		// 		}
		// 	}
		// 	if(! $return){
		// 		edd_set_error( 'edd-discount-error', __( 'This discount is not valid for the cart contents.', 'easy-digital-downloads' ) );
		// 	}
		// }
  	//
		if ( ! empty( $product_excluded ) ) {
			// vl($product_excluded);
			// vl($cart_ids);
			// // vl($cart_coreids);
			if ( count( array_intersect( $cart_coreids, $product_excluded ) ) == count( $cart_coreids ) ||
				count( array_intersect( $cart_ids, $product_excluded ) ) == count( $cart_ids ) ) {
					$return = false;
					if ( !$return ) {
					edd_set_error( 'edd-discount-error', __( 'This discount is not valid for the cart contents.', 'easy-digital-downloads' ) );
				   }
			}
		}
				
		return $return;
	}
}



add_filter('edd_item_in_cart','item_in_cart',5,3);
function item_in_cart($ret=true,$download_id = 0, $options = array())
{
if(!$ret || $ret){
$cart = edd_get_cart_contents();

$ret = false;
if ( is_array( $cart ) ) {
			foreach ( $cart as $item ) {
				$pid=explode('_', $download_id);
				// vl($item['id']);
				// vl($pid[0]);
				if ( $item['id'] == $pid[0]) {
					// vl($item['options']['price_id']);
					// vl($pid[1]);
					if ( isset( $pid[1] )  && isset( $item['options']['price_id'] ) ) {
						if ( $pid[1] == $item['options']['price_id'] ) {
							//echo "here";
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

	if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		return $product_reqs;
	}
	$product_request = (array) get_post_meta($ID,'_edd_discount_product_request',true);
	$product_request = array_map( 'absint', $product_request );
	asort( $product_request );
	$product_request = array_filter( array_values( $product_request ) );	
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
	if( !defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		return $excluded_products;
	}

	$product_excluded = (array) get_post_meta($ID,'_edd_discount_product_excluded',true);
	$product_excluded = array_map( 'absint', $product_excluded );
	//$product_excluded=array_merge($product_excluded,$product_coreexcluded);
	asort( $product_excluded );
	$product_excluded = array_filter( array_values( $product_excluded ) );

	if( empty( $product_excluded ) ) {
		return $excluded_products;
	}

	foreach ($excluded_products as $key => $main_product_id) {
		if( in_array($main_product_id, $product_excluded) ) {
			unset( $excluded_products[$key] );
		}
	}

		//return array_unique(array_merge($excluded_products,$product_excluded));
		//return $excluded_products;
		// vl(array_unique(wp_parse_args($excluded_products,$product_excluded)));
		//vl($product_excluded);
		return $product_excluded;
} 
