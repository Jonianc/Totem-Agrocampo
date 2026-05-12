<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="agp-screen-content">
    <h1><?php echo esc_html($view_data['welcome_message']); ?></h1>
    <div class="agp-grid-options">
        <button type="button" class="agp-card-btn" data-next="category" data-type="Cotizar tractor">Cotizar tractor</button>
        <button type="button" class="agp-card-btn" data-next="category" data-type="Cotizar implemento agrícola">Cotizar implemento agrícola</button>
        <button type="button" class="agp-card-btn" data-next="category" data-type="Consultar servicio técnico">Consultar servicio técnico</button>
        <button type="button" class="agp-card-btn" data-next="category" data-type="Consultar repuestos">Consultar repuestos</button>
        <button type="button" class="agp-card-btn" data-next="category" data-type="Hablar con un vendedor">Hablar con un vendedor</button>
        <button type="button" class="agp-card-btn" data-next="category" data-type="Ver promociones o destacados">Ver promociones o destacados</button>
    </div>
    <button type="button" class="agp-btn agp-btn-secondary" data-action="home">Reiniciar atención</button>
</div>
