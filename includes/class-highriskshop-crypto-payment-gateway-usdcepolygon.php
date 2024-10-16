<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('plugins_loaded', 'init_highriskshopcryptogateway_usdcepolygon_gateway');

function init_highriskshopcryptogateway_usdcepolygon_gateway() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

class HighRiskShop_Crypto_Payment_Gateway_Usdcepolygon extends WC_Payment_Gateway {

    public function __construct() {
        $this->id                 = 'highriskshop-crypto-payment-gateway-usdcepolygon';
        $this->icon = esc_url(plugin_dir_url(__DIR__) . 'static/usdcepolygon.png');
        $this->method_title       = esc_html__('USD Coin (Bridged) polygon Crypto Payment Gateway With Instant Payouts', 'crypto-payment-gateway'); // Escaping title
        $this->method_description = esc_html__('USD Coin (Bridged) polygon Crypto Payment Gateway With Instant Payouts to your polygon_usdc.e wallet. Allows you to accept crypto polygon/usdc.e payments without sign up and without KYC.', 'crypto-payment-gateway'); // Escaping description
        $this->has_fields         = false;

        $this->init_form_fields();
        $this->init_settings();

        $this->title       = sanitize_text_field($this->get_option('title'));
        $this->description = sanitize_text_field($this->get_option('description'));

        // Use the configured settings for redirect and icon URLs
        $this->usdcepolygon_wallet_address = sanitize_text_field($this->get_option('usdcepolygon_wallet_address'));
		$this->usdcepolygon_blockchain_fees = $this->get_option('usdcepolygon_blockchain_fees');
        $this->icon_url     = sanitize_url($this->get_option('icon_url'));

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_before_thankyou', array($this, 'before_thankyou_page'));
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => esc_html__('Enable/Disable', 'crypto-payment-gateway'), // Escaping title
                'type'    => 'checkbox',
                'label'   => esc_html__('Enable polygon_usdc.e payment gateway', 'crypto-payment-gateway'), // Escaping label
                'default' => 'no',
            ),
            'title' => array(
                'title'       => esc_html__('Title', 'crypto-payment-gateway'), // Escaping title
                'type'        => 'text',
                'description' => esc_html__('Payment method title that users will see during checkout.', 'crypto-payment-gateway'), // Escaping description
                'default'     => esc_html__('USD Coin (Bridged) polygon', 'crypto-payment-gateway'), // Escaping default value
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => esc_html__('Description', 'crypto-payment-gateway'), // Escaping title
                'type'        => 'textarea',
                'description' => esc_html__('Payment method description that users will see during checkout.', 'crypto-payment-gateway'), // Escaping description
                'default'     => esc_html__('Pay via crypto USD Coin (Bridged) polygon polygon_usdc.e', 'crypto-payment-gateway'), // Escaping default value
                'desc_tip'    => true,
            ),
            'usdcepolygon_wallet_address' => array(
                'title'       => esc_html__('Wallet Address', 'crypto-payment-gateway'), // Escaping title
                'type'        => 'text',
                'description' => esc_html__('Insert your polygon/usdc.e wallet address to receive instant payouts.', 'crypto-payment-gateway'), // Escaping description
                'desc_tip'    => true,
            ),
			'usdcepolygon_blockchain_fees' => array(
                'title'       => esc_html__('Customer Pays Blockchain Fees', 'crypto-payment-gateway'), // Escaping title
                'type'        => 'checkbox',
                'description' => esc_html__('Add estimated blockchian fees to the order total.', 'crypto-payment-gateway'), // Escaping description
                'desc_tip'    => true,
				'default' => 'no',
            ),
        );
    }
	
	 // Add this method to validate the wallet address in wp-admin
    public function process_admin_options() {
		if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'woocommerce-settings')) {
    WC_Admin_Settings::add_error(__('Nonce verification failed. Please try again.', 'crypto-payment-gateway'));
    return false;
}
        $highriskshopcryptogateway_usdcepolygon_admin_wallet_address = isset($_POST[$this->plugin_id . $this->id . '_usdcepolygon_wallet_address']) ? sanitize_text_field( wp_unslash( $_POST[$this->plugin_id . $this->id . '_usdcepolygon_wallet_address'])) : '';

        // Check if wallet address is empty
        if (empty($highriskshopcryptogateway_usdcepolygon_admin_wallet_address)) {
		WC_Admin_Settings::add_error(__('Invalid Wallet Address: Please insert a valid USD Coin (Bridged) polygon wallet address.', 'crypto-payment-gateway'));
            return false;
		}

        // Proceed with the default processing if validations pass
        return parent::process_admin_options();
    }
	
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        $highriskshopcryptogateway_usdcepolygon_currency = get_woocommerce_currency();
		$highriskshopcryptogateway_usdcepolygon_total = $order->get_total();
		$highriskshopcryptogateway_usdcepolygon_nonce = wp_create_nonce( 'highriskshopcryptogateway_usdcepolygon_nonce_' . $order_id );
		$highriskshopcryptogateway_usdcepolygon_callback = add_query_arg(array('order_id' => $order_id, 'nonce' => $highriskshopcryptogateway_usdcepolygon_nonce,), rest_url('highriskshopcryptogateway/v1/highriskshopcryptogateway-usdcepolygon/'));
		$highriskshopcryptogateway_usdcepolygon_email = urlencode(sanitize_email($order->get_billing_email()));
		$highriskshopcryptogateway_usdcepolygon_status_nonce = wp_create_nonce( 'highriskshopcryptogateway_usdcepolygon_status_nonce_' . $highriskshopcryptogateway_usdcepolygon_email );

		
