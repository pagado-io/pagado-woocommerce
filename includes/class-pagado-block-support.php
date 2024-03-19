<?php

/**
 * Pagado payment gateway WooCommerce block support class.
 *
 * @link            #
 * @since           2.2.0
 *
 * @package         Pagado
 * @subpackage      Pagado/includes
 */

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

class Pagado_Block_Support extends AbstractPaymentMethodType
{
    private $gateway;
    protected $name = 'pagado';

    public function initialize()
    {
        $this->settings = get_option("woocommerce_{$this->name}_settings", array());
    }

    public function is_active()
    {
        return !empty($this->settings['enabled']) && 'yes' === $this->settings['enabled'];
    }

    public function get_payment_method_script_handles()
    {

        wp_register_script(
            'pagado-blocks-integration',
            plugin_dir_url(__DIR__) . 'build/index.js',
            array(
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
            ),
            null, // or time() or filemtime( ... ) to skip caching
            true
        );

        return array('pagado-blocks-integration');
    }

    public function get_payment_method_data()
    {
        return array(
            'title'        => $this->get_setting('title'),
            'description'  => $this->get_setting('description'),
        );
    }
}
