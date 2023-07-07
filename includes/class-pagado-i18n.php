<?php

/**
 * Define the internationalization functionality
 *
 * @link            #
 * @since           0.1.0
 *
 * @package         Pagado
 * @subpackage      Pagado/includes
 */

class Pagado_I18n
{
    public function load_plugin_text_domain()
    {
        load_plugin_textdomain(
            'pagado',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages'
        );
    }
}
