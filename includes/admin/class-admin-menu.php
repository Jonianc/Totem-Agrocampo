<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Admin_Menu {
    const MENU_SLUG = 'agp-totem';

    public function init() {
        add_action('admin_menu', array($this, 'register_menus'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'agp-totem') === false) {
            return;
        }
        wp_enqueue_style('agp-totem-admin', AGP_TOTEM_URL . 'assets/css/admin.css', array(), AGP_TOTEM_VERSION);
    }

    public function register_menus() {
        add_menu_page(
            __('Tótem Agrocampo', 'totem-agrocampo'),
            __('Tótem Agrocampo', 'totem-agrocampo'),
            'manage_options',
            self::MENU_SLUG,
            array($this, 'render_summary_page'),
            'dashicons-screenoptions',
            58
        );

        add_submenu_page(self::MENU_SLUG, __('Resumen', 'totem-agrocampo'), __('Resumen', 'totem-agrocampo'), 'manage_options', self::MENU_SLUG, array($this, 'render_summary_page'));
        add_submenu_page(self::MENU_SLUG, __('Atenciones recibidas', 'totem-agrocampo'), __('Atenciones recibidas', 'totem-agrocampo'), 'manage_options', 'agp-totem-leads', array($this, 'render_leads_page'));
        add_submenu_page(self::MENU_SLUG, __('Productos / Servicios', 'totem-agrocampo'), __('Productos / Servicios', 'totem-agrocampo'), 'manage_options', 'agp-totem-products', array($this, 'render_products_page'));
        add_submenu_page(self::MENU_SLUG, __('Categorías', 'totem-agrocampo'), __('Categorías', 'totem-agrocampo'), 'manage_options', 'agp-totem-categories', array($this, 'render_categories_page'));
        add_submenu_page(self::MENU_SLUG, __('Configuración', 'totem-agrocampo'), __('Configuración', 'totem-agrocampo'), 'manage_options', 'agp-totem-settings', array($this, 'render_settings_page'));
        add_submenu_page(self::MENU_SLUG, __('Diseño / Pantallas', 'totem-agrocampo'), __('Diseño / Pantallas', 'totem-agrocampo'), 'manage_options', 'agp-totem-design', array($this, 'render_design_page'));
    }

    public function render_summary_page() {
        $this->assert_manage_options();

        $settings = AGP_Totem_Settings::get_all();
        $counts = array(
            'leads' => $this->get_table_count('agp_totem_leads'),
            'products' => $this->get_table_count('agp_totem_products'),
            'categories' => $this->get_table_count('agp_totem_categories'),
        );
        include AGP_TOTEM_PATH . 'templates/admin/summary.php';
    }

    public function render_leads_page() {
        $this->assert_manage_options();
        include AGP_TOTEM_PATH . 'templates/admin/leads.php';
    }

    public function render_products_page() {
        $this->assert_manage_options();
        include AGP_TOTEM_PATH . 'templates/admin/products.php';
    }

    public function render_categories_page() {
        $this->assert_manage_options();
        include AGP_TOTEM_PATH . 'templates/admin/categories.php';
    }

    public function render_settings_page() {
        $this->assert_manage_options();

        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['agp_totem_settings_nonce'])) {
            check_admin_referer('agp_totem_save_settings', 'agp_totem_settings_nonce');
            $input = wp_unslash($_POST);
            AGP_Totem_Settings::update($input);
            add_settings_error('agp_totem_messages', 'agp_totem_message', __('Configuración guardada.', 'totem-agrocampo'), 'updated');
        }

        $settings = AGP_Totem_Settings::get_all();
        settings_errors('agp_totem_messages');
        include AGP_TOTEM_PATH . 'templates/admin/settings.php';
    }

    public function render_design_page() {
        $this->assert_manage_options();
        include AGP_TOTEM_PATH . 'templates/admin/design.php';
    }

    private function assert_manage_options() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('No tienes permisos para acceder a esta sección.', 'totem-agrocampo'));
        }
    }

    private function get_table_count($table_name) {
        global $wpdb;

        $table = $wpdb->prefix . $table_name;
        $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));

        if ($exists !== $table) {
            return 0;
        }

        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    }
}

