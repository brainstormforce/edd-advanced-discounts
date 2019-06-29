<?php
/**
 * BSF EDD Advanced Coupons.
 *
 *
 * @category PHP
 * @package  EDD Advanced Coupons.
 * @author   Display Name <username@brainstormforce.com>
 * @license  http://brainstormforce.com
 * @link     http://brainstormforce.com
 */

if ( ! class_exists( 'BSFEAC_Loader' ) ) :
	/**
	 *
	 *
	 * @category PHP
	 * @package  EDD Advanced Coupons
	 * @author   Display Name <username@brainstormforce.com>
	 * @license  http://brainstormforce.com
	 * @link     http://brainstormforce.com
	 */
	class BSFEAC_Loader {
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

			require_once BSF_EAC_ABSPATH . 'includes/bsfeac-page.php';
		}
	}

		BSFEAC_Loader::get_instance();
endif;