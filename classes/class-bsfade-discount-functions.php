<?php
/**
 * The Discount Function Class
 *
 * @since      1.0.0
 * @package    BSF Advanced Discount EDD
 * @author     Brainstorm Force.
 */

/**
 * Class for checking product condition and maximum amount condition to apply discount.
 *
 * The class that contains all functions for checking condition.
 *
 * @since 1.0.0
 */
class BSFADE_Discount_Functions {
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
		add_filter( 'edd_is_discount_min_met', array( $this, 'ade_is_max_met' ), 11, 2 );
		add_filter( 'edd_is_discount_products_req_met', array( $this, 'ade_is_product_request_met' ), 11, 2 );
		add_filter( 'edd_item_in_cart', array( $this, 'ade_item_in_cart' ), 10, 3 );
		add_filter( 'edd_get_discount_product_reqs', array( $this, 'ade_get_product_req' ), 10, 2 );

	}

	/**
	 *  If the Maximum cart amount has been met to satisify the discount.
	 *
	 * @param bool $return Are the maximum condition met or not.
	 * @param int  $discount_id Discount ID.
	 * @return bool $return Is the maximun cart amount met or not.
	 */
	public function ade_is_max_met( $return = false, $discount_id = null ) {
		if ( $return ) {

			$is_discount_max_met = false;
			$discount            = new EDD_Discount( $discount_id );
			$max_price           = get_post_meta( $discount_id, '_edd_discount_max_price', true );
			$cart_amount         = edd_get_cart_discountable_subtotal( $discount_id );

			if ( (float) $cart_amount <= (float) $max_price ) {
				$is_discount_max_met = true;
			} elseif ( 0 == (float) $max_price ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
				$is_discount_max_met = true;//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
			} else {
				edd_set_error( 'edd-discount-error', sprintf( esc_html__( 'Maximum order of %s not met.', 'advanced-discount-edd' ), edd_currency_filter( edd_format_amount( $max_price ) ) ) );//PHPCS:ignore:WordPress.WP.I18n.MissingTranslatorsComment
			}
				return $is_discount_max_met;

		}
	}

	/**
	 * Whether the product requirements are met for the discount to hold.
	 *
	 * @since 2.7
	 *
	 * @param bool $return            Are the product requirements met or not.
	 * @param int  $discount_id       Discount ID.
	 * @return bool   $return         Are the product requirements met or not.
	 */
	public function ade_is_product_request_met( $return = false, $discount_id = null ) {
		if ( $return ) {
			$return            = false;
			$product_condition = edd_get_discount_product_condition( $discount_id );

			$product_request = (array) get_post_meta( $discount_id, '_edd_discount_product_request', true );
			$product_request = array_filter( array_values( $product_request ) );

			$cart_items = edd_get_cart_contents();

			if ( empty( $product_request ) ) {

				$return = true;
			}

			if ( ! $return && ! empty( $product_request ) ) {
				switch ( $product_condition ) {
					case 'all':
						$return = true;

						foreach ( $product_request as $download_id ) {

							if ( empty( $download_id ) ) {
								continue;
							}

							if ( ! edd_item_in_cart( $download_id ) ) {
								$return = false;
								if ( ! $return ) {
									edd_set_error( 'edd-discount-error', esc_html__( 'The product requirements for this discount are not met.', 'advanced-discount-edd' ) );
								}
								break;

							}
						}

						break;

					default:
						foreach ( $product_request as $download_id ) {
							if ( empty( $download_id ) ) {
								continue;
							}
							if ( edd_item_in_cart( $download_id ) ) {
								$return = true;
								break;
							}
						}

						if ( ! $return ) {
							edd_set_error( 'edd-discount-error', esc_html__( 'The product requirements for this discount are not met.', 'advanced-discount-edd' ) );
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
	 * @param bool  $ret true if item in cart.
	 * @param int   $download_id Download ID of the item to check.
	 * @param array $options array of cart contents.
	 * @return bool $ret true if item in cart..
	 */
	public function ade_item_in_cart( $ret = true, $download_id = 0, $options = array() ) {
		if ( ! $ret || $ret ) {
			$cart = edd_get_cart_contents();
			$ret  = false;
			if ( is_array( $cart ) ) {
				foreach ( $cart as $item ) {
					$pid = explode( '_', $download_id );
					if ( $item['id'] == $pid[0] ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
						if ( isset( $pid[1] ) && isset( $item['options']['price_id'] ) ) {
							if ( $pid[1] == $item['options']['price_id'] ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
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
	 * Get product requirements.
	 *
	 * @param array $product_reqs IDs of required products.
	 * @param int   $id           Discount ID.
	 * @return array of product requirements
	 */
	public function ade_get_product_req( $product_reqs, $id ) {
		if ( isset( $_GET['page'] ) && 'edd-discounts' === $_GET['page'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
			return $product_reqs;
		}

			$product_request = (array) get_post_meta( $id, '_edd_discount_product_request', true );
			$product_request = array_map( 'absint', $product_request );
			$product_request = array_filter( array_values( $product_request ) );
			asort( $product_request );

		if ( empty( $product_request ) ) {
			return $product_request;
		}
			return array_unique( wp_parse_args( $product_reqs, $product_request ) );
	}
}

		BSFADE_Discount_Functions::get_instance();

