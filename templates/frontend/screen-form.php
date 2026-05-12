<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="agp-screen-content">
    <h2>Formulario de contacto</h2>

    <?php if (!empty($view_data['errors'])) : ?>
        <div class="agp-form-errors" role="alert">
            <?php foreach ($view_data['errors'] as $error) : ?>
                <p><?php echo esc_html($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="agp-totem-form">
        <?php wp_nonce_field('agp_totem_submit_lead', 'agp_totem_nonce'); ?>
        <input type="hidden" name="agp_totem_submit" value="1">
        <input type="hidden" name="selected_product" id="agp-selected-product" value="<?php echo esc_attr($view_data['form_values']['selected_product'] ?? ''); ?>">

        <label>Nombre cliente
            <input type="text" name="customer_name" required value="<?php echo esc_attr($view_data['form_values']['customer_name'] ?? ''); ?>">
        </label>
        <label>WhatsApp
            <input type="text" name="customer_whatsapp" required value="<?php echo esc_attr($view_data['form_values']['customer_whatsapp'] ?? ''); ?>">
        </label>
        <label>Correo (opcional)
            <input type="email" name="customer_email" value="<?php echo esc_attr($view_data['form_values']['customer_email'] ?? ''); ?>">
        </label>
        <label>Comuna / sector
            <input type="text" name="customer_location" value="<?php echo esc_attr($view_data['form_values']['customer_location'] ?? ''); ?>">
        </label>
        <label>Sucursal
            <input type="text" name="branch" required value="<?php echo esc_attr($view_data['form_values']['branch'] ?? ''); ?>">
        </label>
        <label>Tipo de consulta
            <input type="text" name="inquiry_type" required value="<?php echo esc_attr($view_data['form_values']['inquiry_type'] ?? ''); ?>">
        </label>
        <label>Comentario adicional
            <textarea name="comment" rows="3"><?php echo esc_textarea($view_data['form_values']['comment'] ?? ''); ?></textarea>
        </label>

        <button type="submit" class="agp-btn">Enviar atención</button>
    </form>
</div>