$highriskshopcryptogateway_usdcepolygon_response = wp_remote_get('https://api.highriskshop.com/crypto/polygon/usdc.e/convert.php?value=' . $highriskshopcryptogateway_usdcepolygon_total . '&from=' . strtolower($highriskshopcryptogateway_usdcepolygon_currency), array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_usdcepolygon_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Payment could not be processed due to failed currency conversion process, please try again', 'crypto-payment-gateway'), 'error');
    return null;
} else {

$highriskshopcryptogateway_usdcepolygon_body = wp_remote_retrieve_body($highriskshopcryptogateway_usdcepolygon_response);
$highriskshopcryptogateway_usdcepolygon_conversion_resp = json_decode($highriskshopcryptogateway_usdcepolygon_body, true);

if ($highriskshopcryptogateway_usdcepolygon_conversion_resp && isset($highriskshopcryptogateway_usdcepolygon_conversion_resp['value_coin'])) {
    // Escape output
    $highriskshopcryptogateway_usdcepolygon_final_total	= sanitize_text_field($highriskshopcryptogateway_usdcepolygon_conversion_resp['value_coin']);
    $highriskshopcryptogateway_usdcepolygon_reference_total = (float)$highriskshopcryptogateway_usdcepolygon_final_total;	
} else {
    wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Payment could not be processed, please try again (unsupported store currency)', 'crypto-payment-gateway'), 'error');
    return null;
}	
		}
		
		if ($this->usdcepolygon_blockchain_fees === 'yes') {
			
			// Get the estimated feed for our crypto coin in USD fiat currency
			
		$highriskshopcryptogateway_usdcepolygon_feesest_response = wp_remote_get('https://api.highriskshop.com/crypto/polygon/usdc.e/fees.php', array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_usdcepolygon_feesest_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Failed to get estimated fees, please try again', 'crypto-payment-gateway'), 'error');
    return null;
} else {

$highriskshopcryptogateway_usdcepolygon_feesest_body = wp_remote_retrieve_body($highriskshopcryptogateway_usdcepolygon_feesest_response);
$highriskshopcryptogateway_usdcepolygon_feesest_conversion_resp = json_decode($highriskshopcryptogateway_usdcepolygon_feesest_body, true);

if ($highriskshopcryptogateway_usdcepolygon_feesest_conversion_resp && isset($highriskshopcryptogateway_usdcepolygon_feesest_conversion_resp['estimated_cost_currency']['USD'])) {
    // Escape output
    $highriskshopcryptogateway_usdcepolygon_feesest_final_total = sanitize_text_field($highriskshopcryptogateway_usdcepolygon_feesest_conversion_resp['estimated_cost_currency']['USD']);
    $highriskshopcryptogateway_usdcepolygon_feesest_reference_total = (float)$highriskshopcryptogateway_usdcepolygon_feesest_final_total;	
} else {
    wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Failed to get estimated fees, please try again', 'crypto-payment-gateway'), 'error');
    return null;
}	
		}

// Convert the estimated fee back to our crypto

$highriskshopcryptogateway_usdcepolygon_revfeesest_response = wp_remote_get('https://api.highriskshop.com/crypto/polygon/usdc.e/convert.php?value=' . $highriskshopcryptogateway_usdcepolygon_feesest_reference_total . '&from=usd', array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_usdcepolygon_revfeesest_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Payment could not be processed due to failed currency conversion process, please try again', 'crypto-payment-gateway'), 'error');
    return null;
} else {

$highriskshopcryptogateway_usdcepolygon_revfeesest_body = wp_remote_retrieve_body($highriskshopcryptogateway_usdcepolygon_revfeesest_response);
$highriskshopcryptogateway_usdcepolygon_revfeesest_conversion_resp = json_decode($highriskshopcryptogateway_usdcepolygon_revfeesest_body, true);

if ($highriskshopcryptogateway_usdcepolygon_revfeesest_conversion_resp && isset($highriskshopcryptogateway_usdcepolygon_revfeesest_conversion_resp['value_coin'])) {
    // Escape output
    $highriskshopcryptogateway_usdcepolygon_revfeesest_final_total = sanitize_text_field($highriskshopcryptogateway_usdcepolygon_revfeesest_conversion_resp['value_coin']);
    $highriskshopcryptogateway_usdcepolygon_revfeesest_reference_total = (float)$highriskshopcryptogateway_usdcepolygon_revfeesest_final_total;
	// Calculating order total after adding the blockchain fees
	$highriskshopcryptogateway_usdcepolygon_payin_total = $highriskshopcryptogateway_usdcepolygon_reference_total + $highriskshopcryptogateway_usdcepolygon_revfeesest_reference_total;
} else {
    wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Payment could not be processed, please try again (unsupported store currency)', 'crypto-payment-gateway'), 'error');
    return null;
}	
		}
		
		} else {
			
		$highriskshopcryptogateway_usdcepolygon_payin_total = $highriskshopcryptogateway_usdcepolygon_reference_total;	

		}
		
