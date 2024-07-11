<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link            #
 * @since           0.1.0
 *
 * @package         Pagado
 * @subpackage      Pagado/public
 */

class Pagado_Public
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/pagado-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/pagado-public.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    public function add_after_checkout_button()
    {
        $server = 'https://pagado.io';

        echo '<div id="pagado-checkout-wrapper" class="pagado-hidden"><iframe id="pagado-checkout-iframe" src="' . $server . '/checkout-buttons" name="pagado_checkout_iframe" height="100%" width="100%" title="Pagado Checkout" style="border:none;"></iframe></div>';
    }

    public function get_pagado_order_data()
    {
        try {
            $gateway = WC()->payment_gateways->payment_gateways()[$this->plugin_name];
            $cart = [];

            foreach (WC()->cart->get_cart() as $item) {
                $quantity = $item['quantity'];
                $product = $item['data'];
                $sku = $product->get_sku();
                $name = $product->get_name();
                $price = $product->get_price();

                $cart_item = [];
                $cart_item['sku'] = $sku;
                $cart_item['name'] = $name;
                $cart_item['price'] = $price;
                $cart_item['quantity'] = $quantity;

                $cart[] = $cart_item;
            }
            $data['cart'] = json_encode($cart);
            $data['to'] = $gateway->email;
            $data['price'] = WC()->cart->subtotal;
            $data['currency'] = get_woocommerce_currency();
            $data['version'] = $this->version;
            $data['variant'] = 'WC';

            wp_send_json_success($data);
        } catch (Exception $e) {
            wp_send_json_error($e);
        }
    }
}
