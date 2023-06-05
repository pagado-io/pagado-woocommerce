<?php

/**
 * The admin-specific functionality of Pagado
 * @link        #
 * @since       0.1.0
 *
 * @package     Pagado
 * @subpackage  Pagado/admin
 */

class PagadoAdmin
{
    private $pluginName;
    private $version;
    private $uiClass;

    public function __construct($uiClass, $pluginName, $version)
    {
        $this->pluginName = $pluginName;
        $this->version = $version;
        $this->uiClass = $uiClass;
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
            plugin_dir_url(__FILE__) . 'js/pagado-admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    public function settingsInit()
    {
        register_setting('pagado', 'pagado_options');

        add_settings_section(
            'pagado_section',
            __('Section', 'pagado'),
            array($this->uiClass, 'sectionHTML'),
            'pagado'
        );

        add_settings_field(
            'pagado_field',
            __('Field', 'pagado'),
            array($this->uiClass, 'fieldHTML'),
            'pagado',
            'pagado_section',
            array(
                'label_for' => 'pagado_field',
                'class' => 'pagado_row',
                'pagado_custom_data' => 'custom',
            )
        );
    }

    public function menuPageInit()
    {
        add_menu_page(
            __('Pagado', 'pagado'),
            'Pagado',
            'manage_options',
            'pagado',
            array($this->uiClass, 'optionPageHTML'),
        );
    }
}
