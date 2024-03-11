<?php

/**
 * Main plugin file
 *
 * @link            #
 * @since           0.1.0
 * @package         Pagado
 *
 * @wordpress-plugin
 * Plugin Name:     Pagado
 * Plugin URI:      https://pagado.io
 * Description:     Pagado payment processor for WooCommerce.
 * Version:         2.0.0
 * Requires PHP:    7.2
 * Author:          Pagado
 * Author URI:      https://pagado.io
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     pagado
 * Domain Path:     /languages
 */

if (!defined('WPINC')) {
    die;
}

if (is_plugin_active('woocommerce/woocommerce.php')) {
    // Define constants
    define('PAGADO_VERSION', '2.0.0');
    define('PAGADO_ROOT', plugin_dir_path(__FILE__));
    define('PAGADO_ROOT_URL', plugin_dir_url(__FILE__));

    // Register activation and deactivation hooks
    register_activation_hook(__FILE__, 'activate_pagado');
    register_deactivation_hook(__FILE__, 'deactivate_pagado');

    // Initialize Pagado features
    pagado_init();

    // Activate Dokan support if it's active
    if (is_plugin_active('dokan-lite/dokan.php') || is_plugin_active('dokan-pro/dokan.php')) {
        require_once plugin_dir_path(__FILE__) . 'dokan/pagado-dokan-payment-method.php';
    }
} else {
    // Display admin notice if WooCommerce is not activated
    add_action('admin_notices', 'no_woocommerce_notice');
}

function pagado_init()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado.php';
    $pagado = new Pagado();
    add_action('plugin_loaded', array($pagado, 'init'));
}

function activate_pagado()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado-activator.php';
    Pagado_Activator::activate();
}

function deactivate_pagado()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado-deactivator.php';
    Pagado_Deactivation::deactivate();
}

function no_woocommerce_notice()
{
    echo '<div class="notice notice-error"><p><strong>WooCommerce is required for Pagado payment gateway. Please install and activate WooCommerce first.</strong></p></div>';
}
