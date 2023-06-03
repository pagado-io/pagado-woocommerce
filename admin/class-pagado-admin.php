<?php

/**
 * The admin-specific functionality of Pagado
 * @link        #
 * @since       0.1.0
 *
 * @package Pagado
 * @subpackage Pagado/admin
 */

class PagadoAdmin
{
    private $pluginName;
    private $version;

    public function __construct($pluginName, $version)
    {
        $this->pluginName = $pluginName;
        $this->version = $version;
    }

    public function enqueueStyles()
    {
        wp_enqueue_style(
            $this->pluginName,
            plugin_dir_url(__FILE__) . 'css/pagado-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueueScripts()
    {
        wp_enqueue_script(
            $this->pluginName,
            plugin_dir_url(__FILE__) . 'js/pagado-admin.js', array('jquery'),
            $this->version,
            false
        );
    }
}
