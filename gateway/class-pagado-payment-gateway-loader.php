<?php

/**
 * Pagado payment gateway loader class
 *
 * @link            #
 * @since           0.1.0
 *
 * @package         Pagado
 * @subpackage      Pagado/gateway
 */

class Pagado_Payment_Gateway_Loader
{
    public static function load_gateway()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'gateway/class-pagado-payment-gateway.php';
    }

    public static function add_gateway($methods)
    {
        $methods[] = 'Pagado_Payment_Gateway';
        return $methods;
    }
}
