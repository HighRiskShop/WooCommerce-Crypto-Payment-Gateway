<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('plugins_loaded', 'init_highriskshopcryptogateway_btctrc20_gateway');

function init_highriskshopcryptogateway_btctrc20_gateway() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

class HighRiskShop_Instant_Payment_Gateway_Btctrc20 extends WC_Payment_Gateway {

    public function __construct() {
        $this->id                 = 'highriskshop-instant-payment-gateway-btctrc20';
        $this->icon = esc_url(plugin_dir_url(__DIR__) . 'static/btctrc20.png');
        $this->method_title       = esc_html__('BTC trc20 Crypto Payment Gateway With Instant Payouts', 'highriskshopcryptogateway'); // Escaping title
        $this->method_description = esc_html__('BTC trc20 Crypto Payment Gateway With Instant Payouts to your trc20_btc wallet. Allows you to accept crypto trc20/btc payments without sign up and without KYC.', 'highriskshopcryptogateway'); // Escaping description
        $this->has_fields         = false;

        $this->init_form_fields();
        $this->init_settings();

        $this->title       = sanitize_text_field($this->get_option('title'));
        $this->description = sanitize_text_field($this->get_option('description'));

        // Use the configured settings for redirect and icon URLs
        $this->btctrc20_wallet_address = sanitize_text_field($this->get_option('btctrc20_wallet_address'));
		$this->btctrc20_blockchain_fees = $this->get_option('btctrc20_blockchain_fees');
        $this->icon_url     = sanitize_url($this->get_option('icon_url'));

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_before_thankyou', array($this, 'before_thankyou_page'));
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => esc_html__('Enable/Disable', 'highriskshopcryptogateway'), // Escaping title
                'type'    => 'checkbox',
                'label'   => esc_html__('Enable trc20_btc payment gateway', 'highriskshopcryptogateway'), // Escaping label
                'default' => 'no',
            ),
            'title' => array(
                'title'       => esc_html__('Title', 'highriskshopcryptogateway'), // Escaping title
                'type'        => 'text',
                'description' => esc_html__('Payment method title that users will see during checkout.', 'highriskshopcryptogateway'), // Escaping description
                'default'     => esc_html__('BTC trc20', 'highriskshopcryptogateway'), // Escaping default value
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => esc_html__('Description', 'highriskshopcryptogateway'), // Escaping title
                'type'        => 'textarea',
                'description' => esc_html__('Payment method description that users will see during checkout.', 'highriskshopcryptogateway'), // Escaping description
                'default'     => esc_html__('Pay via crypto BTC trc20 trc20_btc', 'highriskshopcryptogateway'), // Escaping default value
                'desc_tip'    => true,
            ),
            'btctrc20_wallet_address' => array(
                'title'       => esc_html__('Wallet Address', 'highriskshopcryptogateway'), // Escaping title
                'type'        => 'text',
                'description' => esc_html__('Insert your trc20/btc wallet address to receive instant payouts.', 'highriskshopcryptogateway'), // Escaping description
                'desc_tip'    => true,
            ),
			'btctrc20_blockchain_fees' => array(
                'title'       => esc_html__('Customer Pays Blockchain Fees', 'highriskshopcryptogateway'), // Escaping title
                'type'        => 'checkbox',
                'description' => esc_html__('Add estimated blockchian fees to the order total.', 'highriskshopcryptogateway'), // Escaping description
                'desc_tip'    => true,
				'default' => 'no',
            ),
        );
    }
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        $highriskshopcryptogateway_btctrc20_currency = get_woocommerce_currency();
		$highriskshopcryptogateway_btctrc20_total = $order->get_total();
		$highriskshopcryptogateway_btctrc20_nonce = wp_create_nonce( 'highriskshopcryptogateway_btctrc20_nonce_' . $order_id );
		$highriskshopcryptogateway_btctrc20_callback = add_query_arg(array('order_id' => $order_id, 'nonce' => $highriskshopcryptogateway_btctrc20_nonce,), rest_url('highriskshopcryptogateway/v1/highriskshopcryptogateway-btctrc20/'));
		$highriskshopcryptogateway_btctrc20_email = urlencode(sanitize_email($order->get_billing_email()));
		$highriskshopcryptogateway_btctrc20_status_nonce = wp_create_nonce( 'highriskshopcryptogateway_btctrc20_status_nonce_' . $highriskshopcryptogateway_btctrc20_email );

		
