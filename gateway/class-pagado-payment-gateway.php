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
    public $email = '';

    public function __construct()
    {
        $this->id = 'pagado';
        $this->icon = '';
        $this->method_title = 'Pagado';
        $this->method_description = __('Pagado payment gateway for WooCommerce', 'pagado');
        $this->supports = array('products');
        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->email = $this->get_option('email');

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
            'password' => array(
                'title' => __('Pagado password', 'pagado'),
                'type' => 'password',
                'description' => __('Provide your pagado password.', 'pagado'),
                'desc_tip' => true,
            ),
        );
    }

    public function payment_fields()
    {
    }

    public function process_payment($order_id)
    {
        $order = new WC_Order($order_id);
        $transactionId = wc_get_var($_REQUEST['transaction_id']);

        if ($transactionId) {
            $server = 'https://pagado.io'; // TODO: Update

            $url = $server . '/api/pagado/transaction/' . $transactionId;
            $cookies = array();

            foreach ($_COOKIE as $name => $value) {
                $cookies[] = new WP_Http_Cookie(array('name' => $name, 'value' => $value));
            }

            $request = wp_remote_get($url, array(
                'cookies' => $cookies,
                'sslverify' => false, // TODO: enable (optional)
            ));

            $response = wp_remote_retrieve_body($request);
            $response = json_decode($response);

            $rspTransactionId = $response->transaction_hash;
            $rspTransactionAmount = $response->amount;

            if ($rspTransactionId && $rspTransactionAmount) {
                if (
                    $transactionId == $rspTransactionId &&
                    $order->get_subtotal() == $rspTransactionAmount
                ) {
                    $order->update_status('completed', __('Payment complete.', 'pagado'));
                    wc_reduce_stock_levels($order);
                    WC()->cart->empty_cart();

                    return array(
                        'result' => 'success',
                        'redirect' => $this->get_return_url($order),
                    );
                }
            }

            return array(
                'result' => 'failure',
                'message' => 'Payment failed',
            );
        }
    }
}
