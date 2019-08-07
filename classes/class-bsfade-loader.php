<?php
/**
 * Advanced coupons for EDD Loader Doc comment
 *
 * PHP version 7
 *
 * @category PHP
 * @package  BSF Advanced Discount EDD
 * @author   Display Name <username@brainstormforce.com>
 * @license  http://brainstormforce.com
 * @link     http://brainstormforce.com
 */

if ( ! class_exists( 'BSFADE_Loader' ) ) :
	/**
	 * Advanced coupons for EDD Loader Doc comment
	 *
	 * @category PHP 7
	 * @package  BSF Advanced Discount EDD
	 * @author   Display Name <username@brainstormforce.com>
	 * @license  http://brainstormforce.com
	 * @link     http://brainstormforce.com
	 */
	class BSFADE_Loader {
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
			require_once BSF_ADE_ABSPATH . '/classes/class-bsfade-discount-options.php';
			require_once BSF_ADE_ABSPATH . '/classes/class-bsfade-discount-functions.php';
			add_action( 'admin_notices', array( $this, 'ade_is_edd_active' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'bsf_ade_script' ) );

		}

		/**
		 *  Register JS file for enable and disable product condition.
		 */
		public function bsf_ade_script() {

			wp_register_script( 'bsf_js', BSF_ADE_PLUGIN_URL . '/assets/js/ade_load_js.js', null, '1.0', true );

		}

		/**
		 *  Checks is Easy Digital Downloads plugin active or not if not install and activate .
		 */
		public function ade_is_edd_active() {
			$url = network_admin_url() . 'plugin-install.php?s=Easy+Digital+Downloads&tab=search&type=term';
			if ( ! is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {

				echo '<div class="notice notice-error">';
				echo '<p>The <strong>Advanced Discount Code for Easy Digital Downloads</strong> ' . esc_html__( 'plugin requires', 'advanced-discount-edd' ) . " <strong><a href='" . esc_url( $url ) . "'>Easy Digital Downloads</strong></a>" . esc_html__( ' plugin installed & activated.', 'advanced-discount-edd' ) . '</p>';
				echo '</div>';

			}
		}
	}

		BSFADE_Loader::get_instance();
endif;
