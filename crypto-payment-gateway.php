<?php
/**
 * Plugin Name: Crypto Payment Gateway with Instant Payouts
 * Plugin URI: https://www.highriskshop.com/crypto-payment-gateway-no-kyc-instant-payouts/
 * Description: Cryptocurrency Payment Gateway with instant payouts to your wallet and without KYC hosted directly on your website.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Tested up to: 6.6.1
 * WC requires at least: 5.8
 * WC tested up to: 9.2.2
 * Requires PHP: 7.2
 * Author: HighRiskShop.COM
 * Author URI: https://www.highriskshop.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

add_action('before_woocommerce_init', function() {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
    }
} );

/**
 * Enqueue block assets for the gateway.
 */
function highriskshopcryptogateway_enqueue_block_assets() {
    // Fetch all enabled WooCommerce payment gateways
    $highriskshopcryptogateway_available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
    $highriskshopcryptogateway_gateways_data = array();

    foreach ($highriskshopcryptogateway_available_gateways as $gateway_id => $gateway) {
		if (strpos($gateway_id, 'highriskshop-crypto-payment-gateway') === 0) {
        $icon_url = !empty($gateway->icon) ? esc_url($gateway->icon) : '';
        $highriskshopcryptogateway_gateways_data[] = array(
            'id' => sanitize_key($gateway_id),
            'label' => sanitize_text_field($gateway->get_title()),
            'description' => wp_kses_post($gateway->get_description()),
            'icon_url' => sanitize_url($icon_url),
        );
		}
    }

    wp_enqueue_script(
        'highriskshopcryptogateway-block-support',
        plugin_dir_url(__FILE__) . 'assets/js/highriskshopcryptogateway-block-checkout-support.js',
        array('wc-blocks-registry', 'wp-element', 'wp-i18n', 'wp-components', 'wp-blocks', 'wp-editor'),
        filemtime(plugin_dir_path(__FILE__) . 'assets/js/highriskshopcryptogateway-block-checkout-support.js'),
        true
    );

    // Localize script with gateway data
    wp_localize_script(
        'highriskshopcryptogateway-block-support',
        'highriskshopcryptogatewayData',
        $highriskshopcryptogateway_gateways_data
    );
}
add_action('enqueue_block_assets', 'highriskshopcryptogateway_enqueue_block_assets');

/**
 * Enqueue styles for the gateway on checkout page.
 */
function highriskshopcryptogateway_enqueue_styles() {
    if (is_checkout()) {
        wp_enqueue_style(
            'highriskshopcryptogateway-styles',
            plugin_dir_url(__FILE__) . 'assets/css/highriskshopcryptogateway-payment-gateway-styles.css',
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'assets/css/highriskshopcryptogateway-payment-gateway-styles.css')
        );
    }
}
add_action('wp_enqueue_scripts', 'highriskshopcryptogateway_enqueue_styles');

		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-btc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-bch.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-ltc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-doge.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-oneinchbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-adabep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-bnbbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-btcbbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-cakebep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-daibep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-dogebep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-ethbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-injbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-ltcbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-phptbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-shibbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-thcbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdtbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-virtubep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-xrpbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-oneincherc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-arberc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-bnberc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-daierc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-eurcerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-eurterc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-linkerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-mkrerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-nexoerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-pepeerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-shiberc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-tusderc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdperc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdterc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-verseerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-arbarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-daiarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-etharbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-linkarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-pepearbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcearbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdtarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-wbtcarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-avaxpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-manapolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-polpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-smtpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcepolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdtpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-virtupolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-wbtcpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-wethpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-avaxavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-btcbavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-eurcavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdceavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdtavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-wavaxavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-wbtceavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-wetheavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-daibase.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-ethbase.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-eurcbase.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcbase.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-daioptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-ethoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-linkoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-opoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdcoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdceoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdtoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-wbtcoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-eth.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-aedttrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-btctrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-inrttrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-tusdtrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-usdttrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-crypto-payment-gateway-trx.php'); // Include the payment gateway class
?>