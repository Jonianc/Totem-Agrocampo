<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="wrap">
    <h1><?php echo esc_html__('Configuración', 'totem-agrocampo'); ?></h1>
    <form method="post">
        <?php wp_nonce_field('agp_totem_save_settings', 'agp_totem_settings_nonce'); ?>
        <table class="form-table" role="presentation">
            <tr><th scope="row"><label for="frontend_route">Ruta frontend</label></th><td><input name="frontend_route" id="frontend_route" type="text" class="regular-text" value="<?php echo esc_attr($settings['frontend_route']); ?>"></td></tr>
            <tr><th scope="row"><label for="welcome_message">Mensaje bienvenida</label></th><td><textarea name="welcome_message" id="welcome_message" class="large-text" rows="3"><?php echo esc_textarea($settings['welcome_message']); ?></textarea></td></tr>
            <tr><th scope="row"><label for="closing_message">Mensaje cierre</label></th><td><textarea name="closing_message" id="closing_message" class="large-text" rows="3"><?php echo esc_textarea($settings['closing_message']); ?></textarea></td></tr>
            <tr><th scope="row"><label for="inactivity_timeout">Timeout inactividad (segundos)</label></th><td><input name="inactivity_timeout" id="inactivity_timeout" type="number" min="10" class="small-text" value="<?php echo esc_attr((string) $settings['inactivity_timeout']); ?>"></td></tr>
            <tr><th scope="row"><label for="whatsapp_main">WhatsApp principal</label></th><td><input name="whatsapp_main" id="whatsapp_main" type="text" class="regular-text" value="<?php echo esc_attr($settings['whatsapp_main']); ?>"></td></tr>
            <tr><th scope="row"><label for="internal_emails">Correos internos (separados por coma)</label></th><td><input name="internal_emails" id="internal_emails" type="text" class="large-text" value="<?php echo esc_attr($settings['internal_emails']); ?>"></td></tr>
            <tr><th scope="row">Sucursales</th><td>
                <p><label>Talca <input name="branch_talca" type="text" value="<?php echo esc_attr($settings['branches']['talca']); ?>"></label></p>
                <p><label>Linares <input name="branch_linares" type="text" value="<?php echo esc_attr($settings['branches']['linares']); ?>"></label></p>
                <p><label>Parral <input name="branch_parral" type="text" value="<?php echo esc_attr($settings['branches']['parral']); ?>"></label></p>
            </td></tr>
        </table>
        <?php submit_button(__('Guardar configuración', 'totem-agrocampo')); ?>
    </form>
</div>
