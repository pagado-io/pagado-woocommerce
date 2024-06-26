<?php

/**
 * Pagado payment gateway class
 *
 * @link            #
 * @since           0.1.0
 *
 * @package         Pagado
 * @subpackage      Pagado/gateway
 */

class Pagado_Payment_Gateway extends WC_Payment_Gateway
{
    public $email = null;
    public $api_key = null;

    public function __construct()
    {
        $this->id = 'pagado';
        $this->icon = '';
        $this->method_title = 'Pagado Gateway';
        $this->method_description = __('Pagado payment gateway for WooCommerce', 'pagado');
        $this->supports = array('products');
        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');
        $this->email = $this->get_option('email');
        $this->api_key = $this->get_option('api_key');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'pagado'),
                'type' => 'checkbox',
                'label' => __('Enable Pagado Payment Gateway', 'pagado'),
                'default' => 'yes',
            ),
            'title' => array(
                'title' => __('Title', 'pagado'),
                'type' => 'text',
                'description' => __('The title displayed to the user during checkout.', 'pagado'),
                'default' => __('Pagado', 'pagado'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'pagado'),
                'type' => 'textarea',
                'description' => __('The description displayed to the user during checkout.', 'pagado'),
                'default' => __('Pay securely using Pagado.', 'pagado'),
                'desc_tip' => true,
            ),
            'email' => array(
                'title' => __('Pagado email', 'pagado'),
                'type' => 'email',
                'description' => __('Provide your pagado email.', 'pagado'),
                'desc_tip' => true,
            ),
            'api_key' => array(
                'title' => __('API Direct key', 'pagado'),
                'type' => 'text',
                'description' => __('Provide your Pagado API Direct key.', 'pagado'),
                'desc_tip' => true,
            ),
        );
    }

    public function payment_fields()
    {
        echo $this->description;
    }

    public function process_payment($order_id)
    {
        $order = new WC_Order($order_id);

        $data = wc_get_var($_REQUEST['pagado_data']);
        $data = json_decode(html_entity_decode(stripslashes($data)));

        $currency = $order->get_currency();

        if ($currency !== 'MILLIX') {
            wc_add_notice(__('Pagado only supports Millix. Invalid currency: ', 'pagado') . $currency, 'error');
            return;
        }

        do_action('before_pagado_checkout_init', $order_id, $order);

        if ($data->token) {
            $server = 'https://pagado.io'; // change
            $url = $server . '/api/pagado/confirm-checkout';
            $nonce = substr(str_shuffle(md5(microtime())), 0, 12);
            $cookies = array();
            $cookies[] = new WP_Http_Cookie(array('name' => 'AuthToken', 'value' => $data->token));

            foreach ($_COOKIE as $name => $value) {
                $cookies[] = new WP_Http_Cookie(array('name' => $name, 'value' => $value));
            }

            $request = wp_remote_post($url, array(
                'body' => array(
                    'to' => $data->order->to,
                    'price' => $data->order->price,
                    'order_id' => $order->get_id(),
                    'pg_nonce' => $nonce,
                ),
                'cookies' => $cookies,
                'sslverify' => true, // enable
            ));

            if (is_wp_error($request)) {
                $err_msg = $request->get_error_message();
                throw new Exception($err_msg);
            }

            $response = wp_remote_retrieve_body($request);
            $response = json_decode($response);

            if (
                $response->success == true &&
                $response->content->price == $order->get_subtotal() &&
                $response->content->pg_nonce == $nonce
            ) {
                $transaction_id = $response->content->transaction_id;

                wc_reduce_stock_levels($order);

                $order->update_status('completed', __('Payment complete.', 'pagado'));
                $order->set_transaction_id($transaction_id);
                $order->add_order_note("Transaction ID: " . $transaction_id, 1);

                WC()->cart->empty_cart();

                do_action('pagado_checkout_complete', $order_id, $order, $transaction_id);

                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url($order),
                );
            } else {
                throw new Exception("{$response->message}: {$response->error}.");
            }

            throw new Exception("Error while processing payment.");
        }
    }
}
