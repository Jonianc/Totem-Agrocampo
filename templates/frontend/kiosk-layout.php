<?php if (!defined('ABSPATH')) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc_html__('Tótem Agrocampo', 'totem-agrocampo'); ?></title>
    <?php wp_head(); ?>
</head>
<body class="agp-kiosk-body">
<main class="agp-kiosk-app" data-route="<?php echo esc_attr($view_data['route']); ?>" data-initial-screen="<?php echo esc_attr($view_data['active_screen'] ?? 'home'); ?>" data-has-ticket="<?php echo !empty($view_data['ticket_number']) ? '1' : '0'; ?>">
    <header class="agp-kiosk-header">
        <div class="agp-brand">AGROCAMPO</div>
        <div class="agp-header-actions">
            <button type="button" class="agp-btn agp-btn-secondary" data-action="back">Volver</button>
            <button type="button" class="agp-btn" data-action="home">Inicio</button>
        </div>
    </header>

    <section class="agp-progress" aria-label="Progreso">
        <div class="agp-progress-step is-active" data-step="home">1. Inicio</div>
        <div class="agp-progress-step" data-step="category">2. Necesidad</div>
        <div class="agp-progress-step" data-step="products">3. Destacados</div>
        <div class="agp-progress-step" data-step="form">4. Formulario</div>
        <div class="agp-progress-step" data-step="confirm">5. Confirmación</div>
        <div class="agp-progress-step" data-step="success">6. Final</div>
    </section>

    <section class="agp-kiosk-screen" data-screen="home"><?php include AGP_TOTEM_PATH . 'templates/frontend/screen-home.php'; ?></section>
    <section class="agp-kiosk-screen is-hidden" data-screen="category"><?php include AGP_TOTEM_PATH . 'templates/frontend/screen-category.php'; ?></section>
    <section class="agp-kiosk-screen is-hidden" data-screen="products"><?php include AGP_TOTEM_PATH . 'templates/frontend/screen-products.php'; ?></section>
    <section class="agp-kiosk-screen is-hidden" data-screen="form"><?php include AGP_TOTEM_PATH . 'templates/frontend/screen-form.php'; ?></section>
    <section class="agp-kiosk-screen is-hidden" data-screen="confirm"><?php include AGP_TOTEM_PATH . 'templates/frontend/screen-confirm.php'; ?></section>
    <section class="agp-kiosk-screen is-hidden" data-screen="success"><?php include AGP_TOTEM_PATH . 'templates/frontend/screen-success.php'; ?></section>

    <div class="agp-timeout-warning is-hidden" id="agp-timeout-warning"></div>
</main>
<?php wp_footer(); ?>
</body>
</html>
