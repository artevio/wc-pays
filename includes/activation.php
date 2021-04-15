<?php
/**
 * File: activation.php
 *
 * Handles activation actions of this plugin.
 *
 * @package   Woo Pays
 * @author    Artevio
 * @license   GPL-2.0+
 * @link      https://artevio.com
 * @copyright 2021
 */

/**
 * Function that handles the activation action.
 */
function artevio_wc_pays_activation() {

	global $wp_version;

	// Checks and messages.
	$checks = array(
		'Your Wordpress version is not compatible with WooCommerce Pays plugin which requires at least version 4.6. Please update your Wordpress.' => version_compare( $wp_version, '4.6', '<' ),
		'This plugin requires at least PHP version 5.5.0, your version: ' . PHP_VERSION . '. Please ask your hosting company to bring your PHP version up to date.' => version_compare( PHP_VERSION, '5.5.0', '<' ),
		'You need WooCommerce plugin installed and activated to run this plugin.' => ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ),
	);

	// Compare if check failed.
	foreach ( $checks as $message => $disable ) {
		if ( $disable ) {
			deactivate_plugins( WC_PAYS_BASENAME_ARTEVIO );
			wp_die( esc_html( $message ) );
		}
	}

	/*
	 * Create success page that gateway needs.
	 * Gateway uses this page as fallback when error with return url.
	 */
	$page_success_id = artevio_wc_create_page(
		_x( 'Payment Successful', 'Page Title', 'wc-pays' ),
		_x( 'Payment was successful. You will receive all details within 24 hours on your e-mail. If this does not happen, contact us.', 'Page Content', 'wc-pays' ),
		'payment-success'
	);

	/*
	 * Create error page that gateway needs.
	 * Gateway uses this page as fallback when error with return url.
	 */
	$page_error_id = artevio_wc_create_page(
		_x( 'Payment Error', 'Page Title', 'wc-pays' ),
		_x( 'There was an error with your payment. Please contact us.', 'Page Content', 'wc-pays' ),
		'payment-error'
	);

	// Get saved options.
	$saved_options = get_option( 'woocommerce_artevio_pays_settings' );

	// Set default settings.
	$new_options = array(
		'enabled'          => 'yes',
		'title'            => _x( 'Pays.cz', 'Gateway Title', 'wc-pays' ),
		'description'      => _x( 'Pay by credit card, fast bank transfer or QR code via the Pays.cz payment gateway.', 'Gateway Description', 'wc-pays' ),
		'testmode'         => 'no',
		'test_shop_id'     => '',
		'test_api_key'     => '',
		'live_shop_id'     => '',
		'live_api_key'     => '',
		'test_merchant_id' => '',
		'live_merchant_id' => '',
		'success_page'     => $page_success_id,
		'error_page'       => $page_error_id,
	);

	// Merge options.
	$all_options = array_merge( $new_options, $saved_options );

	// Save options.
	update_option( 'woocommerce_artevio_pays_settings', $all_options );

	// Flush rewrite rules.
	flush_rewrite_rules( true );
}

/**
 * Function that handles the activation action.
 *
 * @param string $title - name of the page.
 * @param string $content - body of the page.
 * @param string $slug - slug of the page.
 * @param int    $parent_id - parent id of the page.
 */
function artevio_wc_create_page( $title, $content = '', $slug = null, $parent_id = null ) {

	// Define page object.
	$page_object = null;

	// Check if page exists.
	if ( ! $slug ) {
		$page_object = get_page_by_title( $title, 'OBJECT', 'page' );
		$slug        = sanitize_title( $title );
	} else {
		$page_results = get_posts( array( 'name' => $slug ) );
		if ( is_array( $page_results ) ) {
			if ( count( $page_results ) > 0 ) {
				$page_object = $page_results[0];
			}
		}
	}

	// If found return ID.
	if ( ! empty( $page_object ) ) {
		return $page_object->ID;
	}

	// Insert new post.
	$page_id = wp_insert_post(
		array(
			'comment_status' => 'close',
			'ping_status'    => 'close',
			'post_author'    => 1,
			'post_title'     => ucwords( $title ),
			'post_name'      => $slug,
			'post_status'    => 'publish',
			'post_content'   => $content,
			'post_type'      => 'page',
			'post_parent'    => $parent_id,
		)
	);

	return $page_id;
}
