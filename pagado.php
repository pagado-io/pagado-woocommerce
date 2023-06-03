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
 * Plugin URI:      https:https://github.com/sajibsrs/pagado-wordpress-plugin
 * Description:     Pagado payment processor
 * Version:         0.1.0
 * Requires PHP:    7.2
 * Author:          Sajidur Rahman
 * Author URI:      #
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     pagado
 * Domain Path:     /languages
 */

if (!defined('WPINC')) {
    die;
}

define('PAGADO_VERSION', '0.1.0');

function activatePagado()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado-activator.php';
    PagadoActivator::activate();
}

function deactivatePagado()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-pagado-deactivator.php';
    PagadoDeactivation::deactivate();
}

register_activation_hook(__FILE__, 'activatePagado');
register_deactivation_hook(__FILE__, 'deactivatePagado');

require plugin_dir_path(__FILE__) . 'includes/class-pagado.php';

$pagado = new Pagado();
$pagado->run();
