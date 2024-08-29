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

		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-btc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-bch.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-ltc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-doge.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-oneinchbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-adabep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-bnbbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-btcbbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-cakebep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-daibep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-dogebep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-ethbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-injbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-ltcbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-maticbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-phptbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-shibbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-thcbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdtbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-virtubep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-xrpbep20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-oneincherc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-arberc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-bnberc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-daierc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-eurcerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-eurterc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-linkerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-mkrerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-nexoerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-pepeerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-shiberc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-tusderc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdperc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdterc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-verseerc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-arbarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-daiarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-etharbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-linkarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-pepearbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcearbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdtarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-wbtcarbitrum.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-avaxpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-manapolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-maticpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-smtpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcepolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdtpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-virtupolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-wbtcpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-wethpolygon.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-avaxavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-btcbavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-eurcavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdceavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdtavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-wavaxavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-wbtceavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-wetheavaxc.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-daibase.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-ethbase.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-eurcbase.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcbase.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-daioptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-ethoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-linkoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-opoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdcoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdceoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdtoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-wbtcoptimism.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-eth.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-aedttrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-btctrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-inrttrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-tusdtrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-usdttrc20.php'); // Include the payment gateway class
		include_once(plugin_dir_path(__FILE__) . 'includes/class-highriskshop-instant-payment-gateway-trx.php'); // Include the payment gateway class
?>