$highriskshopcryptogateway_btctrc20_response = wp_remote_get('https://api.highriskshop.com/crypto/trc20/btc/convert.php?value=' . $highriskshopcryptogateway_btctrc20_total . '&from=' . strtolower($highriskshopcryptogateway_btctrc20_currency));

if (is_wp_error($highriskshopcryptogateway_btctrc20_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed due to failed currency conversion process, please try again', 'hrsbtctrc20'), 'error');
    return null;
} else {

$highriskshopcryptogateway_btctrc20_body = wp_remote_retrieve_body($highriskshopcryptogateway_btctrc20_response);
$highriskshopcryptogateway_btctrc20_conversion_resp = json_decode($highriskshopcryptogateway_btctrc20_body, true);

if ($highriskshopcryptogateway_btctrc20_conversion_resp && isset($highriskshopcryptogateway_btctrc20_conversion_resp['value_coin'])) {
    // Escape output
    $highriskshopcryptogateway_btctrc20_final_total	= sanitize_text_field($highriskshopcryptogateway_btctrc20_conversion_resp['value_coin']);
    $highriskshopcryptogateway_btctrc20_reference_total = (float)$highriskshopcryptogateway_btctrc20_final_total;	
} else {
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed, please try again (unsupported store currency)', 'hrsbtctrc20'), 'error');
    return null;
}	
		}
		
		if ($this->btctrc20_blockchain_fees === 'yes') {
			
			// Get the estimated feed for our crypto coin in USD fiat currency
			
		$highriskshopcryptogateway_btctrc20_feesest_response = wp_remote_get('https://api.highriskshop.com/crypto/trc20/btc/fees.php');

if (is_wp_error($highriskshopcryptogateway_btctrc20_feesest_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Failed to get estimated fees, please try again', 'hrsbtctrc20'), 'error');
    return null;
} else {

$highriskshopcryptogateway_btctrc20_feesest_body = wp_remote_retrieve_body($highriskshopcryptogateway_btctrc20_feesest_response);
$highriskshopcryptogateway_btctrc20_feesest_conversion_resp = json_decode($highriskshopcryptogateway_btctrc20_feesest_body, true);

if ($highriskshopcryptogateway_btctrc20_feesest_conversion_resp && isset($highriskshopcryptogateway_btctrc20_feesest_conversion_resp['estimated_cost_currency']['USD'])) {
    // Escape output
    $highriskshopcryptogateway_btctrc20_feesest_final_total = sanitize_text_field($highriskshopcryptogateway_btctrc20_feesest_conversion_resp['estimated_cost_currency']['USD']);
    $highriskshopcryptogateway_btctrc20_feesest_reference_total = (float)$highriskshopcryptogateway_btctrc20_feesest_final_total;	
} else {
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Failed to get estimated fees, please try again', 'hrsbtctrc20'), 'error');
    return null;
}	
		}

// Convert the estimated fee back to our crypto

$highriskshopcryptogateway_btctrc20_revfeesest_response = wp_remote_get('https://api.highriskshop.com/crypto/trc20/btc/convert.php?value=' . $highriskshopcryptogateway_btctrc20_feesest_reference_total . '&from=usd');

if (is_wp_error($highriskshopcryptogateway_btctrc20_revfeesest_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed due to failed currency conversion process, please try again', 'hrsbtctrc20'), 'error');
    return null;
} else {

$highriskshopcryptogateway_btctrc20_revfeesest_body = wp_remote_retrieve_body($highriskshopcryptogateway_btctrc20_revfeesest_response);
$highriskshopcryptogateway_btctrc20_revfeesest_conversion_resp = json_decode($highriskshopcryptogateway_btctrc20_revfeesest_body, true);

if ($highriskshopcryptogateway_btctrc20_revfeesest_conversion_resp && isset($highriskshopcryptogateway_btctrc20_revfeesest_conversion_resp['value_coin'])) {
    // Escape output
    $highriskshopcryptogateway_btctrc20_revfeesest_final_total = sanitize_text_field($highriskshopcryptogateway_btctrc20_revfeesest_conversion_resp['value_coin']);
    $highriskshopcryptogateway_btctrc20_revfeesest_reference_total = (float)$highriskshopcryptogateway_btctrc20_revfeesest_final_total;
	// Calculating order total after adding the blockchain fees
	$highriskshopcryptogateway_btctrc20_payin_total = $highriskshopcryptogateway_btctrc20_reference_total + $highriskshopcryptogateway_btctrc20_revfeesest_reference_total;
} else {
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed, please try again (unsupported store currency)', 'hrsbtctrc20'), 'error');
    return null;
}	
		}
		
		} else {
			
		$highriskshopcryptogateway_btctrc20_payin_total = $highriskshopcryptogateway_btctrc20_reference_total;	

		}
		
$highriskshopcryptogateway_btctrc20_gen_wallet = wp_remote_get('https://api.highriskshop.com/crypto/trc20/btc/wallet.php?address=' . $this->btctrc20_wallet_address .'&callback=' . urlencode($highriskshopcryptogateway_btctrc20_callback));

if (is_wp_error($highriskshopcryptogateway_btctrc20_gen_wallet)) {
    // Handle error
    wc_add_notice(__('Wallet error:', 'woocommerce') . __('Payment could not be processed due to incorrect payout wallet settings, please contact website admin', 'hrsbtctrc20'), 'error');
    return null;
} else {
	$highriskshopcryptogateway_btctrc20_wallet_body = wp_remote_retrieve_body($highriskshopcryptogateway_btctrc20_gen_wallet);
	$highriskshopcryptogateway_btctrc20_wallet_decbody = json_decode($highriskshopcryptogateway_btctrc20_wallet_body, true);

 // Check if decoding was successful
    if ($highriskshopcryptogateway_btctrc20_wallet_decbody && isset($highriskshopcryptogateway_btctrc20_wallet_decbody['address_in'])) {
		// Store and sanitize variables
        $highriskshopcryptogateway_btctrc20_gen_addressIn = wp_kses_post($highriskshopcryptogateway_btctrc20_wallet_decbody['address_in']);
		$highriskshopcryptogateway_btctrc20_gen_callback = sanitize_url($highriskshopcryptogateway_btctrc20_wallet_decbody['callback_url']);
        
		// Generate QR code Image
		$highriskshopcryptogateway_btctrc20_genqrcode_response = wp_remote_get('https://api.highriskshop.com/crypto/trc20/btc/qrcode.php?address=' . $highriskshopcryptogateway_btctrc20_gen_addressIn);

if (is_wp_error($highriskshopcryptogateway_btctrc20_genqrcode_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Unable to generate QR code', 'hrsbtctrc20'), 'error');
    return null;
} else {

$highriskshopcryptogateway_btctrc20_genqrcode_body = wp_remote_retrieve_body($highriskshopcryptogateway_btctrc20_genqrcode_response);
$highriskshopcryptogateway_btctrc20_genqrcode_conversion_resp = json_decode($highriskshopcryptogateway_btctrc20_genqrcode_body, true);

if ($highriskshopcryptogateway_btctrc20_genqrcode_conversion_resp && isset($highriskshopcryptogateway_btctrc20_genqrcode_conversion_resp['qr_code'])) {
    
    $highriskshopcryptogateway_btctrc20_genqrcode_pngimg = wp_kses_post($highriskshopcryptogateway_btctrc20_genqrcode_conversion_resp['qr_code']);	
	
} else {
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Unable to generate QR code', 'hrsbtctrc20'), 'error');
    return null;
}	
		}
		
		
		// Save $btctrc20response in order meta data
    $order->add_meta_data('highriskshop_btctrc20_payin_address', $highriskshopcryptogateway_btctrc20_gen_addressIn, true);
    $order->add_meta_data('highriskshop_btctrc20_callback', $highriskshopcryptogateway_btctrc20_gen_callback, true);
	$order->add_meta_data('highriskshop_btctrc20_payin_amount', $highriskshopcryptogateway_btctrc20_payin_total, true);
	$order->add_meta_data('highriskshop_btctrc20_qrcode', $highriskshopcryptogateway_btctrc20_genqrcode_pngimg, true);
	$order->add_meta_data('highriskshop_btctrc20_nonce', $highriskshopcryptogateway_btctrc20_nonce, true);
	$order->add_meta_data('highriskshop_btctrc20_status_nonce', $highriskshopcryptogateway_btctrc20_status_nonce, true);
    $order->save();
    } else {
        wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed, please try again (wallet address error)', 'btctrc20'), 'error');

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
    $highriskshopgateway_crypto_total = $order->get_meta('highriskshop_btctrc20_payin_amount', true);
    $highriskshopgateway__crypto_wallet_address = $order->get_meta('highriskshop_btctrc20_payin_address', true);
    $highriskshopgateway_crypto_qrcode = $order->get_meta('highriskshop_btctrc20_qrcode', true);
	$highriskshopgateway_crypto_qrcode_status_nonce = $order->get_meta('highriskshop_btctrc20_status_nonce', true);

    // CSS
	wp_enqueue_style('highriskshopcryptogateway-btctrc20-loader-css', plugin_dir_url( __DIR__ ) . 'static/payment-status.css', array(), '1.0.0');

    // Title
    echo '<div id="highriskshopcryptogateway-wrapper"><h1 style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
        . esc_html__('Please Complete Your Payment', 'highriskshop-instant-payment-gateway-btctrc20') 
        . '</h1>';

    // QR Code Image
    echo '<div style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '"><img style="' . esc_attr('text-align:center;max-width:80%;margin:0 auto;') . '" src="data:image/png;base64,' 
        . esc_attr($highriskshopgateway_crypto_qrcode) . '" alt="' . esc_attr('trc20/btc Payment Address') . '"/></div>';

    // Payment Instructions
	/* translators: 1: Amount of cryptocurrency to be sent, 2: Name of the cryptocurrency */
    echo '<p style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">' . sprintf( esc_html__('Please send %1$s %2$s to the following address:', 'highriskshop-instant-payment-gateway-btctrc20'), '<br><strong>' . esc_html($highriskshopgateway_crypto_total) . '</strong>', esc_html__('trc20/btc', 'highriskshop-instant-payment-gateway-btctrc20') ) . '</p>';


    // Wallet Address
    echo '<p style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
        . '<strong>' . esc_html($highriskshopgateway__crypto_wallet_address) . '</strong>'
        . '</p><br><hr></div>';
		
	echo '<div class="' . esc_attr('highriskshopcryptogateway-unpaid') . '" id="' . esc_attr('highriskshop-payment-status-message') . '" style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
                . esc_html__('Waiting for payment', 'highriskshop-instant-payment-gateway-btctrc20')
                . '</div><br><hr><br>';	

 ?>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                function highriskshopcryptogateway_payment_status() {
                    $.ajax({
                        url: '<?php echo esc_url(rest_url('highriskshopcryptogateway/v1/highriskshopcryptogateway-check-order-status-btctrc20/')); ?>',
                        method: 'GET',
                        data: {
                            order_id: '<?php echo esc_js($order_id); ?>',
							nonce: '<?php echo esc_js($highriskshopgateway_crypto_qrcode_status_nonce); ?>'
                        },
                        success: function(response) {
                            if (response.status === 'processing' || response.status === 'completed') {
                                $('#highriskshop-payment-status-message').text('<?php echo esc_js(__('Payment received', 'highriskshop-instant-payment-gateway-btctrc20')); ?>')
								.removeClass('highriskshopcryptogateway-unpaid')
								.addClass('<?php echo esc_js(esc_attr('highriskshopcryptogateway-paid')); ?>');
								$('#highriskshopcryptogateway-wrapper').remove();
                            } else {
                                $('#highriskshop-payment-status-message').text('<?php echo esc_js(__('Waiting for payment', 'highriskshop-instant-payment-gateway-btctrc20')); ?>');
                            }
                        },
                        error: function() {
                            $('#highriskshop-payment-status-message').text('<?php echo esc_js(__('Error checking payment status. Please refresh the page.', 'highriskshop-instant-payment-gateway-btctrc20')); ?>');
                        }
                    });
                }

                setInterval(highriskshopcryptogateway_payment_status, 60000);
            });
            </script>
            <?php

}



}

function highriskshop_add_instant_payment_gateway_btctrc20($gateways) {
    $gateways[] = 'HighRiskShop_Instant_Payment_Gateway_Btctrc20';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'highriskshop_add_instant_payment_gateway_btctrc20');
}

// Add custom endpoint for reading crypto payment status

   function highriskshopcryptogateway_btctrc20_check_order_status_rest_endpoint() {
        register_rest_route('highriskshopcryptogateway/v1', '/highriskshopcryptogateway-check-order-status-btctrc20/', array(
            'methods'  => 'GET',
            'callback' => 'highriskshopcryptogateway_btctrc20_check_order_status_callback',
            'permission_callback' => '__return_true',
        ));
    }

    add_action('rest_api_init', 'highriskshopcryptogateway_btctrc20_check_order_status_rest_endpoint');

    function highriskshopcryptogateway_btctrc20_check_order_status_callback($request) {
        $order_id = absint($request->get_param('order_id'));
		$highriskshopcryptogateway_btctrc20_live_status_nonce = sanitize_text_field($request->get_param('nonce'));

        if (empty($order_id)) {
            return new WP_Error('missing_order_id', __('Order ID parameter is missing.', 'highriskshop-instant-payment-gateway-btctrc20'), array('status' => 400));
        }

        $order = wc_get_order($order_id);

        if (!$order) {
            return new WP_Error('invalid_order', __('Invalid order ID.', 'highriskshop-instant-payment-gateway-btctrc20'), array('status' => 404));
        }
		
		// Verify stored status nonce

        if ( empty( $highriskshopcryptogateway_btctrc20_live_status_nonce ) || $order->get_meta('highriskshop_btctrc20_status_nonce', true) !== $highriskshopcryptogateway_btctrc20_live_status_nonce ) {
        return new WP_Error( 'invalid_nonce', __( 'Invalid nonce.', 'highriskshop-instant-payment-gateway-btctrc20' ), array( 'status' => 403 ) );
    }
        return array('status' => $order->get_status());
    }

// Add custom endpoint for changing order status
function highriskshopcryptogateway_btctrc20_change_order_status_rest_endpoint() {
    // Register custom route
    register_rest_route( 'highriskshopcryptogateway/v1', '/highriskshopcryptogateway-btctrc20/', array(
        'methods'  => 'GET',
        'callback' => 'highriskshopcryptogateway_btctrc20_change_order_status_callback',
        'permission_callback' => '__return_true',
    ));
}
add_action( 'rest_api_init', 'highriskshopcryptogateway_btctrc20_change_order_status_rest_endpoint' );

// Callback function to change order status
function highriskshopcryptogateway_btctrc20_change_order_status_callback( $request ) {
    $order_id = absint($request->get_param( 'order_id' ));
	$highriskshopcryptogateway_btctrc20getnonce = sanitize_text_field($request->get_param( 'nonce' ));
	$highriskshopcryptogateway_btctrc20paid_value_coin = sanitize_text_field($request->get_param('value_coin'));
	$highriskshopcryptogateway_btctrc20_paid_coin_name = sanitize_text_field($request->get_param('coin'));
	$highriskshopcryptogateway_btctrc20_paid_txid_in = sanitize_text_field($request->get_param('txid_in'));

    // Check if order ID parameter exists
    if ( empty( $order_id ) ) {
        return new WP_Error( 'missing_order_id', __( 'Order ID parameter is missing.', 'highriskshop-instant-payment-gateway-btctrc20' ), array( 'status' => 400 ) );
    }

    // Get order object
    $order = wc_get_order( $order_id );

    // Check if order exists
    if ( ! $order ) {
        return new WP_Error( 'invalid_order', __( 'Invalid order ID.', 'highriskshop-instant-payment-gateway-btctrc20' ), array( 'status' => 404 ) );
    }
	
	// Verify nonce
    if ( empty( $highriskshopcryptogateway_btctrc20getnonce ) || $order->get_meta('highriskshop_btctrc20_nonce', true) !== $highriskshopcryptogateway_btctrc20getnonce ) {
        return new WP_Error( 'invalid_nonce', __( 'Invalid nonce.', 'highriskshop-instant-payment-gateway-btctrc20' ), array( 'status' => 403 ) );
    }

    // Check if the order is pending and payment method is 'highriskshop-instant-payment-gateway-btctrc20'
    if ( $order && !in_array($order->get_status(), ['processing', 'completed'], true) && 'highriskshop-instant-payment-gateway-btctrc20' === $order->get_payment_method() ) {
		
		// Get the expected amount and coin
	$highriskshopcryptogateway_btctrc20expected_amount = $order->get_meta('highriskshop_btctrc20_payin_amount', true);
	$highriskshopcryptogateway_btctrc20expected_coin = $order->get_meta('highriskshop_btctrc20_payin_amount', true);
	
		if ( $highriskshopcryptogateway_btctrc20paid_value_coin < $highriskshopcryptogateway_btctrc20expected_amount || $highriskshopcryptogateway_btctrc20_paid_coin_name !== 'trc20_btc') {
			// Mark the order as failed and add an order note
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Expected amount, 4: Transaction ID */			
$order->update_status('failed', sprintf(__( '[Order Failed] Customer sent %1$s %2$s instead of %3$s trc20_btc. TXID: %4$s', 'highriskshop-instant-payment-gateway-btctrc20' ), $highriskshopcryptogateway_btctrc20paid_value_coin, $highriskshopcryptogateway_btctrc20_paid_coin_name, $highriskshopcryptogateway_btctrc20expected_amount, $highriskshopcryptogateway_btctrc20_paid_txid_in));
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Expected amount, 4: Transaction ID */
$order->add_order_note(sprintf( __( '[Order Failed] Customer sent %1$s %2$s instead of %3$s trc20_btc. TXID: %4$s', 'highriskshop-instant-payment-gateway-btctrc20' ), $highriskshopcryptogateway_btctrc20paid_value_coin, $highriskshopcryptogateway_btctrc20_paid_coin_name, $highriskshopcryptogateway_btctrc20expected_amount, $highriskshopcryptogateway_btctrc20_paid_txid_in));
            return array( 'message' => 'Order status changed to failed due to partial payment or incorrect coin. Please check order notes' );
			
		} else {
        // Change order status to processing
		$order->payment_complete();
		/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Transaction ID */
		$order->update_status('processing', sprintf( __( '[Payment completed] Customer sent %1$s %2$s TXID:%3$s', 'highriskshop-instant-payment-gateway-btctrc20' ), $highriskshopcryptogateway_btctrc20paid_value_coin, $highriskshopcryptogateway_btctrc20_paid_coin_name, $highriskshopcryptogateway_btctrc20_paid_txid_in));

// Return success response
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Transaction ID */
$order->add_order_note(sprintf( __( '[Payment completed] Customer sent %1$s %2$s TXID:%3$s', 'highriskshop-instant-payment-gateway-btctrc20' ), $highriskshopcryptogateway_btctrc20paid_value_coin, $highriskshopcryptogateway_btctrc20_paid_coin_name, $highriskshopcryptogateway_btctrc20_paid_txid_in));
        return array( 'message' => 'Order status changed to processing.' );
		}
    } else {
        // Return error response if conditions are not met
        return new WP_Error( 'order_not_eligible', __( 'Order is not eligible for status change.', 'highriskshop-instant-payment-gateway-btctrc20' ), array( 'status' => 400 ) );
    }
}
?>