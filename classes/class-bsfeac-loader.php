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
			add_action('admin_enqueue_scripts',array($this,'bsf_eac_script'));


		}
		public function bsf_eac_script()
		{
			//wp_register_style( 'bsf_eac_as_style', BSF_EAC_PLUGIN_URL . '/assets/css/eac-style.css', null,'1.0', false ); 
		wp_enqueue_script('bsf_eac_js', BSF_EAC_PLUGIN_URL . '/assets/js/myscript.js', null,'1.0', true);

		}
	}

		BSFEAC_Loader::get_instance();
endif;