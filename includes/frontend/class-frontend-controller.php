<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Frontend_Controller {
    public function render() {
        status_header(200);
        nocache_headers();

        $settings = AGP_Totem_Settings::get_all();
        $view_data = array(
            'welcome_message' => $settings['welcome_message'],
            'closing_message' => $settings['closing_message'],
            'route' => $settings['frontend_route'],
        );

        include AGP_TOTEM_PATH . 'templates/frontend/kiosk-layout.php';
    }
}
