<?php
/*
Plugin Name:	Woo Pays
Plugin URI:		https://artevio.com
Description:	Pays.cz payment gateway for WooCommerce
Version:		1.0.1
Author:			Artevio
Author URI:		https://artevio.com
License:		GPL-2.0+
License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:    woo-pays

This plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with This plugin. If not, see {URI to Plugin License}.
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WC_PAYS_DIR_ARTEVIO', plugin_dir_path( __FILE__ ) );
define( 'WC_PAYS_URL_ARTEVIO', plugin_dir_url( __FILE__ ) );
define( 'WC_PAYS_BASENAME_ARTEVIO', basename( __FILE__ ) );
define( 'WC_PAYS_PLUGIN_BASENAME_ARTEVIO', plugin_basename( __FILE__ ) );

/**
 * Includes
 */
require_once WC_PAYS_DIR_ARTEVIO . '/includes/plugin-links.php';
require_once WC_PAYS_DIR_ARTEVIO . '/includes/localization.php';
require_once WC_PAYS_DIR_ARTEVIO . '/includes/activation.php';
require_once WC_PAYS_DIR_ARTEVIO . '/includes/class-wc-pays-gateway.php';

/**
 * Activation hook
 */
register_activation_hook( __FILE__, 'artevio_wc_pays_activation' );

/**
 * Register PHP class as a WooCommerce payment gateway
 *
 * @param array $gateways - contains all gateways in WooCommerce.
 */
function artevio_wc_pays_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_Pays_Gateway';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'artevio_wc_pays_add_gateway_class' );
