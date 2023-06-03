<?php

/**
 * Define the internationalization functionality
 *
 * @link            #
 * @since           0.1.0
 *
 * @package Pagado
 * @subpackage Pagado/includes
 */

class PagadoI18n
{
    public function loadPluginTextDomain()
    {
        load_plugin_textdomain(
            'pagado',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages'
        );
    }
}
