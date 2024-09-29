<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('plugins_loaded', 'init_highriskshopcryptogateway_ethoptimism_gateway');

function init_highriskshopcryptogateway_ethoptimism_gateway() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

class HighRiskShop_Instant_Payment_Gateway_Ethoptimism extends WC_Payment_Gateway {

    public function __construct() {
        $this->id                 = 'highriskshop-instant-payment-gateway-ethoptimism';
        $this->icon = esc_url(plugin_dir_url(__DIR__) . 'static/ethoptimism.png');
        $this->method_title       = esc_html__('Ethereum optimism Crypto Payment Gateway With Instant Payouts', 'highriskshopcryptogateway'); // Escaping title
        $this->method_description = esc_html__('Ethereum optimism Crypto Payment Gateway With Instant Payouts to your optimism_eth wallet. Allows you to accept crypto optimism/eth payments without sign up and without KYC.', 'highriskshopcryptogateway'); // Escaping description
        $this->has_fields         = false;

        $this->init_form_fields();
        $this->init_settings();

        $this->title       = sanitize_text_field($this->get_option('title'));
        $this->description = sanitize_text_field($this->get_option('description'));

        // Use the configured settings for redirect and icon URLs
        $this->ethoptimism_wallet_address = sanitize_text_field($this->get_option('ethoptimism_wallet_address'));
		$this->ethoptimism_blockchain_fees = $this->get_option('ethoptimism_blockchain_fees');
        $this->icon_url     = sanitize_url($this->get_option('icon_url'));

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_before_thankyou', array($this, 'before_thankyou_page'));
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => esc_html__('Enable/Disable', 'highriskshopcryptogateway'), // Escaping title
                'type'    => 'checkbox',
                'label'   => esc_html__('Enable optimism_eth payment gateway', 'highriskshopcryptogateway'), // Escaping label
                'default' => 'no',
            ),
            'title' => array(
                'title'       => esc_html__('Title', 'highriskshopcryptogateway'), // Escaping title
                'type'        => 'text',
                'description' => esc_html__('Payment method title that users will see during checkout.', 'highriskshopcryptogateway'), // Escaping description
                'default'     => esc_html__('Ethereum optimism', 'highriskshopcryptogateway'), // Escaping default value
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => esc_html__('Description', 'highriskshopcryptogateway'), // Escaping title
                'type'        => 'textarea',
                'description' => esc_html__('Payment method description that users will see during checkout.', 'highriskshopcryptogateway'), // Escaping description
                'default'     => esc_html__('Pay via crypto Ethereum optimism optimism_eth', 'highriskshopcryptogateway'), // Escaping default value
                'desc_tip'    => true,
            ),
            'ethoptimism_wallet_address' => array(
                'title'       => esc_html__('Wallet Address', 'highriskshopcryptogateway'), // Escaping title
                'type'        => 'text',
                'description' => esc_html__('Insert your optimism/eth wallet address to receive instant payouts.', 'highriskshopcryptogateway'), // Escaping description
                'desc_tip'    => true,
            ),
			'ethoptimism_blockchain_fees' => array(
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
        $highriskshopcryptogateway_ethoptimism_currency = get_woocommerce_currency();
		$highriskshopcryptogateway_ethoptimism_total = $order->get_total();
		$highriskshopcryptogateway_ethoptimism_nonce = wp_create_nonce( 'highriskshopcryptogateway_ethoptimism_nonce_' . $order_id );
		$highriskshopcryptogateway_ethoptimism_callback = add_query_arg(array('order_id' => $order_id, 'nonce' => $highriskshopcryptogateway_ethoptimism_nonce,), rest_url('highriskshopcryptogateway/v1/highriskshopcryptogateway-ethoptimism/'));
		$highriskshopcryptogateway_ethoptimism_email = urlencode(sanitize_email($order->get_billing_email()));
		$highriskshopcryptogateway_ethoptimism_status_nonce = wp_create_nonce( 'highriskshopcryptogateway_ethoptimism_status_nonce_' . $highriskshopcryptogateway_ethoptimism_email );

		
$highriskshopcryptogateway_ethoptimism_response = wp_remote_get('https://api.highriskshop.com/crypto/optimism/eth/convert.php?value=' . $highriskshopcryptogateway_ethoptimism_total . '&from=' . strtolower($highriskshopcryptogateway_ethoptimism_currency), array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_ethoptimism_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed due to failed currency conversion process, please try again', 'hrsethoptimism'), 'error');
    return null;
} else {

$highriskshopcryptogateway_ethoptimism_body = wp_remote_retrieve_body($highriskshopcryptogateway_ethoptimism_response);
$highriskshopcryptogateway_ethoptimism_conversion_resp = json_decode($highriskshopcryptogateway_ethoptimism_body, true);

if ($highriskshopcryptogateway_ethoptimism_conversion_resp && isset($highriskshopcryptogateway_ethoptimism_conversion_resp['value_coin'])) {
    // Escape output
    $highriskshopcryptogateway_ethoptimism_final_total	= sanitize_text_field($highriskshopcryptogateway_ethoptimism_conversion_resp['value_coin']);
    $highriskshopcryptogateway_ethoptimism_reference_total = (float)$highriskshopcryptogateway_ethoptimism_final_total;	
} else {
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed, please try again (unsupported store currency)', 'hrsethoptimism'), 'error');
    return null;
}	
		}
		
		if ($this->ethoptimism_blockchain_fees === 'yes') {
			
			// Get the estimated feed for our crypto coin in USD fiat currency
			
		$highriskshopcryptogateway_ethoptimism_feesest_response = wp_remote_get('https://api.highriskshop.com/crypto/optimism/eth/fees.php', array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_ethoptimism_feesest_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Failed to get estimated fees, please try again', 'hrsethoptimism'), 'error');
    return null;
} else {

$highriskshopcryptogateway_ethoptimism_feesest_body = wp_remote_retrieve_body($highriskshopcryptogateway_ethoptimism_feesest_response);
$highriskshopcryptogateway_ethoptimism_feesest_conversion_resp = json_decode($highriskshopcryptogateway_ethoptimism_feesest_body, true);

if ($highriskshopcryptogateway_ethoptimism_feesest_conversion_resp && isset($highriskshopcryptogateway_ethoptimism_feesest_conversion_resp['estimated_cost_currency']['USD'])) {
    // Escape output
    $highriskshopcryptogateway_ethoptimism_feesest_final_total = sanitize_text_field($highriskshopcryptogateway_ethoptimism_feesest_conversion_resp['estimated_cost_currency']['USD']);
    $highriskshopcryptogateway_ethoptimism_feesest_reference_total = (float)$highriskshopcryptogateway_ethoptimism_feesest_final_total;	
} else {
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Failed to get estimated fees, please try again', 'hrsethoptimism'), 'error');
    return null;
}	
		}

// Convert the estimated fee back to our crypto

$highriskshopcryptogateway_ethoptimism_revfeesest_response = wp_remote_get('https://api.highriskshop.com/crypto/optimism/eth/convert.php?value=' . $highriskshopcryptogateway_ethoptimism_feesest_reference_total . '&from=usd', array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_ethoptimism_revfeesest_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed due to failed currency conversion process, please try again', 'hrsethoptimism'), 'error');
    return null;
} else {

$highriskshopcryptogateway_ethoptimism_revfeesest_body = wp_remote_retrieve_body($highriskshopcryptogateway_ethoptimism_revfeesest_response);
$highriskshopcryptogateway_ethoptimism_revfeesest_conversion_resp = json_decode($highriskshopcryptogateway_ethoptimism_revfeesest_body, true);

if ($highriskshopcryptogateway_ethoptimism_revfeesest_conversion_resp && isset($highriskshopcryptogateway_ethoptimism_revfeesest_conversion_resp['value_coin'])) {
    // Escape output
    $highriskshopcryptogateway_ethoptimism_revfeesest_final_total = sanitize_text_field($highriskshopcryptogateway_ethoptimism_revfeesest_conversion_resp['value_coin']);
    $highriskshopcryptogateway_ethoptimism_revfeesest_reference_total = (float)$highriskshopcryptogateway_ethoptimism_revfeesest_final_total;
	// Calculating order total after adding the blockchain fees
	$highriskshopcryptogateway_ethoptimism_payin_total = $highriskshopcryptogateway_ethoptimism_reference_total + $highriskshopcryptogateway_ethoptimism_revfeesest_reference_total;
} else {
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed, please try again (unsupported store currency)', 'hrsethoptimism'), 'error');
    return null;
}	
		}
		
		} else {
			
		$highriskshopcryptogateway_ethoptimism_payin_total = $highriskshopcryptogateway_ethoptimism_reference_total;	

		}
		
$highriskshopcryptogateway_ethoptimism_gen_wallet = wp_remote_get('https://api.highriskshop.com/crypto/optimism/eth/wallet.php?address=' . $this->ethoptimism_wallet_address .'&callback=' . urlencode($highriskshopcryptogateway_ethoptimism_callback), array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_ethoptimism_gen_wallet)) {
    // Handle error
    wc_add_notice(__('Wallet error:', 'woocommerce') . __('Payment could not be processed due to incorrect payout wallet settings, please contact website admin', 'hrsethoptimism'), 'error');
    return null;
} else {
	$highriskshopcryptogateway_ethoptimism_wallet_body = wp_remote_retrieve_body($highriskshopcryptogateway_ethoptimism_gen_wallet);
	$highriskshopcryptogateway_ethoptimism_wallet_decbody = json_decode($highriskshopcryptogateway_ethoptimism_wallet_body, true);

 // Check if decoding was successful
    if ($highriskshopcryptogateway_ethoptimism_wallet_decbody && isset($highriskshopcryptogateway_ethoptimism_wallet_decbody['address_in'])) {
		// Store and sanitize variables
        $highriskshopcryptogateway_ethoptimism_gen_addressIn = wp_kses_post($highriskshopcryptogateway_ethoptimism_wallet_decbody['address_in']);
		$highriskshopcryptogateway_ethoptimism_gen_callback = sanitize_url($highriskshopcryptogateway_ethoptimism_wallet_decbody['callback_url']);
        
		// Generate QR code Image
		$highriskshopcryptogateway_ethoptimism_genqrcode_response = wp_remote_get('https://api.highriskshop.com/crypto/optimism/eth/qrcode.php?address=' . $highriskshopcryptogateway_ethoptimism_gen_addressIn, array('timeout' => 30));

if (is_wp_error($highriskshopcryptogateway_ethoptimism_genqrcode_response)) {
    // Handle error
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Unable to generate QR code', 'hrsethoptimism'), 'error');
    return null;
} else {

$highriskshopcryptogateway_ethoptimism_genqrcode_body = wp_remote_retrieve_body($highriskshopcryptogateway_ethoptimism_genqrcode_response);
$highriskshopcryptogateway_ethoptimism_genqrcode_conversion_resp = json_decode($highriskshopcryptogateway_ethoptimism_genqrcode_body, true);

if ($highriskshopcryptogateway_ethoptimism_genqrcode_conversion_resp && isset($highriskshopcryptogateway_ethoptimism_genqrcode_conversion_resp['qr_code'])) {
    
    $highriskshopcryptogateway_ethoptimism_genqrcode_pngimg = wp_kses_post($highriskshopcryptogateway_ethoptimism_genqrcode_conversion_resp['qr_code']);	
	
} else {
    wc_add_notice(__('Payment error:', 'woocommerce') . __('Unable to generate QR code', 'hrsethoptimism'), 'error');
    return null;
}	
		}
		
		
		// Save $ethoptimismresponse in order meta data
    $order->add_meta_data('highriskshop_ethoptimism_payin_address', $highriskshopcryptogateway_ethoptimism_gen_addressIn, true);
    $order->add_meta_data('highriskshop_ethoptimism_callback', $highriskshopcryptogateway_ethoptimism_gen_callback, true);
	$order->add_meta_data('highriskshop_ethoptimism_payin_amount', $highriskshopcryptogateway_ethoptimism_payin_total, true);
	$order->add_meta_data('highriskshop_ethoptimism_qrcode', $highriskshopcryptogateway_ethoptimism_genqrcode_pngimg, true);
	$order->add_meta_data('highriskshop_ethoptimism_nonce', $highriskshopcryptogateway_ethoptimism_nonce, true);
	$order->add_meta_data('highriskshop_ethoptimism_status_nonce', $highriskshopcryptogateway_ethoptimism_status_nonce, true);
    $order->save();
    } else {
        wc_add_notice(__('Payment error:', 'woocommerce') . __('Payment could not be processed, please try again (wallet address error)', 'ethoptimism'), 'error');

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
    $highriskshopgateway_crypto_total = $order->get_meta('highriskshop_ethoptimism_payin_amount', true);
    $highriskshopgateway__crypto_wallet_address = $order->get_meta('highriskshop_ethoptimism_payin_address', true);
    $highriskshopgateway_crypto_qrcode = $order->get_meta('highriskshop_ethoptimism_qrcode', true);
	$highriskshopgateway_crypto_qrcode_status_nonce = $order->get_meta('highriskshop_ethoptimism_status_nonce', true);

    // CSS
	wp_enqueue_style('highriskshopcryptogateway-ethoptimism-loader-css', plugin_dir_url( __DIR__ ) . 'static/payment-status.css', array(), '1.0.0');

    // Title
    echo '<div id="highriskshopcryptogateway-wrapper"><h1 style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
        . esc_html__('Please Complete Your Payment', 'highriskshop-instant-payment-gateway-ethoptimism') 
        . '</h1>';

    // QR Code Image
    echo '<div style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '"><img style="' . esc_attr('text-align:center;max-width:80%;margin:0 auto;') . '" src="data:image/png;base64,' 
        . esc_attr($highriskshopgateway_crypto_qrcode) . '" alt="' . esc_attr('optimism/eth Payment Address') . '"/></div>';

    // Payment Instructions
	/* translators: 1: Amount of cryptocurrency to be sent, 2: Name of the cryptocurrency */
    echo '<p style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">' . sprintf( esc_html__('Please send %1$s %2$s to the following address:', 'highriskshop-instant-payment-gateway-ethoptimism'), '<br><strong>' . esc_html($highriskshopgateway_crypto_total) . '</strong>', esc_html__('optimism/eth', 'highriskshop-instant-payment-gateway-ethoptimism') ) . '</p>';


    // Wallet Address
    echo '<p style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
        . '<strong>' . esc_html($highriskshopgateway__crypto_wallet_address) . '</strong>'
        . '</p><br><hr></div>';
		
	echo '<div class="' . esc_attr('highriskshopcryptogateway-unpaid') . '" id="' . esc_attr('highriskshop-payment-status-message') . '" style="' . esc_attr('text-align:center;max-width:100%;margin:0 auto;') . '">'
                . esc_html__('Waiting for payment', 'highriskshop-instant-payment-gateway-ethoptimism')
                . '</div><br><hr><br>';	

 ?>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                function highriskshopcryptogateway_payment_status() {
                    $.ajax({
                        url: '<?php echo esc_url(rest_url('highriskshopcryptogateway/v1/highriskshopcryptogateway-check-order-status-ethoptimism/')); ?>',
                        method: 'GET',
                        data: {
                            order_id: '<?php echo esc_js($order_id); ?>',
							nonce: '<?php echo esc_js($highriskshopgateway_crypto_qrcode_status_nonce); ?>'
                        },
                        success: function(response) {
                            if (response.status === 'processing' || response.status === 'completed') {
                                $('#highriskshop-payment-status-message').text('<?php echo esc_js(__('Payment received', 'highriskshop-instant-payment-gateway-ethoptimism')); ?>')
								.removeClass('highriskshopcryptogateway-unpaid')
								.addClass('<?php echo esc_js(esc_attr('highriskshopcryptogateway-paid')); ?>');
								$('#highriskshopcryptogateway-wrapper').remove();
                            } else if (response.status === 'failed') { 
							    $('#highriskshop-payment-status-message').text('<?php echo esc_js(__('Payment failed, you may have sent incorrect amount or token. Contact support', 'highriskshop-instant-payment-gateway-ethoptimism')); ?>')
								.removeClass('highriskshopcryptogateway-unpaid')
								.addClass('<?php echo esc_js(esc_attr('highriskshopcryptogateway-failed')); ?>');
								$('#highriskshopcryptogateway-wrapper').remove();
							} else {
                                $('#highriskshop-payment-status-message').text('<?php echo esc_js(__('Waiting for payment', 'highriskshop-instant-payment-gateway-ethoptimism')); ?>');
                            }
                        },
                        error: function() {
                            $('#highriskshop-payment-status-message').text('<?php echo esc_js(__('Error checking payment status. Please refresh the page.', 'highriskshop-instant-payment-gateway-ethoptimism')); ?>');
                        }
                    });
                }

                setInterval(highriskshopcryptogateway_payment_status, 60000);
            });
            </script>
            <?php

}



}

