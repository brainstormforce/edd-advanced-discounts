<?php
/**
 * The Add Option Class
 *
 * @since      1.0.0
 * @package    Advanced Discount EDD
 * @author     Brainstorm Force.
 */

/**
 * Class for adding two new options or conditions to apply discount to cart.
 *
 * The class that contains all functions to add two new optiona and save it in database.
 *
 * @since 1.0.0
 */
class ADE_EDD_Discount_Options {
	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;
	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'edd_add_discount_form_bottom', array( $this, 'ade_add_new_option' ), 10 );
		add_action( 'edd_edit_discount_form_bottom', array( $this, 'ade_edit_new_option' ), 10, 2 );
		add_filter( 'edd_insert_discount', array( $this, 'ade_add_opt_meta' ), 20, 1 );
		add_action( 'init', array( $this, 'ade_update_data' ) );

	}

	/**
	 *  Function for adding new option in add new discount page.
	 */
	public function ade_add_new_option() {
		wp_enqueue_script( 'ade-edd-js' ); ?>
			<table class="form-table">
				<tbody>

				<tr class="new-option">
					<th scope="row" valign="top">
						<label for="edd-products"><?php printf( esc_html__( 'Product Requirements', 'advanced-discount-edd' ), esc_attr( edd_get_label_singular() ) ); ?></label>
					</th>
					<td>
						<p>
							<?php
							echo EDD()->html->product_dropdown(//PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
								array(
									'name'        => 'product_request[]',
									'id'          => 'product_request',
									'multiple'    => true,
									'chosen'      => true,
									'variations'  => true,
									'placeholder' => sprintf( esc_html__( 'Select one or more products', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ),
								)
							);
							?>
							<br/>
						</p>
						<div id="edd-discount-product-conditions_new" style="display: none;" >
							<p>
								<select id="edd-product-new-condition" name="product_condition">
									<option value="all"><?php printf( esc_html__( 'Cart must contain all selected products', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ); ?></option>
									<option value="any"><?php printf( esc_html__( 'Cart needs one or more of the selected products', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ); ?></option>
								</select>
							</p>
							<p>
								<label>
									<input type="radio" class="tog" name="not_global" value="0" checked="checked"/>
									<?php esc_html_e( 'Apply discount to entire purchase.', 'advanced-discount-edd' ); ?>
								</label><br/>
								<label>
									<input type="radio" class="tog" name="not_global" value="1"/>
									<?php printf( esc_html__( 'Apply discount only to selected products.', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ); ?>
								</label>
							</p>
						</div>
						<p class="description"><?php printf( esc_html__( 'Select products relevant to this discount. If left blank, this discount can be used on any product.', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ); ?></p>
					</td>
				</tr>	

				<tr class="new-option">
					<th scope="row" valign="top">
						<label for="edd-max-cart-amount"><?php esc_html_e( 'Maximum Amount', 'advanced-discount-edd' ); ?></label>
					</th>
					<td>
						<input type="text" id="edd-max-cart-amount" name="max_price" value=" " />
						<p class="description"><?php esc_html_e( 'The maximum amount below which this discount can be used. Leave blank for no maximum.', 'advanced-discount-edd' ); ?></p>
					</td>
				</tr>	
				</tbody>
			</table>

			<?php
	}
	/**
	 *  Function for adding new option in edit discount page.
	 *
	 * @param int   $discount_id Discount ID.
	 * @param array $discount Discount Data.
	 */
	public function ade_edit_new_option( $discount_id, $discount ) {
		$product_request = (array) get_post_meta( $discount_id, '_edd_discount_product_request', true );
		$product_request = array_filter( array_values( $product_request ) );
		$max_price       = get_post_meta( $discount_id, '_edd_discount_max_price', true );
		wp_enqueue_script( 'ade-edd-js' );
		$condition_dis = empty( $product_request ) ? 'style="display:none;"' : '';
		?>

			<table class="form-table">
				<tbody>
				<tr class="new-option">
					<th scope="row" valign="top">
						<label for="edd-products"><?php printf( esc_html__( 'Product Requirements', 'advanced-discount-edd' ), esc_attr( edd_get_label_singular() ) ); ?></label>
					</th>
					<td>
						<p>
							<?php

							echo EDD()->html->product_dropdown(
								array(
									'name'        => 'product_request[]',
									'id'          => 'product_request',
									'selected'    => $product_request,
									'multiple'    => true,
									'chosen'      => true,
									'variations'  => true,
									'placeholder' => sprintf( esc_html__( 'Select one or more products', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ),
								)
							);
							?>
							<br/>
						</p>
						<div id="edd-discount-product-conditions_new"  <?php echo $condition_dis; //PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
									<p>
										<select id="edd-product-condition" name="product_condition">
											<option value="all"<?php selected( 'all', edd_get_discount_product_condition( $discount_id ) ); ?>><?php printf( esc_html__( 'Cart must contain all selected products', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ); ?></option>
											<option value="any"<?php selected( 'any', edd_get_discount_product_condition( $discount_id ) ); ?>><?php printf( esc_html__( 'Cart needs one or more of the selected products', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ); ?></option>
										</select>
									</p>
									<p>
										<label>
											<input type="radio" class="tog" name="not_global" value="0"<?php checked( false, edd_is_discount_not_global( $discount_id ) ); ?>/>
										<?php esc_html_e( 'Apply discount to entire purchase.', 'advanced-discount-edd' ); ?>
										</label><br/>
										<label>
											<input type="radio" class="tog" name="not_global" value="1"<?php checked( true, edd_is_discount_not_global( $discount_id ) ); ?>/>
										<?php printf( esc_html__( 'Apply discount only to selected products.', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ); ?>
										</label>
									</p>
						</div>

						<p class="description"><?php printf( esc_html__( 'Select products relevant to this discount. If left blank, this discount can be used on any product.', 'advanced-discount-edd' ), esc_attr( edd_get_label_plural() ) ); ?></p>
					</td>
				</tr>

				<tr class="new-option">
					<th scope="row" valign="top">
						<label for="edd-max-cart-amount"><?php esc_html_e( 'Maximum Amount', 'advanced-discount-edd' ); ?></label>
					</th>
					<td>
						<input type="text" id="edd-max-cart-amount" name="max_price" value="<?php echo esc_attr( $max_price ); ?>" style="width: 40px "/>
						<p class="description"><?php esc_html_e( 'The maximum amount below which this discount can be used. Leave blank for no maximum.', 'advanced-discount-edd' ); ?></p>
					</td>	
				</tr>


				</tbody>
			</table>
		<?php
	}

	/**
	 *  Checks and verify nonce to insert data in existing databse or add new data option in meta data.
	 *
	 * @param array $meta User Input For Discount.
	 * @return array $meta Meta Data Saved In Database.
	 */
	public function ade_add_opt_meta( $meta ) {

		if ( isset( $_POST['edd-discount-nonce'] ) && wp_verify_nonce( $_POST['edd-discount-nonce'], 'edd_discount_nonce' ) ) {
			$maxprice       = ! empty( $_POST['max_price'] ) ? floatval( $_POST['max_price'] ) : 0;
			$productrequest = ! empty( $_POST['product_request'] ) ? $_POST['product_request'] : array();
			$productrequest = array_map( 'esc_attr', $productrequest );
			$arr            = array(
				'max_price'       => $maxprice,
				'product_request' => $productrequest,
			);

				$meta = array_merge( $arr, $meta );
				return $meta;

		}

		return $meta;
	}

	/**
	 *  Checks nonce to update metadata in database.
	 */
	public function ade_update_data() {

		if ( isset( $_POST['edd-discount-nonce'] ) && wp_verify_nonce( $_POST['edd-discount-nonce'], 'edd_discount_nonce' ) ) {
			$id   = ! empty( $_GET['discount'] ) ? sanitize_key( $_GET['discount'] ) : '';
			$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : null;
			if ( 'edd-discounts' !== $page ) {
				return;
			}
			$maxprice = ( ! empty( $_POST['max_price'] ) ? floatval( $_POST['max_price'] ) : 0 );
			update_post_meta( $id, '_edd_discount_max_price', $maxprice );
			$productrequest = ( ! empty( $_POST['product_request'] ) ? $_POST['product_request'] : array() );
			$productrequest = array_map( 'esc_attr', $productrequest );
			update_post_meta( $id, '_edd_discount_product_request', $productrequest );
		}

	}


}

		ADE_EDD_Discount_Options::get_instance();

