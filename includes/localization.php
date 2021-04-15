<?php
/**
 * File: localization.php
 *
 * Handles loading of localization files.
 *
 * @package   Woo Pays
 * @author    Artevio
 * @license   GPL-2.0+
 * @link      https://artevio.com
 * @copyright 2021
 */

/**
 * Load plugin textdomain
 */
function artevio_wc_pays_load_textdomain_action() {
	load_plugin_textdomain( 'woo-pays', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'artevio_wc_pays_load_textdomain_action' );
