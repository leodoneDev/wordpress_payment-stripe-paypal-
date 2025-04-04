<?php

/**
 * @since      1.0.0
 * @package    PPCP_Paypal_Checkout_For_Woocommerce
 * @subpackage PPCP_Paypal_Checkout_For_Woocommerce/ppcp/includes
 * @author     PayPal <wpeasypayment@gmail.com>
 */
class PPCP_Paypal_Checkout_For_Woocommerce {

    protected $loader;
    protected $plugin_name;
    protected $version;
    public $button_manager;
    public $subscription_support_enabled;

    public function __construct() {
        if (defined('WPG_PLUGIN_VERSION')) {
            $this->version = WPG_PLUGIN_VERSION;
        } else {
            $this->version = '5.1.0';
        }
        $this->plugin_name = 'woo-paypal-gateway';
        add_filter('woocommerce_payment_gateways', array($this, 'ppcp_woocommerce_payment_gateways'), 999);

        $this->load_dependencies();
        $seller_onboarding = PPCP_Paypal_Checkout_For_Woocommerce_Seller_Onboarding::instance();
        if (!has_action('admin_init', array($seller_onboarding, 'wpg_listen_for_merchant_id'))) {
            add_action('admin_init', array($seller_onboarding, 'wpg_listen_for_merchant_id'));
        }
        $this->set_locale();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once WPG_PLUGIN_DIR . '/ppcp/includes/ppcp-paypal-checkout-for-woocommerce-function.php';
        require_once WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-loader.php';
        require_once WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-i18n.php';
        require_once WPG_PLUGIN_DIR . '/ppcp/public/class-ppcp-paypal-checkout-for-woocommerce-button-manager.php';
        require_once WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-product.php';
        require_once WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-pay-later-messaging.php';
        require_once WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-seller-onboarding.php';
        require_once WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-tracking.php';
        PPCP_Paypal_Checkout_For_Woocommerce_Tracking::get_instance();
        $this->loader = new PPCP_Paypal_Checkout_For_Woocommerce_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new PPCP_Paypal_Checkout_For_Woocommerce_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_public_hooks() {
        $this->button_manager = PPCP_Paypal_Checkout_For_Woocommerce_Button_Manager::instance();
        PPCP_Paypal_Checkout_For_Woocommerce_Pay_Later::instance();
        PPCP_Paypal_Checkout_For_Woocommerce_Seller_Onboarding::instance();
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

    public function ppcp_woocommerce_payment_gateways($methods) {
        $advanced_card_position = $this->button_manager->advanced_card_payments_display_position;
        $this->subscription_support_enabled = class_exists('WC_Subscriptions') && function_exists('wcs_create_renewal_order');
        include_once WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-gateway.php';
        if ($this->subscription_support_enabled) {
            include_once WPG_PLUGIN_DIR . '/ppcp/subscriptions/class-ppcp-paypal-checkout-for-woocommerce-subscriptions.php';
            include_once WPG_PLUGIN_DIR . '/ppcp/subscriptions/class-ppcp-paypal-checkout-for-woocommerce-subscriptions-cc.php';
        }
        $is_checkout_settings_page = isset($_GET['page'], $_GET['tab']) && $_GET['page'] === 'wc-settings' && $_GET['tab'] === 'checkout';
        if (!$is_checkout_settings_page) {
            if (!class_exists('PPCP_Paypal_Checkout_For_Woocommerce_Gateway_CC')) {
                include_once WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-gateway-cc.php';
            }
            $methods[] = $this->subscription_support_enabled ? 'PPCP_Paypal_Checkout_For_Woocommerce_Subscriptions_CC' : 'PPCP_Paypal_Checkout_For_Woocommerce_Gateway_CC';
        }
        $methods[] = $this->subscription_support_enabled ? 'PPCP_Paypal_Checkout_For_Woocommerce_Subscriptions' : 'PPCP_Paypal_Checkout_For_Woocommerce_Gateway';
        array_reverse($methods);
        if($this->subscription_support_enabled) {
            $methods = wpg_ppcp_reorder_methods($methods, 'PPCP_Paypal_Checkout_For_Woocommerce_Subscriptions', 'PPCP_Paypal_Checkout_For_Woocommerce_Subscriptions_CC', $advanced_card_position);
        } else {
            $methods = wpg_ppcp_reorder_methods($methods, 'PPCP_Paypal_Checkout_For_Woocommerce_Gateway', 'PPCP_Paypal_Checkout_For_Woocommerce_Gateway_CC', $advanced_card_position);
        }
        return $methods;
    }
}
