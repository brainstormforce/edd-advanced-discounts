<?php
/**
 * Plugin Name: Advanced Discount Code for Easy Digital Downloads
 * Description: Discounting options to EDD
 * Version:     1.0.0
 * Author:      Brainstorm Force
 * Author URI:  https://brainstormforce.com
 * Text Domain: advanced-discount-edd
 *
 * @category PHP 7
 * @package  advanced-discount-edd
 * @author   Display Name <username@brainstormforce.com>
 * @license  https://brainstormforce.com
 * @link     https://brainstormforce.com
 */

define( 'ADE_EDD_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'ADE_EDD_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'ADE_EDD_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
require_once plugin_dir_path( __FILE__ ) . '/classes/class-ade-loader.php';

