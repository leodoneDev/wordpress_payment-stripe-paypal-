<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class PPCP_Checkout_Block extends AbstractPaymentMethodType {

    private $gateway;
    protected $name = 'wpg_paypal_checkout';
    public $pay_later;
    public $icon;
    public $button_class;
    public $button_size;
    public $is_mobile;
    public $device_class;

    public function initialize() {
        $this->settings = get_option('woocommerce_wpg_paypal_checkout_settings', []);
        if (!class_exists('PPCP_Paypal_Checkout_For_Woocommerce_Gateway')) {
            include_once ( WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-gateway.php');
        }
        $this->gateway = new PPCP_Paypal_Checkout_For_Woocommerce_Gateway();
        if (!class_exists('PPCP_Paypal_Checkout_For_Woocommerce_Pay_Later')) {
            include_once ( WPG_PLUGIN_DIR . '/ppcp/includes/class-ppcp-paypal-checkout-for-woocommerce-pay-later-messaging.php');
        }
        $this->pay_later = PPCP_Paypal_Checkout_For_Woocommerce_Pay_Later::instance();
        
    }

    public function is_active() {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {
        wp_enqueue_script('ppcp-checkout-js');
        if (ppcp_has_active_session() === false) {
            wp_enqueue_script('ppcp-paypal-checkout-for-woocommerce-public');
        }
        wp_enqueue_style("ppcp-paypal-checkout-for-woocommerce-public");
        //$this->pay_later->add_pay_later_script_in_frontend();
        wp_register_script('wpg_paypal_checkout-blocks-integration', WPG_PLUGIN_ASSET_URL . 'ppcp/checkout-block/ppcp-checkout.js', array('jquery', 'react', 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-i18n', 'wp-polyfill', 'wp-element', 'wp-plugins'), WPG_PLUGIN_VERSION, true);
        if (ppcp_has_active_session()) {
            $order_button_text = apply_filters('wpg_paypal_checkout_order_review_page_place_order_button_text', __('Confirm Your PayPal Order', 'woo-paypal-gateway'));
        } else {
            $order_button_text = 'Proceed to PayPal';
        }
        $is_paylater_enable_incart_page = 'no';
        if ($this->pay_later->is_paypal_pay_later_messaging_enable_for_page($page = 'cart')) {
            $is_paylater_enable_incart_page = 'yes';
        } else {
            $is_paylater_enable_incart_page = 'no';
        }
        $this->is_mobile = wp_is_mobile();
        $this->device_class = $this->is_mobile ? 'mobile' : 'desktop';
        $page = '';
        $is_pay_page = '';
        $this->button_class = $this->device_class . ' ' . 'responsive';
        if (is_product()) {
            $page = 'product';
        } else if (is_cart() && WC()->cart && !WC()->cart->is_empty()) {
            $page = 'cart';
            $this->button_size = isset($this->settings['cart_button_size']) ? $this->settings['cart_button_size'] : 'responsive';
            $this->button_class = $this->device_class . ' ' . $this->button_size;
        } elseif (is_checkout_pay_page()) {
            $page = 'checkout';
            $is_pay_page = 'yes';
            $this->button_size = isset($this->settings['checkout_button_size']) ? $this->settings['checkout_button_size'] : 'responsive';
            $this->button_class = $this->device_class . ' ' . $this->button_size;
        } elseif (is_checkout()) {
            $page = 'checkout';
            $this->button_size = isset($this->settings['checkout_button_size']) ? $this->settings['checkout_button_size'] : 'responsive';
            $this->button_class = $this->device_class . ' ' . $this->button_size;
        }
        $all_settings = $this->settings;
        $required_keys = ['enable_checkout_button_top', 'show_on_cart'];
        $filtered_settings = array_intersect_key($all_settings, array_flip($required_keys));
        if(!isset($filtered_settings['enable_checkout_button_top'])) {
            $filtered_settings['enable_checkout_button_top'] = 'yes';
        }
        if(!isset($filtered_settings['show_on_cart'])) {
            $filtered_settings['show_on_cart'] = 'no';
        }
        wp_localize_script('wpg_paypal_checkout-blocks-integration', 'wpg_paypal_checkout_manager_block', array(
            'placeOrderButtonLabel' => $order_button_text,
            'is_order_confirm_page' => (ppcp_has_active_session() === false) ? 'no' : 'yes',
            'is_paylater_enable_incart_page' => $is_paylater_enable_incart_page,
            'settins' => $filtered_settings,
            'page' => $page,
            'is_block_enable' => 'yes',
            'is_google_pay_enable_for_cart' => $this->is_google_pay_enable_for_page('cart') ? 'yes' : 'no',
            'is_google_pay_enable_for_express_checkout' => $this->is_google_pay_enable_for_page('express_checkout') ? 'yes' : 'no',
            'is_google_pay_enable_for_checkout' => $this->is_google_pay_enable_for_page('checkout') ? 'yes' : 'no',
            'is_apple_pay_enable_for_cart' => $this->is_apple_pay_enable_for_page('cart') ? 'yes' : 'no',
            'is_apple_pay_enable_for_express_checkout' => $this->is_apple_pay_enable_for_page('express_checkout') ? 'yes' : 'no',
            'is_apple_pay_enable_for_checkout' => $this->is_apple_pay_enable_for_page('checkout') ? 'yes' : 'no',
            'is_mobile' => wp_is_mobile() ? 'mobile' : 'desktop',
            'button_class' => $this->button_class
        ));

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('wpg_paypal_checkout-blocks-integration', 'woo-paypal-gateway');
        }
        wp_enqueue_script('wpg_paypal_checkout');
        if (ppcp_has_active_session() === false && $page === 'cart') {
            do_action('wpg_paypal_checkout_woo_cart_block_pay_later_message');
        }
        return ['wpg_paypal_checkout-blocks-integration'];
    }
    
    public function is_google_pay_enable_for_page($page = '') {
        if (!isset($this->settings['enabled_google_pay'])) {
            return false;
        }
        if ($this->settings['enabled_google_pay'] === 'no') {
            return false;
        }
        if (empty($page)) {
            return false;
        }
        if(!isset($this->settings['google_pay_pages'])) {
            $this->settings['google_pay_pages'] = array('express_checkout');
        }
        if (empty($this->settings['google_pay_pages'])) {
            return false;
        }
        if (in_array($page, $this->settings['google_pay_pages'])) {
            return true;
        }
        return false;
    }
    
    public function is_apple_pay_enable_for_page($page = '') {
        if (!isset($this->settings['enabled_apple_pay'])) {
            return false;
        }
        if ($this->settings['enabled_apple_pay'] === 'no') {
            return false;
        }
        if (empty($page)) {
            return false;
        }
        if (empty($this->settings['apple_pay_pages'])) {
            return false;
        }
        if (in_array($page, $this->settings['apple_pay_pages'])) {
            return true;
        }
        return false;
    }

    public function get_payment_method_data() {
        $this->icon = apply_filters('woocommerce_ppcp_cc_icon', WPG_PLUGIN_ASSET_URL . 'assets/images/wpg_paypal.png');
        return [
            'title' => $this->gateway->title,
            'description' => $this->get_setting('description'),
            'supports' => $this->get_supported_features(),
            'icons' => $this->gateway->icon
        ];
    }
}