$highriskshopcryptogateway_usdcepolygon_gen_wallet = wp_remote_get('https://api.highriskshop.com/crypto/polygon/usdc.e/wallet.php?address=' . $this->usdcepolygon_wallet_address .'&callback=' . urlencode($highriskshopcryptogateway_usdcepolygon_callback), array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_usdcepolygon_gen_wallet)) {
    // Handle error
    wc_add_notice(__('Wallet error:', 'crypto-payment-gateway') . __('Payment could not be processed due to incorrect payout wallet settings, please contact website admin', 'crypto-payment-gateway'), 'error');
    return null;
} else {
	$highriskshopcryptogateway_usdcepolygon_wallet_body = wp_remote_retrieve_body($highriskshopcryptogateway_usdcepolygon_gen_wallet);
	$highriskshopcryptogateway_usdcepolygon_wallet_decbody = json_decode($highriskshopcryptogateway_usdcepolygon_wallet_body, true);

 // Check if decoding was successful
    if ($highriskshopcryptogateway_usdcepolygon_wallet_decbody && isset($highriskshopcryptogateway_usdcepolygon_wallet_decbody['address_in'])) {
		// Store and sanitize variables
        $highriskshopcryptogateway_usdcepolygon_gen_addressIn = wp_kses_post($highriskshopcryptogateway_usdcepolygon_wallet_decbody['address_in']);
		$highriskshopcryptogateway_usdcepolygon_gen_callback = sanitize_url($highriskshopcryptogateway_usdcepolygon_wallet_decbody['callback_url']);
        
		// Generate QR code Image
		$highriskshopcryptogateway_usdcepolygon_genqrcode_response = wp_remote_get('https://api.highriskshop.com/crypto/polygon/usdc.e/qrcode.php?address=' . $highriskshopcryptogateway_usdcepolygon_gen_addressIn, array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_usdcepolygon_genqrcode_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Unable to generate QR code', 'crypto-payment-gateway'), 'error');
    return null;
} else {

$highriskshopcryptogateway_usdcepolygon_genqrcode_body = wp_remote_retrieve_body($highriskshopcryptogateway_usdcepolygon_genqrcode_response);
$highriskshopcryptogateway_usdcepolygon_genqrcode_conversion_resp = json_decode($highriskshopcryptogateway_usdcepolygon_genqrcode_body, true);

if ($highriskshopcryptogateway_usdcepolygon_genqrcode_conversion_resp && isset($highriskshopcryptogateway_usdcepolygon_genqrcode_conversion_resp['qr_code'])) {
    
    $highriskshopcryptogateway_usdcepolygon_genqrcode_pngimg = wp_kses_post($highriskshopcryptogateway_usdcepolygon_genqrcode_conversion_resp['qr_code']);	
	
} else {
    wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Unable to generate QR code', 'crypto-payment-gateway'), 'error');
    return null;
}	
		}
		
		
		// Save $usdcepolygonresponse in order meta data
    $order->add_meta_data('highriskshop_usdcepolygon_payin_address', $highriskshopcryptogateway_usdcepolygon_gen_addressIn, true);
    $order->add_meta_data('highriskshop_usdcepolygon_callback', $highriskshopcryptogateway_usdcepolygon_gen_callback, true);
	$order->add_meta_data('highriskshop_usdcepolygon_payin_amount', $highriskshopcryptogateway_usdcepolygon_payin_total, true);
	$order->add_meta_data('highriskshop_usdcepolygon_qrcode', $highriskshopcryptogateway_usdcepolygon_genqrcode_pngimg, true);
	$order->add_meta_data('highriskshop_usdcepolygon_nonce', $highriskshopcryptogateway_usdcepolygon_nonce, true);
	$order->add_meta_data('highriskshop_usdcepolygon_status_nonce', $highriskshopcryptogateway_usdcepolygon_status_nonce, true);
    $order->save();
    } else {
        wc_add_notice(__('Payment error:', 'crypto-payment-gateway') . __('Payment could not be processed, please try again (wallet address error)', 'crypto-payment-gateway'), 'error');

        return null;
    }
}

        // Redirect to payment page
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }

