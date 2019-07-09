<?php
/**
 * Discounting options to EDD.
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */

/**
 *
 * @since  1.0.0
 * @return void
 */
function bsf_eac_settings_page() {
	add_submenu_page(
		'edit.php?post_type=download',
		'Advanced Coupons',
		'Advanced Coupons',
		'manage_options',
		'bsf_eac',
		'bsf_eac_page_html'
		
	);
}
add_action( 'admin_menu', 'bsf_eac_settings_page',1);

/**
 * Main Frontpage.
 *
 * @since  1.0.0
 * @return void
 */
function bsf_eac_page_html() {
	require_once BSF_EAC_ABSPATH . 'includes/bsfeac-frontend.php';
}
