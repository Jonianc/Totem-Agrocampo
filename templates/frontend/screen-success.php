<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="agp-screen-content">
    <h2>Solicitud recibida</h2>
    <p><?php echo esc_html($view_data['closing_message']); ?></p>
    <p>Número de atención: <strong><?php echo esc_html($view_data['ticket_number'] ?: '---'); ?></strong></p>
    <p>Agrocampo se contactará contigo a la brevedad.</p>
    <button type="button" class="agp-btn" data-action="home">Volver al inicio</button>
</div>