// Show payment instructions on thankyou page
public function before_thankyou_page($order_id) {
    $order = wc_get_order($order_id);
	// Check if this is the correct payment method
    if ($order->get_payment_method() !== $this->id) {
        return;
    }
    $highriskshopgateway_crypto_total = $order->get_meta('highriskshop_usdcepolygon_payin_amount', true);
    $highriskshopgateway__crypto_wallet_address = $order->get_meta('highriskshop_usdcepolygon_payin_address', true);
    $highriskshopgateway_crypto_qrcode = $order->get_meta('highriskshop_usdcepolygon_qrcode', true);
	$highriskshopgateway_crypto_qrcode_status_nonce = $order->get_meta('highriskshop_usdcepolygon_status_nonce', true);

    // CSS
	wp_enqueue_style('highriskshopcryptogateway-usdcepolygon-loader-css', plugin_dir_url( __DIR__ ) . 'static/payment-status.css', array(), '1.0.0');

    // Title
    echo '<div id="highriskshopcryptogateway-wrapper"><h1 style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
        . esc_html__('Please Complete Your Payment', 'crypto-payment-gateway') 
        . '</h1>';

    // QR Code Image
    echo '<div style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '"><img style="' . esc_attr('text-align:center;max-width:80%;margin:0 auto;') . '" src="data:image/png;base64,' 
        . esc_attr($highriskshopgateway_crypto_qrcode) . '" alt="' . esc_attr('polygon/usdc.e Payment Address') . '"/></div>';

    // Payment Instructions
	/* translators: 1: Amount of cryptocurrency to be sent, 2: Name of the cryptocurrency */
    echo '<p style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">' . sprintf( esc_html__('Please send %1$s %2$s to the following address:', 'crypto-payment-gateway'), '<br><strong>' . esc_html($highriskshopgateway_crypto_total) . '</strong>', esc_html__('polygon/usdc.e', 'crypto-payment-gateway') ) . '</p>';


    // Wallet Address
    echo '<p style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
        . '<strong>' . esc_html($highriskshopgateway__crypto_wallet_address) . '</strong>'
        . '</p><br><hr></div>';
		
	echo '<div class="' . esc_attr('highriskshopcryptogateway-unpaid') . '" id="' . esc_attr('highriskshop-payment-status-message') . '" style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
                . esc_html__('Waiting for payment', 'crypto-payment-gateway')
                . '</div><br><hr><br>';	

  // Enqueue jQuery and the external script
    wp_enqueue_script('jquery');
    wp_enqueue_script('highriskshopcryptogateway-check-status', plugin_dir_url(__DIR__) . 'assets/js/highriskshopcryptogateway-payment-status-check.js?order_id=' . esc_attr($order_id) . '&nonce=' . esc_attr($highriskshopgateway_crypto_qrcode_status_nonce) . '&tickerstring=usdcepolygon', array('jquery'), '1.0.0', true);

}



}

