<?php
/**
 * Plugin Name: EDD Advanced Discounts
 * Description: Customize EDD discounts easily that will bring more sales and profit to your store. Offer discounts on selected product variations and set a maximum limit for cart total.
 * Version:     1.0.4
 * Author:      Pratik Chaskar
 * Author URI:  https://pratikchaskar.com
 * Text Domain: advanced-discount-edd
 *
 * @category PHP 7
 * @package  advanced-discount-edd
 * @author   Pratik Chaskar
 * @license  https://pratikchaskar.com
 * @link     https://pratikchaskar.com
 */

define( 'ADE_EDD_VER', '1.0.3' );
define( 'ADE_EDD_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'ADE_EDD_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'ADE_EDD_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

require_once plugin_dir_path( __FILE__ ) . '/classes/class-ade-edd-loader.php';

