<?php
/**
 * Advanced coupons for EDD Loader Doc comment
 *
 * PHP version 7
 *
 * @category PHP
 * @package  Advanced Discount EDD
 * @author   Display Name <username@brainstormforce.com>
 * @license  http://brainstormforce.com
 * @link     http://brainstormforce.com
 */

if ( ! class_exists( 'ADE_EDD_Loader' ) ) :
	/**
	 * Advanced coupons for EDD Loader Doc comment
	 *
	 * @category PHP 7
	 * @package  Advanced Discount EDD
	 * @author   Display Name <username@brainstormforce.com>
	 * @license  http://brainstormforce.com
	 * @link     http://brainstormforce.com
	 */
	class ADE_EDD_Loader {
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

			add_action( 'admin_notices', array( $this, 'ade_is_edd_active' ) );

			if( ! $this->is_edd_active() ) {
				return;
			}

			require_once ADE_EDD_ABSPATH . '/classes/class-ade-edd-discount-options.php';
			require_once ADE_EDD_ABSPATH . '/classes/class-ade-edd-discount-functions.php';
			
			add_action( 'admin_enqueue_scripts', array( $this, 'ade_script' ) );

		}

		/**
		 *  Register JS file for enable and disable product condition.
		 */
		public function ade_script() {

			wp_register_script( 'ade-edd-js', ADE_EDD_PLUGIN_URL . '/assets/js/ade_load_js.js', array( 'jquery' ), ADE_EDD_VER, true );

		}

		/**
		 *  Checks is Easy Digital Downloads plugin active or not.
		 */
		public function is_edd_active() {
			if ( is_plugin_active( 'easy-digital-downloads-pro/easy-digital-downloads.php' ) || is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
				return true;
			}
			return true;
		}

		/**
		 *  Checks is Easy Digital Downloads plugin active or not if not install and activate .
		 */
		public function ade_is_edd_active() {
			$url = network_admin_url() . 'plugin-install.php?s=Easy+Digital+Downloads&tab=search&type=term';
			if ( ! $this->is_edd_active() ) {

				echo '<div class="notice notice-error">';
				echo '<p>The <strong>EDD Advanced Discount</strong> ' . esc_html__( 'plugin requires', 'advanced-discount-edd' ) . " <strong><a href='" . esc_url( $url ) . "'>Easy Digital Downloads</strong></a>" . esc_html__( ' plugin installed & activated.', 'advanced-discount-edd' ) . '</p>';
				echo '</div>';

			}
		}
	}

		ADE_EDD_Loader::get_instance();
endif;
