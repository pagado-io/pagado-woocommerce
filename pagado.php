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
 * Description:     Pagado payment processor.
 * Version:         1.2.4
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
    define('PAGADO_VERSION', '1.2.4');
    define('PAGADO_ROOT', plugin_dir_path(__FILE__));

    register_activation_hook(__FILE__, 'activate_pagado');
    register_deactivation_hook(__FILE__, 'deactivate_pagado');

    require plugin_dir_path(__FILE__) . 'includes/class-pagado.php';

    $pagado = new Pagado();
    $pagado->init();
} else {
    add_action('admin_notices', 'no_woocommerce_notice');
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
