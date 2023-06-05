<?php

/**
 * Pagado admin UI
 *
 * @link            #
 * @since           0.1.0
 *
 * @package         Pagado
 * @subpackage      Pagado/admin/templates
 */

class PagadoAdminUI
{
    public static function optionPageHTML()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error('pagado_messages', 'pagado_message', __('Settings Saved', 'pagado'), 'updated');
        }

        settings_errors('pagado_messages'); ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('pagado');
                do_settings_sections('pagado');
                submit_button('Save Settings'); ?>
            </form>
        </div>
    <?php
    }

    public static function sectionHTML($args)
    { ?>
        <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Select from options.', 'pagado'); ?></p>
    <?php
    }

    public static function fieldHTML($args)
    {
        $options = get_option('pagado_options'); ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['pagado_custom_data']); ?>" name="pagado_options[<?php echo esc_attr($args['label_for']); ?>]">
            <option value="one" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'one', false)) : (''); ?>>
                <?php esc_html_e('Option one', 'pagado'); ?>
            </option>
            <option value="two" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'two', false)) : (''); ?>>
                <?php esc_html_e('Option two', 'pagado'); ?>
            </option>
        </select>
        <p class="description">
            <?php esc_html_e('Description goes here.', 'pagado'); ?>
        </p>
    <?php
    }
}
