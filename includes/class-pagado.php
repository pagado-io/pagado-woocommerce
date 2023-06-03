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
    protected $loader;
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

        $this->loadDependencies();
        $this->setlocale();
        $this->defineAdminHooks();
        $this->definePublicHooks();
    }

    private function loadDependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-pagado-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-pagado-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-pagado-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-pagado-public.php';

        $this->loader = new PagadoLoader();
    }

    private function setLocale()
    {
        $pluginI18n = new PagadoI18n();
        $this->loader->addAction('plugins_loaded', $pluginI18n, 'loadPluginTextDomain');
    }

    private function defineAdminHooks()
    {
        $pluginAdmin = new PagadoAdmin($this->pluginName, $this->version);

        $this->loader->addAction('wp_enqueue_scripts', $pluginAdmin, 'enqueueStyles');
        $this->loader->addAction('wp_enqueue_scripts', $pluginAdmin, 'enqueueScripts');
    }

    private function definePublicHooks()
    {
        $pluginPublic = new PagadoPublic($this->pluginName, $this->version);

        $this->loader->addAction('wp_enqueue_scripts', $pluginPublic, 'enqueueStyles');
        $this->loader->addAction('wp_enqueue_scripts', $pluginPublic, 'enqueueScripts');
    }

    public function run()
    {
        $this->loader->run();
    }

    public function getPluginName()
    {
        return $this->pluginName;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
