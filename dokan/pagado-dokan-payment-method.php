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
    $email = isset($store_settings['payment']['pagado']['email']) ? esc_attr($store_settings['payment']['pagado']['email']) : ''; ?>

    <div class="dokan-form-group">
        <div class="dokan-w8">
            <div class="dokan-input-group">
                <span class="dokan-input-group-addon"><?php esc_html_e('E-mail', 'dokan-lite'); ?></span>
                <input value="<?php echo esc_attr($email); ?>" name="settings[pagado][email]" class="dokan-form-control email" placeholder="you@domain.com" type="email">
            </div>
        </div>
    </div>
    <?php if (dokan_is_seller_dashboard()) : ?>
        <div class="dokan-form-group">
            <div class="dokan-w8">
                <input name="dokan_update_payment_settings" type="hidden">
                <button class="ajax_prev disconnect dokan_payment_disconnect_btn dokan-btn dokan-btn-danger <?php echo empty($email) ? 'dokan-hide' : ''; ?>" type="button" name="settings[pagado][disconnect]">
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
    if (isset($_POST['settings']['pagado']['email'])) {
        $dokan_settings['payment']['pagado'] = array(
            'email' => sanitize_text_field($_POST['settings']['pagado']['email']),
        );
    }
    update_user_meta($store_id, 'dokan_profile_settings', $dokan_settings);
}
add_action('dokan_store_profile_saved', 'save_withdraw_method_pagado', 10, 2);

function add_pagado_withdraw_in_payment_method_list($required_fields, $payment_method_id)
{
    if ('pagado' == $payment_method_id) {
        $required_fields = ['email'];
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
    if (isset($store_info['payment']['pagado']['email']) && $store_info['payment']['pagado']['email'] !== false) {
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
                    details = data[method].email || '';
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

/**
 * Hook for dokan withdraw approval action
 * @since 2.0.0
 */
function dokan_withdraw_on_approve($response, $withdraw, $request)
{
    $method = $withdraw->get_method();
    $status = $withdraw->get_status();
    $details = unserialize($withdraw->get_details());
    $body_params = $request->get_body_params();

    if (
        $method == 'pagado' &&
        $status == 1 && // status 1 accepted
        !empty($body_params) &&
        array_key_exists("status", $body_params)
    ) {
        if ($body_params["status"] == "approved") {
            $pagado_gateway = WC()->payment_gateways()->get_available_payment_gateways()['pagado'];
            $pagado_settings = $pagado_gateway->settings;

            if (!$pagado_settings["api_key"]) {
                $withdraw->set_note("Failed! API Direct key missing.");
                $withdraw->save();

                return $response;
            }

            $server = 'https://pagado'; // change for dev env
            $url = $server . '/api/direct/millix-send';

            $request = wp_remote_post($url, array(
                'body' => array(
                    'api_key' => $pagado_settings["api_key"],
                    'to' => $details["email"],
                    'amount' => $details["receivable"],
                    'pg_nonce' => '',
                ),
                'sslverify' => true, // enable
            ));

            $response = wp_remote_retrieve_body($request);
            $response = json_decode($response);

            if ($response->status == "success") {
                $withdraw->set_note("Success! Transaction ID: {$response->content}");

                // Action to execute when Pagado payment is successful
                do_action('pagado_dokan_on_admin_withdraw_success', $response, $withdraw, $request);
            } else {
                $withdraw->set_note("{$response->message}");

                // Action to execute when Pagado payment failed
                do_action('pagado_dokan_on_admin_withdraw_fail', $response, $withdraw, $request);
            }
            $withdraw->save();
        }
    }

    return $response;
}
add_filter('dokan_rest_prepare_withdraw_object', 'dokan_withdraw_on_approve', 10, 3);