function highriskshop_add_instant_payment_gateway_ethoptimism($gateways) {
    $gateways[] = 'HighRiskShop_Instant_Payment_Gateway_Ethoptimism';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'highriskshop_add_instant_payment_gateway_ethoptimism');
}

// Add custom endpoint for reading crypto payment status

   function highriskshopcryptogateway_ethoptimism_check_order_status_rest_endpoint() {
        register_rest_route('highriskshopcryptogateway/v1', '/highriskshopcryptogateway-check-order-status-ethoptimism/', array(
            'methods'  => 'GET',
            'callback' => 'highriskshopcryptogateway_ethoptimism_check_order_status_callback',
            'permission_callback' => '__return_true',
        ));
    }

    add_action('rest_api_init', 'highriskshopcryptogateway_ethoptimism_check_order_status_rest_endpoint');

    function highriskshopcryptogateway_ethoptimism_check_order_status_callback($request) {
        $order_id = absint($request->get_param('order_id'));
		$highriskshopcryptogateway_ethoptimism_live_status_nonce = sanitize_text_field($request->get_param('nonce'));

        if (empty($order_id)) {
            return new WP_Error('missing_order_id', __('Order ID parameter is missing.', 'highriskshop-instant-payment-gateway-ethoptimism'), array('status' => 400));
        }

        $order = wc_get_order($order_id);

        if (!$order) {
            return new WP_Error('invalid_order', __('Invalid order ID.', 'highriskshop-instant-payment-gateway-ethoptimism'), array('status' => 404));
        }
		
		// Verify stored status nonce

        if ( empty( $highriskshopcryptogateway_ethoptimism_live_status_nonce ) || $order->get_meta('highriskshop_ethoptimism_status_nonce', true) !== $highriskshopcryptogateway_ethoptimism_live_status_nonce ) {
        return new WP_Error( 'invalid_nonce', __( 'Invalid nonce.', 'highriskshop-instant-payment-gateway-ethoptimism' ), array( 'status' => 403 ) );
    }
        return array('status' => $order->get_status());
    }

