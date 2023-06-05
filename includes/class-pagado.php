<?php

/**
 * This file defines core plugin class
 *
 * @link            #
 * @since           0.1.0
 *
 * @package         Pagado
 * @subpackage      Pagado/includes
 */

class Pagado
{
    protected $pluginName;
    protected $version;

    function __construct()
    {
        if (defined('PAGADO_VERSION')) {
            $this->version = PAGADO_VERSION;
        } else {
            $this->version = '0.1.0';
        }
        $this->pluginName = 'pagado';
    }

    public function init()
    {
        $this->loadDependencies();
        $this->setlocale();
        $this->defineAdminHooks();
        $this->definePublicHooks();
        $this->definePaymentGateway();
    }

    private function loadDependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-pagado-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-pagado-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/templates/class-pagado-admin-ui.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-pagado-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'gateway/class-pagado-payment-gateway-loader.php';
    }

    private function setLocale()
    {
        $pluginI18n = new PagadoI18n();
        add_action('plugins_loaded', array($pluginI18n, 'loadPluginTextDomain'));
    }

    private function defineAdminHooks()
    {
        $pluginAdmin = new PagadoAdmin(PagadoAdminUI::class, $this->pluginName, $this->version);

        add_action('admin_enqueue_scripts', array($pluginAdmin, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($pluginAdmin, 'enqueueScripts'));
        add_action('admin_init', array($pluginAdmin, 'settingsInit'));
        add_action('admin_menu', array($pluginAdmin, 'menuPageInit'));
    }

    private function definePublicHooks()
    {
        $pluginPublic = new PagadoPublic($this->pluginName, $this->version);

        add_action('wp_enqueue_scripts', array($pluginPublic, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($pluginPublic, 'enqueueScripts'));
    }

    private function definePaymentGateway()
    {
        // TODO: Check if woocommerce is active

        $gatewayHelper = PagadoPaymentGatewayLoader::class;

        add_action('plugins_loaded', array($gatewayHelper, 'loadGateway'));
        add_filter('woocommerce_payment_gateways', array($gatewayHelper, 'addGateway'));

        // TODO: Display admin notice if woocommerce is not active
    }

    public function getPluginName()
    {
        return $this->pluginName;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
