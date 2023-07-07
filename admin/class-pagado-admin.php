<?php

/**
 * The admin-specific functionality of Pagado
 * @link        #
 * @since       0.1.0
 *
 * @package     Pagado
 * @subpackage  Pagado/admin
 */

class Pagado_Admin
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function add_millix_currency($currencies)
    {
        $currencies['MILLIX'] = __('Millix', 'pagado');
        return $currencies;
    }

    public function add_millix_currency_symbol($currency_symbol, $currency)
    {
        switch ($currency) {
            case 'MILLIX':
                $currency_symbol = 'MLX';
                break;
        }
        return $currency_symbol;
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/pagado-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/pagado-admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }
}
