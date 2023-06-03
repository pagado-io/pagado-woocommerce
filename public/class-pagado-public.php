<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link            #
 * @since           0.1.0
 *
 * @package Pagado
 * @subpackage Pagado/public
 */


class PagadoPublic
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
            plugin_dir_url(__FILE__) . 'css/pagado-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueueScripts()
    {
        wp_enqueue_script(
            $this->pluginName,
            plugin_dir_url(__FILE__) . 'js/pagado-public.js',
            array('jquery'),
            $this->version,
            false
        );
    }
}
