<?php

/**
 * Pagado Dokan payment method
 *
 * @link            #
 * @since           1.3.0
 *
 * @package         Pagado
 * @subpackage      Pagado/dokan
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Register Pagado withdrawal method
 */
function register_pagado_withdraw_method($methods)
{
    $methods['pagado'] = [
        'title'     => __('Pagado', 'dokan-lite'),
        'callback'  => 'dokan_withdraw_method_pagado',
        'apply_charge' => true
    ];
    return $methods;
}
add_filter('dokan_withdraw_methods', 'register_pagado_withdraw_method', 99);

/**
 * Pagado withdraw method front end
 */
function dokan_withdraw_method_pagado($store_settings)
{
    $value = isset($store_settings['payment']['pagado']['value']) ? esc_attr($store_settings['payment']['pagado']['value']) : ''; ?>

    <div class="dokan-form-group">
        <div class="dokan-w8">
            <div class="dokan-input-group">
                <span class="dokan-input-group-addon"><?php esc_html_e('E-mail', 'dokan-lite'); ?></span>
                <input value="<?php echo esc_attr($value); ?>" name="settings[pagado][value]" class="dokan-form-control value" placeholder="you@domain.com" type="text">
            </div>
        </div>
    </div>
    <?php if (dokan_is_seller_dashboard()) : ?>
        <div class="dokan-form-group">
            <div class="dokan-w8">
                <input name="dokan_update_payment_settings" type="hidden">
                <button class="ajax_prev disconnect dokan_payment_disconnect_btn dokan-btn dokan-btn-danger <?php echo empty($value) ? 'dokan-hide' : ''; ?>" type="button" name="settings[pagado][disconnect]">
                    <?php esc_attr_e('Disconnect', 'dokan-lite'); ?>
                </button>
            </div>
        </div>
    <?php endif; ?>
<?php
}

/**
 * Save Pagado withdrawal method data
 */
function save_withdraw_method_pagado($store_id, $dokan_settings)
{
    if (isset($_POST['settings']['pagado']['value'])) {
        $dokan_settings['payment']['pagado'] = array(
            'value' => sanitize_text_field($_POST['settings']['pagado']['value']),
        );
    }
    update_user_meta($store_id, 'dokan_profile_settings', $dokan_settings);
}
add_action('dokan_store_profile_saved', 'save_withdraw_method_pagado', 10, 2);

function add_pagado_withdraw_in_payment_method_list($required_fields, $payment_method_id)
{
    if ('pagado' == $payment_method_id) {
        $required_fields = ['value'];
    }

    return $required_fields;
}
add_filter('dokan_payment_settings_required_fields', 'add_pagado_withdraw_in_payment_method_list', 10, 2);

/**
 * Add pagado in active withdraw method
 */
function pagado_in_active_withdraw_method($active_payment_methods, $vendor_id)
{
    $store_info = dokan_get_store_info($vendor_id);
    if (isset($store_info['payment']['pagado']['value']) && $store_info['payment']['pagado']['value'] !== false) {
        $active_payment_methods[] = 'pagado';
    }

    return $active_payment_methods;
}
add_filter('dokan_get_seller_active_withdraw_methods', 'pagado_in_active_withdraw_method', 99, 2);

/**
 * Add pagado in withdraw payment methods
 */
function include_pagado_in_withdraw_method_section($methods)
{
    $methods[] = 'pagado';
    return $methods;
}
add_filter('dokan_withdraw_withdrawable_payment_methods', 'include_pagado_in_withdraw_method_section');


/**
 * Add details to the withdrawal requests
 */
function pagado_admin_withdraw()
{
?>
    <script>
        var hooks;

        function getPagadoPaymentDetails(details, method, data) {
            if (data[method] !== undefined) {
                if ('pagado' === method) {
                    details = data[method].value || '';
                }
            }

            return details;
        }
        dokan.hooks.addFilter('dokan_get_payment_details', 'getPagadoPaymentDetails', getPagadoPaymentDetails, 33, 3);
    </script>
<?php
}
add_action('admin_print_footer_scripts', 'pagado_admin_withdraw', 99);

/**
 * Add Pagado icon
 */
function add_pagado_gateway_icon($method_icon, $method_key)
{
    if ($method_key == 'pagado') {
        return PAGADO_ROOT_URL . 'public/img/pagado-icon.png';
    }
    return $method_icon;
}
add_filter('dokan_withdraw_method_icon', 'add_pagado_gateway_icon', 10, 2);
