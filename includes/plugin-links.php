<?php
/**
 * File: plugin-links.php
 *
 * Handles adding of additional actions and link to plugin page.
 *
 * @package   Woo Pays
 * @author    Artevio
 * @license   GPL-2.0+
 * @link      https://artevio.com
 * @copyright 2021
 */

/**
 * Add settings link
 *
 * @param array $links_array - array of action links below the plugin name.
 */
function artevio_wc_pays_action_links( $links_array ) {

	$setings_url = get_admin_url() . 'admin.php?page=wc-settings&tab=checkout&section=artevio_pays';
	$logs_url    = get_admin_url() . 'admin.php?page=wc-status&tab=logs';

	// Add links at the beginning.
	array_unshift(
		$links_array,
		'<a href="' . $setings_url . '">' . _x( 'Settings', 'Plugins screen: Settings link', 'woo-pays' ) . '</a>',
		'<a href="' . $logs_url . '">' . _x( 'Logs', 'Plugins screen: Logs link', 'woo-pays' ) . '</a>'
	);

	return $links_array;
}
add_filter( 'plugin_action_links_' . WC_PAYS_PLUGIN_BASENAME_ARTEVIO, 'artevio_wc_pays_action_links', 10 );

/**
 * Docs and other links
 *
 * @param array  $links_array - array of links below the plugin description.
 * @param string $plugin_file_name - plugin folder and file name - woo-pays/woo-pays.php.
 */
function artevio_wc_pays_other_links( $links_array, $plugin_file_name ) {

	if ( strpos( $plugin_file_name, WC_PAYS_BASENAME_ARTEVIO ) ) {
		$links_array[] = '<a href="https://github.com/artevio/woo-pays" target="_blank">FAQ</a>';
		$links_array[] = '<a href="https://github.com/artevio/woo-pays/issues" target="_blank">Support</a>';
	}

	return $links_array;
}
add_filter( 'plugin_row_meta', 'artevio_wc_pays_other_links', 10, 2 );
