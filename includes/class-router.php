<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Router {
    const QUERY_VAR = 'agp_totem_kiosk';

    private $frontend_controller;

    public function __construct() {
        $this->frontend_controller = new AGP_Totem_Frontend_Controller();
    }

    public function init() {
        add_action('init', array($this, 'register_rewrite_rule'));
        add_filter('query_vars', array($this, 'register_query_vars'));
        add_action('template_redirect', array($this, 'maybe_render_kiosk'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function register_rewrite_rule() {
        $settings = AGP_Totem_Settings::get_all();
        $path = trim($settings['frontend_route'], '/');

        if ($path === '') {
            $path = 'totem-agrocampo';
        }

        add_rewrite_rule('^' . preg_quote($path, '/') . '/?$', 'index.php?' . self::QUERY_VAR . '=1', 'top');
    }

    public function register_query_vars($vars) {
        $vars[] = self::QUERY_VAR;
        return $vars;
    }

    public function maybe_render_kiosk() {
        if ((int) get_query_var(self::QUERY_VAR) !== 1) {
            return;
        }

        $this->frontend_controller->render();
        exit;
    }

    public function enqueue_assets() {
        if ((int) get_query_var(self::QUERY_VAR) !== 1) {
            return;
        }

        wp_enqueue_style('agp-totem-kiosk', AGP_TOTEM_URL . 'assets/css/kiosk.css', array(), AGP_TOTEM_VERSION);
        wp_enqueue_script('agp-totem-kiosk-flow', AGP_TOTEM_URL . 'assets/js/kiosk-flow.js', array(), AGP_TOTEM_VERSION, true);

        $settings = AGP_Totem_Settings::get_all();
        wp_enqueue_script('agp-totem-kiosk-timeout', AGP_TOTEM_URL . 'assets/js/kiosk-timeout.js', array(), AGP_TOTEM_VERSION, true);
        wp_localize_script('agp-totem-kiosk-timeout', 'agpTotemTimeout', array(
            'timeoutSeconds' => max(10, (int) $settings['inactivity_timeout']),
            'warningSeconds' => 10,
            'homeUrl' => esc_url_raw(home_url($settings['frontend_route'])),
            'warningText' => __('La sesión se reiniciará por inactividad.', 'totem-agrocampo'),
        ));
    }

    public static function flush_rules() {
        $router = new self();
        $router->register_rewrite_rule();
        flush_rewrite_rules();
    }
}
