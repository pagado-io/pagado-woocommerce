<?php

/**
 * This file defines core plugin class.
 *
 * @link            #
 * @since           0.1.0
 *
 * @package         Pagado
 * @subpackage      Pagado/includes
 */

final class Pagado
{
    public $plugin_name;
    public $version;

    private $plugin_admin;
    private $plugin_public;
    private $plugin_i18n;

    function __construct()
    {
        if (defined('PAGADO_VERSION')) {
            $this->version = PAGADO_VERSION;
        } else {
            $this->version = '0.1.0';
        }

        $this->plugin_name = 'pagado';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-pagado-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-pagado-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-pagado-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'gateway/class-pagado-payment-gateway-loader.php';

        $this->plugin_admin = new Pagado_Admin($this->plugin_name, $this->version);
        $this->plugin_public = new Pagado_Public($this->plugin_name, $this->version);
        $this->plugin_i18n  = new Pagado_I18n();
    }

    /**
     * Initialize Pagado
     */
    public function initialize()
    {
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_payment_gateway();
    }

    private function set_locale()
    {
        add_action('init', array($this->plugin_i18n, 'load_plugin_text_domain'));
    }

    private function define_admin_hooks()
    {
        add_action('admin_enqueue_scripts', array($this->plugin_admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this->plugin_admin, 'enqueue_scripts'));
        add_filter('woocommerce_currencies', array($this->plugin_admin, 'add_millix_currency'));
        add_filter('woocommerce_currency_symbol', array($this->plugin_admin, 'add_millix_currency_symbol'), 10, 2);
    }

    private function define_public_hooks()
    {
        add_action('wp_enqueue_scripts', array($this->plugin_public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this->plugin_public, 'enqueue_scripts'));
        add_action('wc_ajax_get_pagado_data', array($this->plugin_public, 'get_pagado_order_data'));
        add_action('woocommerce_review_order_after_submit', array($this->plugin_public, 'add_after_checkout_button'), 30);
    }

    private function define_payment_gateway()
    {
        add_action('init', array(Pagado_Payment_Gateway_Loader::class, 'load_gateway'));
        add_filter('woocommerce_payment_gateways', array(Pagado_Payment_Gateway_Loader::class, 'add_gateway'));
    }

    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    public function get_version()
    {
        return $this->version;
    }
}
