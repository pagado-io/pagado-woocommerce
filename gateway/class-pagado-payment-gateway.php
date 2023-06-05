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

class PagadoPaymentGateway extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'pagado_payment_gateway';
        $this->method_title = 'Pagado Payment Gateway';
        $this->method_description = __('Pagado payment gateway for WooCommerce', 'pagado');
        $this->supports = array(
            'products',
        );

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'processAdminOptions'));
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
                'type'  => 'text',
                'description' => __('The title displayed to the user during checkout.', 'pagado'),
                'default' => __('Pagado', 'pagado'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'pagado'),
                'type' => 'textarea',
                'description' => __('Pay securely using Pagado payment gateway.', 'pagado'),
                'desc_tip' => true,
            ),
        );
    }

    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        $order->update_status('on-hold', __('Payment pending.', 'pagado'));
        $order->reduce_order_stock();

        WC()->cart->empty_cart();

        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }
}