// Add custom endpoint for changing order status
function highriskshopcryptogateway_ethoptimism_change_order_status_rest_endpoint() {
    // Register custom route
    register_rest_route( 'highriskshopcryptogateway/v1', '/highriskshopcryptogateway-ethoptimism/', array(
        'methods'  => 'GET',
        'callback' => 'highriskshopcryptogateway_ethoptimism_change_order_status_callback',
        'permission_callback' => '__return_true',
    ));
}
add_action( 'rest_api_init', 'highriskshopcryptogateway_ethoptimism_change_order_status_rest_endpoint' );

// Callback function to change order status
function highriskshopcryptogateway_ethoptimism_change_order_status_callback( $request ) {
    $order_id = absint($request->get_param( 'order_id' ));
	$highriskshopcryptogateway_ethoptimismgetnonce = sanitize_text_field($request->get_param( 'nonce' ));
	$highriskshopcryptogateway_ethoptimismpaid_value_coin = sanitize_text_field($request->get_param('value_coin'));
	$highriskshopcryptogateway_ethoptimism_paid_coin_name = sanitize_text_field($request->get_param('coin'));
	$highriskshopcryptogateway_ethoptimism_paid_txid_in = sanitize_text_field($request->get_param('txid_in'));

    // Check if order ID parameter exists
    if ( empty( $order_id ) ) {
        return new WP_Error( 'missing_order_id', __( 'Order ID parameter is missing.', 'highriskshop-instant-payment-gateway-ethoptimism' ), array( 'status' => 400 ) );
    }

    // Get order object
    $order = wc_get_order( $order_id );

    // Check if order exists
    if ( ! $order ) {
        return new WP_Error( 'invalid_order', __( 'Invalid order ID.', 'highriskshop-instant-payment-gateway-ethoptimism' ), array( 'status' => 404 ) );
    }
	
	// Verify nonce
    if ( empty( $highriskshopcryptogateway_ethoptimismgetnonce ) || $order->get_meta('highriskshop_ethoptimism_nonce', true) !== $highriskshopcryptogateway_ethoptimismgetnonce ) {
        return new WP_Error( 'invalid_nonce', __( 'Invalid nonce.', 'highriskshop-instant-payment-gateway-ethoptimism' ), array( 'status' => 403 ) );
    }

    // Check if the order is pending and payment method is 'highriskshop-instant-payment-gateway-ethoptimism'
    if ( $order && !in_array($order->get_status(), ['processing', 'completed'], true) && 'highriskshop-instant-payment-gateway-ethoptimism' === $order->get_payment_method() ) {
		
		// Get the expected amount and coin
	$highriskshopcryptogateway_ethoptimismexpected_amount = $order->get_meta('highriskshop_ethoptimism_payin_amount', true);
	$highriskshopcryptogateway_ethoptimismexpected_coin = $order->get_meta('highriskshop_ethoptimism_payin_amount', true);
	
		if ( $highriskshopcryptogateway_ethoptimismpaid_value_coin < $highriskshopcryptogateway_ethoptimismexpected_amount || $highriskshopcryptogateway_ethoptimism_paid_coin_name !== 'optimism_eth') {
			// Mark the order as failed and add an order note
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Expected amount, 4: Transaction ID */			
$order->update_status('failed', sprintf(__( '[Order Failed] Customer sent %1$s %2$s instead of %3$s optimism_eth. TXID: %4$s', 'highriskshop-instant-payment-gateway-ethoptimism' ), $highriskshopcryptogateway_ethoptimismpaid_value_coin, $highriskshopcryptogateway_ethoptimism_paid_coin_name, $highriskshopcryptogateway_ethoptimismexpected_amount, $highriskshopcryptogateway_ethoptimism_paid_txid_in));
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Expected amount, 4: Transaction ID */
$order->add_order_note(sprintf( __( '[Order Failed] Customer sent %1$s %2$s instead of %3$s optimism_eth. TXID: %4$s', 'highriskshop-instant-payment-gateway-ethoptimism' ), $highriskshopcryptogateway_ethoptimismpaid_value_coin, $highriskshopcryptogateway_ethoptimism_paid_coin_name, $highriskshopcryptogateway_ethoptimismexpected_amount, $highriskshopcryptogateway_ethoptimism_paid_txid_in));
            return array( 'message' => 'Order status changed to failed due to partial payment or incorrect coin. Please check order notes' );
			
		} else {
        // Change order status to processing
		$order->payment_complete();
		/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Transaction ID */
		$order->update_status('processing', sprintf( __( '[Payment completed] Customer sent %1$s %2$s TXID:%3$s', 'highriskshop-instant-payment-gateway-ethoptimism' ), $highriskshopcryptogateway_ethoptimismpaid_value_coin, $highriskshopcryptogateway_ethoptimism_paid_coin_name, $highriskshopcryptogateway_ethoptimism_paid_txid_in));

// Return success response
/* translators: 1: Paid value in coin, 2: Paid coin name, 3: Transaction ID */
$order->add_order_note(sprintf( __( '[Payment completed] Customer sent %1$s %2$s TXID:%3$s', 'highriskshop-instant-payment-gateway-ethoptimism' ), $highriskshopcryptogateway_ethoptimismpaid_value_coin, $highriskshopcryptogateway_ethoptimism_paid_coin_name, $highriskshopcryptogateway_ethoptimism_paid_txid_in));
        return array( 'message' => 'Order status changed to processing.' );
		}
    } else {
        // Return error response if conditions are not met
        return new WP_Error( 'order_not_eligible', __( 'Order is not eligible for status change.', 'highriskshop-instant-payment-gateway-ethoptimism' ), array( 'status' => 400 ) );
    }
}
?>