function highriskshop_add_instant_payment_gateway_usdcepolygon($gateways) {
    $gateways[] = 'HighRiskShop_Crypto_Payment_Gateway_Usdcepolygon';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'highriskshop_add_instant_payment_gateway_usdcepolygon');
}

// Add custom endpoint for reading crypto payment status

   function highriskshopcryptogateway_usdcepolygon_check_order_status_rest_endpoint() {
        register_rest_route('highriskshopcryptogateway/v1', '/highriskshopcryptogateway-check-order-status-usdcepolygon/', array(
            'methods'  => 'GET',
            'callback' => 'highriskshopcryptogateway_usdcepolygon_check_order_status_callback',
            'permission_callback' => '__return_true',
        ));
    }

    add_action('rest_api_init', 'highriskshopcryptogateway_usdcepolygon_check_order_status_rest_endpoint');

    function highriskshopcryptogateway_usdcepolygon_check_order_status_callback($request) {
        $order_id = absint($request->get_param('order_id'));
		$highriskshopcryptogateway_usdcepolygon_live_status_nonce = sanitize_text_field($request->get_param('nonce'));

        if (empty($order_id)) {
            return new WP_Error('missing_order_id', __('Order ID parameter is missing.', 'crypto-payment-gateway'), array('status' => 400));
        }

        $order = wc_get_order($order_id);

        if (!$order) {
            return new WP_Error('invalid_order', __('Invalid order ID.', 'crypto-payment-gateway'), array('status' => 404));
        }
		
		// Verify stored status nonce

        if ( empty( $highriskshopcryptogateway_usdcepolygon_live_status_nonce ) || $order->get_meta('highriskshop_usdcepolygon_status_nonce', true) !== $highriskshopcryptogateway_usdcepolygon_live_status_nonce ) {
        return new WP_Error( 'invalid_nonce', __( 'Invalid nonce.', 'crypto-payment-gateway' ), array( 'status' => 403 ) );
    }
        return array('status' => $order->get_status());
    }

// Add custom endpoint for changing order status
function highriskshopcryptogateway_usdcepolygon_change_order_status_rest_endpoint() {
    // Register custom route
    register_rest_route( 'highriskshopcryptogateway/v1', '/highriskshopcryptogateway-usdcepolygon/', array(
        'methods'  => 'GET',
        'callback' => 'highriskshopcryptogateway_usdcepolygon_change_order_status_callback',
        'permission_callback' => '__return_true',
    ));
}
add_action( 'rest_api_init', 'highriskshopcryptogateway_usdcepolygon_change_order_status_rest_endpoint' );

