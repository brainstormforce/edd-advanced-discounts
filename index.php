<?php
/**
 * Plugin Name: EDD Advanced Coupons
 * Description: Discounting options to EDD
 * Version:     1.0.0
 * Author:      Brainstorm Force
 * Author URI:  https://brainstormforce.com
 * Text Domain: easy-digital-downloads
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

add_action( 'admin_notices', 'is_edd_active' );
function is_edd_active()
 {
 	$url = network_admin_url() . 'plugin-install.php?s=Easy+Digital+Downloads&tab=search&type=term';
 	 if ( ! is_plugin_active ( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {

 	 	echo '<div class="notice notice-error">';
		echo '<p>The <strong>Edd Advanced Coupons</strong> ' . __( 'plugin requires', 'easy-digital-downloads' ) . " <strong><a href='" . $url . "'>Easy Digital Downloads</strong></a>" . __( ' plugin installed & activated.', 'easy-digital-downloads' ) . '</p>';
		echo '</div>';
        	
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

	$max_price = get_post_meta( $discount_id, '_edd_discount_max_price',true );
	$condition_dis = empty( $product_request ) ? 'style="display:none;"' : '';

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
	 $arr = array(
		'max_price' => $maxprice,
		'product_request' => $productrequest	
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

		$cart_items   = edd_get_cart_contents();

		if ( empty( $product_request )  ) {
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

		return $return;
	}
}



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



