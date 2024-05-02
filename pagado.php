<?php

/**
 * Main plugin file
 *
 * @link                    #
 * @since                   0.1.0
 * @package                 Pagado
 *
 * @wordpress-plugin
 * Plugin Name:             Pagado
 * Plugin URI:              https://pagado.io
 * Description:             Pagado payment processor for WooCommerce.
 * Version:                 2.2.1
 * Requires at least:       6.3
 * Requires PHP:            7.4
 * Requires Plugins:        woocommerce
 * WC requires at least:    5.0.0
 * Author:                  Pagado
 * Author URI:              https://pagado.io
 * License:                 GPL-2.0+
 * License URI:             http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:             pagado
 * Domain Path:             /languages
 */

if (!defined('ABSPATH')) {
    die;
}

// Define constants
define('PAGADO_VERSION', '2.2.1');
define('PAGADO_ROOT', plugin_dir_path(__FILE__));
define('PAGADO_ROOT_URL', plugin_dir_url(__FILE__));

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_pagado');
register_deactivation_hook(__FILE__, 'deactivate_pagado');

// Initialize Pagado features
add_action('woocommerce_loaded', 'pagado_init');
add_action('dokan_loaded', 'dokan_init');

function pagado_init()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado.php';
    // TODO: Make Pagado singleton
    $pagado = new Pagado();
    $pagado->initialize();
    // add_action('woocommerce_blocks_loaded', 'register_block_support');
}

function dokan_init()
{
    require_once plugin_dir_path(__FILE__) . 'dokan/pagado-dokan-payment-method.php';
}

function activate_pagado()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado-activator.php';
    Pagado_Activator::activate();
}

function deactivate_pagado()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado-activator.php';
    Pagado_Activator::deactivate();
}

function register_block_support()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado-block-support.php';

    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function ($registry) {
            $registry->register(new Pagado_Block_Support());
        }
    );
}
