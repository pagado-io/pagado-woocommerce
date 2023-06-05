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

class PagadoPaymentGatewayLoader
{
    public static function loadGateway()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'gateway/class-pagado-payment-gateway.php';
    }

    public static function addGateway($methods)
    {
        $methods[] = 'PagadoPaymentGateway';
        return $methods;
    }
}
