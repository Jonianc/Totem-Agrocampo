<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="wrap agp-totem-admin">
    <h1><?php echo esc_html__('Tótem Agrocampo — Resumen', 'totem-agrocampo'); ?></h1>
    <p><?php echo esc_html__('Estado del plugin: Activo', 'totem-agrocampo'); ?></p>
    <div class="agp-cards">
        <div class="agp-card"><strong><?php echo esc_html__('Versión', 'totem-agrocampo'); ?></strong><br><?php echo esc_html(AGP_TOTEM_VERSION); ?></div>
        <div class="agp-card"><strong><?php echo esc_html__('Ruta frontend sugerida', 'totem-agrocampo'); ?></strong><br><?php echo esc_html($settings['frontend_route']); ?></div>
        <div class="agp-card"><strong><?php echo esc_html__('Atenciones', 'totem-agrocampo'); ?></strong><br><?php echo esc_html((string) $counts['leads']); ?></div>
        <div class="agp-card"><strong><?php echo esc_html__('Productos', 'totem-agrocampo'); ?></strong><br><?php echo esc_html((string) $counts['products']); ?></div>
        <div class="agp-card"><strong><?php echo esc_html__('Categorías', 'totem-agrocampo'); ?></strong><br><?php echo esc_html((string) $counts['categories']); ?></div>
    </div>
</div>