// Callback function to change order status
function highriskshopcryptogateway_usdcepolygon_change_order_status_callback( $request ) {
    $order_id = absint($request->get_param( 'order_id' ));
	$highriskshopcryptogateway_usdcepolygongetnonce = sanitize_text_field($request->get_param( 'nonce' ));
	$highriskshopcryptogateway_usdcepolygonpaid_value_coin = sanitize_text_field($request->get_param('value_coin'));
	$highriskshopcryptogateway_usdcepolygon_paid_coin_name = sanitize_text_field($request->get_param('coin'));
	$highriskshopcryptogateway_usdcepolygon_paid_txid_in = sanitize_text_field($request->get_param('txid_in'));

    // Check if order ID parameter exists
    if ( empty( $order_id ) ) {
        return new WP_Error( 'missing_order_id', __( 'Order ID parameter is missing.', 'crypto-payment-gateway' ), array( 'status' => 400 ) );
    }

    // Get order object
    $order = wc_get_order( $order_id );

    // Check if order exists
    if ( ! $order ) {
        return new WP_Error( 'invalid_order', __( 'Invalid order ID.', 'crypto-payment-gateway' ), array( 'status' => 404 ) );
    }
	
	// Verify nonce
    if ( empty( $highriskshopcryptogateway_usdcepolygongetnonce ) || $order->get_meta('highriskshop_usdcepolygon_nonce', true) !== $highriskshopcryptogateway_usdcepolygongetnonce ) {
        return new WP_Error( 'invalid_nonce', __( 'Invalid nonce.', 'crypto-payment-gateway' ), array( 'status' => 403 ) );
    }

    // Check if the order is pending and payment method is 'highriskshop-crypto-payment-gateway-usdcepolygon'
    if ( $order && !in_array($order->get_status(), ['processing', 'completed'], true) && 'highriskshop-crypto-payment-gateway-usdcepolygon' === $order->get_payment_method() ) {
		
		// Get the expected amount and coin
	$highriskshopcryptogateway_usdcepolygonexpected_amount = $order->get_meta('highriskshop_usdcepolygon_payin_amount', true);
	$highriskshopcryptogateway_usdcepolygonexpected_coin = $order->get_meta('highriskshop_usdcepolygon_payin_amount', true);
	
		if ( $highriskshopcryptogateway_usdcepolygonpaid_value_coin < $highriskshopcryptogateway_usdcepolygonexpected_amount || $highriskshopcryptogateway_usdcepolygon_paid_coin_name !== 'polygon_usdc.e') {
			// Mark the order as failed and add an order note
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Expected amount, 4: Transaction ID */			
$order->update_status('failed', sprintf(__( '[Order Failed] Customer sent %1$s %2$s instead of %3$s polygon_usdc.e. TXID: %4$s', 'crypto-payment-gateway' ), $highriskshopcryptogateway_usdcepolygonpaid_value_coin, $highriskshopcryptogateway_usdcepolygon_paid_coin_name, $highriskshopcryptogateway_usdcepolygonexpected_amount, $highriskshopcryptogateway_usdcepolygon_paid_txid_in));
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Expected amount, 4: Transaction ID */
$order->add_order_note(sprintf( __( '[Order Failed] Customer sent %1$s %2$s instead of %3$s polygon_usdc.e. TXID: %4$s', 'crypto-payment-gateway' ), $highriskshopcryptogateway_usdcepolygonpaid_value_coin, $highriskshopcryptogateway_usdcepolygon_paid_coin_name, $highriskshopcryptogateway_usdcepolygonexpected_amount, $highriskshopcryptogateway_usdcepolygon_paid_txid_in));
            return array( 'message' => 'Order status changed to failed due to partial payment or incorrect coin. Please check order notes' );
			
		} else {
        // Change order status to processing
		$order->payment_complete();
		/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Transaction ID */
		$order->update_status('processing', sprintf( __( '[Payment completed] Customer sent %1$s %2$s TXID:%3$s', 'crypto-payment-gateway' ), $highriskshopcryptogateway_usdcepolygonpaid_value_coin, $highriskshopcryptogateway_usdcepolygon_paid_coin_name, $highriskshopcryptogateway_usdcepolygon_paid_txid_in));

// Return success response
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Transaction ID */
$order->add_order_note(sprintf( __( '[Payment completed] Customer sent %1$s %2$s TXID:%3$s', 'crypto-payment-gateway' ), $highriskshopcryptogateway_usdcepolygonpaid_value_coin, $highriskshopcryptogateway_usdcepolygon_paid_coin_name, $highriskshopcryptogateway_usdcepolygon_paid_txid_in));
        return array( 'message' => 'Order status changed to processing.' );
		}
    } else {
        // Return error response if conditions are not met
        return new WP_Error( 'order_not_eligible', __( 'Order is not eligible for status change.', 'crypto-payment-gateway' ), array( 'status' => 400 ) );
    }
}
?>