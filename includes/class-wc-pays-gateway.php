<?php
/**
 * File: class-wc-pays-gateway.php
 *
 * Handles full logic of payment gateway.
 *
 * @package   Woo Pays
 * @author    Artevio
 * @license   GPL-2.0+
 * @link      https://artevio.com
 * @copyright 2021
 */

/**
 * Gateway class init
 */
function artevio_wc_pays_init_gateway_class() {
	/**
	 * Pays.cz Gateway class
	 * details about integration can be found here: https://www.pays.cz/docs/pays-implementacni-manual-platebni-brany.pdf
	 */
	class WC_Pays_Gateway extends WC_Payment_Gateway {

		/**
		 * Class constructor
		 */
		public function __construct() {

			$this->id                 = 'artevio_pays'; // Payment gateway plugin ID.
			$this->icon               = WC_PAYS_URL_ARTEVIO . 'assets/images/logo.png'; // URL of the icon that will be displayed on checkout page near your gateway name.
			$this->has_fields         = false; // In case you need a custom credit card form.
			$this->method_title       = _x( 'Pays.cz', 'Gateway Title', 'wc-pays' );
			$this->method_description = _x( 'Receive payments by credit card, fast bank transfer or QR code via the Pays.cz payment gateway.', 'Gateway Admin Description', 'wc-pays' ); // Will be displayed on the options page.

			/*
			 * Gateways can support subscriptions, refunds, saved payment methods,
			 * Pays.cz can now support only simple payments.
			 */
			$this->supports = array(
				'products',
			);

			// Load the settings.
			$this->init_settings();
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->enabled      = $this->get_option( 'enabled' );
			$this->testmode     = 'yes' === $this->get_option( 'testmode' );
			$this->merchant_id  = $this->testmode ? $this->get_option( 'test_merchant_id' ) : $this->get_option( 'live_merchant_id' );
			$this->shop_id      = $this->testmode ? $this->get_option( 'test_shop_id' ) : $this->get_option( 'live_shop_id' );
			$this->api_key      = $this->testmode ? $this->get_option( 'test_api_key' ) : $this->get_option( 'live_api_key' );
			$this->success_page = $this->get_option( 'success_page' );
			$this->error_page   = $this->get_option( 'error_page' );

			/*
			 * Method with all the options fields
			 * for WooCommerce setings.
			 */
			$this->init_form_fields();

			// This action hook saves the settings.
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

			// Register a webhook here.
			add_action( 'woocommerce_api_pays-payment', array( $this, 'webhook' ) );

		}

		/**
		 * Plugin options
		 */
		public function init_form_fields() {

			// Mail data.
			$body    = _x( 'We are sending communication addresses.', 'Gateway settings Email body', 'wc-pays' ) . "\n\n" .
					_x( 'Payment confirmation URL:', 'Gateway settings Email body', 'wc-pays' ) . ' ' . get_site_url( null, 'wc-api/pays-payment' ) . "\n" .
					_x( 'Successful payment page:', 'Gateway settings Email body', 'wc-pays' ) . ' ' . get_permalink( $this->success_page ) . "\n" .
					_x( 'Incorrect payment page:', 'Gateway settings Email body', 'wc-pays' ) . ' ' . get_permalink( $this->error_page );
			$subject = _x( 'E-shop settings', 'Gateway settings Email subject', 'wc-pays' ) . ' ' . _x( 'Merchant:', 'Gateway settings Email subject', 'wc-pays' ) . $this->merchant_id . ' ' . _x( 'Shop:', 'Gateway settings Email subject', 'wc-pays' ) . $this->shop_id;
			$body    = rawurlencode( htmlspecialchars_decode( $body ) );
			$subject = rawurlencode( htmlspecialchars_decode( $subject ) );

			$this->form_fields = array(
				'enabled'          => array(
					'title'       => _x( 'Enable/Disable', 'Gateway settings Enabled title', 'wc-pays' ),
					'label'       => _x( 'Enable Pays.cz Gateway', 'Gateway settings Enabled label', 'wc-pays' ),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no',
				),
				'title'            => array(
					'title'       => _x( 'Title', 'Gateway settings Title title', 'wc-pays' ),
					'description' => _x( 'This controls the title which the user sees during checkout.', 'Gateway settings Title label', 'wc-pays' ),
					'default'     => _x( 'Pays.cz', 'Gateway settings Title default', 'wc-pays' ),
					'type'        => 'text',
					'desc_tip'    => true,
				),
				'description'      => array(
					'title'       => _x( 'Description', 'Gateway settings Description title', 'wc-pays' ),
					'description' => _x( 'This controls the description which the user sees during checkout.', 'Gateway settings Description description', 'wc-pays' ),
					'default'     => _x( 'Pay with your credit card, quick bank transfer or QR code via Pays.cz payment gateway.', 'Gateway settings Description default', 'wc-pays' ),
					'type'        => 'textarea',
				),
				'testmode'         => array(
					'title'       => _x( 'Test mode', 'Gateway settings Test Mode title', 'wc-pays' ),
					'label'       => _x( 'Enable Test Mode', 'Gateway settings Test Mode label', 'wc-pays' ),
					'description' => _x( 'Place the payment gateway in test mode using test API keys.', 'Gateway settings Test Mode description', 'wc-pays' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'desc_tip'    => true,
				),
				'test_merchant_id' => array(
					'title' => _x( 'Test Merchant ID', 'Gateway settings Test Merchant ID title', 'wc-pays' ),
					'type'  => 'number',
				),
				'test_shop_id'     => array(
					'title' => _x( 'Test Shop ID', 'Gateway settings Test Shop ID title', 'wc-pays' ),
					'type'  => 'number',
				),
				'test_api_key'     => array(
					'title' => _x( 'Test API Key', 'Gateway settings Test API Key title', 'wc-pays' ),
					'type'  => 'text',
				),
				'live_merchant_id' => array(
					'title' => _x( 'Live Merchant ID', 'Gateway settings Live Merchant ID title', 'wc-pays' ),
					'type'  => 'number',
				),
				'live_shop_id'     => array(
					'title' => _x( 'Live Shop ID', 'Gateway settings Live Shop ID title', 'wc-pays' ),
					'type'  => 'number',
				),
				'live_api_key'     => array(
					'title' => _x( 'Live API Key', 'Gateway settings Live Shop ID title', 'wc-pays' ),
					'type'  => 'text',
				),
				'success_page'     => array(
					'title'   => _x( 'Payment Success Page', 'Gateway settings Payment Success Page title', 'wc-pays' ),
					'type'    => 'select',
					'options' => $this->get_pages(),
				),
				'error_page'       => array(
					'title'   => _x( 'Payment Error Page', 'Gateway settings Payment Error Page title', 'wc-pays' ),
					'type'    => 'select',
					'options' => $this->get_pages(),
				),
				'send_pays_urls'   => array(
					'title'             => _x( 'Send URLs to Pays.cz', 'Gateway settings Pays URLs title', 'wc-pays' ),
					'type'              => 'button',
					'default'           => _x( 'Send needed details and URLs to Pays', 'Gateway settings Pays URLs button text', 'wc-pays' ),
					'description'       => _x( 'Click on this button after you will save the settings.', 'Gateway settings Pays URLs description', 'wc-pays' ),
					'custom_attributes' => array(
						'onclick' => 'window.location = "mailto:podpora@pays.cz&subject=' . $subject . '&body=' . $body . '";',
					),
				),
			);

		}

		/**
		 * Get all pages as options for Page Select
		 */
		public function get_pages() {
			$all_pages = get_pages();

			foreach ( $all_pages as $page ) {
				$page_hierarchy_array[ $page->ID ] = $page->post_title;
			}

			return $page_hierarchy_array;
		}

		/**
		 * Add description for TEST mode
		 */
		public function payment_fields() {

			// Display TEST MODE description in gateway description.
			if ( $this->description ) {
				// Instructions for test mode.
				if ( $this->testmode ) {
					$this->description = _x( 'TEST PAYMENT:', 'Test Mode frontend gateway description', 'wc-pays' ) . ' ' . $this->description;
					$this->description = trim( $this->description );
				}
				// Display the description with <p> tags etc.
				echo esc_html( wp_kses_post( $this->description ) );
			}

		}

		/**
		 * Processing the payments
		 *
		 * @param int $order_id - WooCommerce id of the order.
		 */
		public function process_payment( $order_id ) {

			// We need it to get any order detailes.
			$order = wc_get_order( $order_id );

			if ( ! in_array( $order->get_currency(), array( 'CZK', 'USD', 'EUR' ), true ) ) {
				return array(
					'result'   => 'fail',
					'redirect' => '',
				);
			}

			$link = add_query_arg(
				array(
					'Merchant'            => $this->merchant_id,
					'Shop'                => $this->shop_id,
					'Currency'            => $order->get_currency(),
					'Amount'              => ( $order->get_total() * 100 ),
					'Email'               => $order->get_billing_email(),
					'MerchantOrderNumber' => $order_id,
				),
				'https://www.pays.cz/paymentorder'
			);

			if ( wc_tax_enabled() ) {
				$base  = array();
				$taxes = array();
				foreach ( $order->get_items() as $item ) {
					$tax_rate = (int) round( $item['line_tax'] / $item['line_total'] * 100 );
					if ( ! isset( $base[ $tax_rate ] ) ) {
						$base[ $tax_rate ] = 0;
					}
					if ( ! isset( $taxes[ $tax_rate ] ) ) {
						$taxes[ $tax_rate ] = 0;
					}
					$base[ $tax_rate ]  += (int) round( $item['line_total'] * 100 );
					$taxes[ $tax_rate ] += (int) round( $item['line_tax'] * 100 );
				}

				// Based on https://www.pays.cz/api/public/vatsetup/ tax standards.
				$result = array(
					21 => 'Standard',
					15 => '1Reduced',
					10 => '2Reduced',
					0  => 'Zero',
				);

				foreach ( $result as $key => $value ) {
					if ( isset( $base[ $key ], $taxes[ $key ] ) ) {
						if ( 0 !== $key ) {
							$link = add_query_arg(
								array(
									'TaxBase' . $value => $base[ $key ],
									'TaxVAT' . $value  => $taxes[ $key ],
								),
								$link
							);
						} else {
							$link = add_query_arg(
								'Tax' . $value,
								$base[ $key ],
								$link
							);
						}
					}
				}
			}

			$link = add_query_arg(
				'ReturnURL',
				rawurlencode( add_query_arg( 'utm_nooverride', '1', $order->get_checkout_order_received_url() ) ),
				$link
			);

			return array(
				'result'   => 'success',
				'redirect' => $link,
			);

		}

		/**
		 * Webhook for confirming the payment
		 */
		public function webhook() {

			$payment_id                 = isset( $_GET['PaymentOrderID'] ) ? intval( $_GET['PaymentOrderID'] ) : 0;
			$payment_order_number       = isset( $_GET['MerchantOrderNumber'] ) ? intval( $_GET['MerchantOrderNumber'] ) : 0;
			$payment_status             = isset( $_GET['PaymentOrderStatusID'] ) ? intval( $_GET['PaymentOrderStatusID'] ) : 0;
			$payment_currency           = isset( $_GET['CurrencyID'] ) ? sanitize_text_field( wp_unslash( $_GET['CurrencyID'] ) ) : '';
			$payment_ammount            = isset( $_GET['Amount'] ) ? floatval( $_GET['Amount'] ) : 0;
			$payment_base_units         = isset( $_GET['CurrencyBaseUnits'] ) ? floatval( $_GET['CurrencyBaseUnits'] ) : 0;
			$payment_status_description = isset( $_GET['PaymentOrderStatusDescription'] ) ? sanitize_text_field( wp_unslash( $_GET['PaymentOrderStatusDescription'] ) ) : '';
			$payment_hash               = isset( $_GET['hash'] ) ? sanitize_text_field( wp_unslash( $_GET['hash'] ) ) : '';

			// Compare hash.
			$data2hash    = $payment_id . $payment_order_number . $payment_status . $payment_currency . $payment_ammount . $payment_base_units;
			$compare_hash = hash_hmac( 'md5', $data2hash, $this->api_key );

			// Load woocommerce logger.
			$logger = wc_get_logger();

			// Get order.
			$order = wc_get_order( $payment_order_number );

			// If hash not match continue only with logging.
			if ( $payment_hash === $compare_hash ) {

				// If different amout or different currency.
				if ( floatval( $order->get_total() ) !== $payment_ammount / $payment_base_units || $order->get_currency() !== $payment_currency ) {
					$order->update_status( 'failed', _x( 'Wrong amount paid.', 'Note about wrong payment', 'wc-pays' ) );

					// Payment status 3 = order completed.
				} elseif ( 3 === $payment_status ) {
					$order->payment_complete( $payment_id );
					$order->update_status( 'completed' );
					$order->reduce_order_stock();

					// Payment status 2 and other = order failed.
				} else {
					$order->update_status( 'failed' );
				}

				$order->add_order_note( $payment_status_description . ' PaymentOrderID: ' . $payment_id . ' PaymentOrderStatusID: ' . $payment_status, false );
			}

			// Create debug data array.
			$debug_data = array(
				'source'       => 'wc-pays',
				'payment_hash' => $payment_hash,
				'compare_hash' => $compare_hash,
			);

			// Merge debug data with webhook $_GET.
			$debug_array = array_merge( $debug_data, $_GET );

			// Log webhook debug.
			$logger->info(
				'Webhook fired for Order ID ' . $payment_order_number . ' Details: ' . wc_print_r( $debug_array, true ),
				$debug_array
			);

			wp_die( 'OK', '', 202 );
		}
	}
}
add_action( 'plugins_loaded', 'artevio_wc_pays_init_gateway_class' );
