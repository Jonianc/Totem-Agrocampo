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
        add_menu_page(__('Tótem Agrocampo', 'totem-agrocampo'), __('Tótem Agrocampo', 'totem-agrocampo'), 'manage_options', self::MENU_SLUG, array($this, 'render_summary_page'), 'dashicons-screenoptions', 58);
        add_submenu_page(self::MENU_SLUG, __('Resumen', 'totem-agrocampo'), __('Resumen', 'totem-agrocampo'), 'manage_options', self::MENU_SLUG, array($this, 'render_summary_page'));
        add_submenu_page(self::MENU_SLUG, __('Atenciones recibidas', 'totem-agrocampo'), __('Atenciones recibidas', 'totem-agrocampo'), 'manage_options', 'agp-totem-leads', array($this, 'render_leads_page'));
        add_submenu_page(self::MENU_SLUG, __('Productos / Servicios', 'totem-agrocampo'), __('Productos / Servicios', 'totem-agrocampo'), 'manage_options', 'agp-totem-products', array($this, 'render_products_page'));
        add_submenu_page(self::MENU_SLUG, __('Categorías', 'totem-agrocampo'), __('Categorías', 'totem-agrocampo'), 'manage_options', 'agp-totem-categories', array($this, 'render_categories_page'));
        add_submenu_page(self::MENU_SLUG, __('Configuración', 'totem-agrocampo'), __('Configuración', 'totem-agrocampo'), 'manage_options', 'agp-totem-settings', array($this, 'render_settings_page'));
        add_submenu_page(self::MENU_SLUG, __('Diseño / Pantallas', 'totem-agrocampo'), __('Diseño / Pantallas', 'totem-agrocampo'), 'manage_options', 'agp-totem-design', array($this, 'render_design_page'));
    }

    public function render_summary_page() { $this->assert_manage_options(); $settings = AGP_Totem_Settings::get_all(); $counts = array('leads' => $this->get_table_count('agp_totem_leads'),'products' => $this->get_table_count('agp_totem_products'),'categories' => $this->get_table_count('agp_totem_categories')); include AGP_TOTEM_PATH . 'templates/admin/summary.php'; }

    public function render_leads_page() {
        $this->assert_manage_options();
        $repo = new AGP_Totem_Leads_Repository();

        $action_notice = null;
        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['agp_totem_lead_action'])) {
            check_admin_referer('agp_totem_lead_update', 'agp_totem_lead_nonce');
            $post = wp_unslash($_POST);
            $lead_id = isset($post['lead_id']) ? (int) $post['lead_id'] : 0;

            if ('update_status' === $post['agp_totem_lead_action']) {
                $ok = $repo->update_status($lead_id, isset($post['status']) ? $post['status'] : '');
                $action_notice = array('type' => $ok ? 'updated' : 'error', 'message' => $ok ? __('Estado actualizado.', 'totem-agrocampo') : __('No se pudo actualizar el estado.', 'totem-agrocampo'));
            }

            if ('update_detail' === $post['agp_totem_lead_action']) {
                $ok = $repo->update_notes_and_assignment($lead_id, isset($post['internal_notes']) ? $post['internal_notes'] : '', isset($post['assigned_user_id']) ? (int) $post['assigned_user_id'] : 0);
                $action_notice = array('type' => $ok ? 'updated' : 'error', 'message' => $ok ? __('Detalle guardado.', 'totem-agrocampo') : __('No se pudo guardar el detalle.', 'totem-agrocampo'));
            }
        }

        $filters = array(
            'date_from' => isset($_GET['date_from']) ? sanitize_text_field(wp_unslash($_GET['date_from'])) : '',
            'date_to' => isset($_GET['date_to']) ? sanitize_text_field(wp_unslash($_GET['date_to'])) : '',
            'branch' => isset($_GET['branch']) ? sanitize_text_field(wp_unslash($_GET['branch'])) : '',
            'status' => isset($_GET['status']) ? sanitize_key(wp_unslash($_GET['status'])) : '',
            'inquiry_type' => isset($_GET['inquiry_type']) ? sanitize_text_field(wp_unslash($_GET['inquiry_type'])) : '',
            'search' => isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '',
        );

        $current_page = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
        $per_page = AGP_Totem_Leads_Repository::PER_PAGE_DEFAULT;
        $total_items = $repo->count_leads($filters);
        $total_pages = max(1, (int) ceil($total_items / $per_page));
        $current_page = min($current_page, $total_pages);

        $view = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : 'list';
        $lead_id = isset($_GET['lead_id']) ? (int) $_GET['lead_id'] : 0;
        $lead_detail = null;
        if ('detail' === $view && $lead_id > 0) {
            $lead_detail = $repo->get_lead_by_id($lead_id);
        }

        $leads = $repo->get_leads_paginated($filters, $current_page, $per_page);
        $filter_options = $repo->get_filter_options();
        $statuses = AGP_Totem_Leads_Repository::allowed_statuses();
        $assignable_users = get_users(array('role__in' => array('administrator'),'orderby' => 'display_name','order' => 'ASC'));
        include AGP_TOTEM_PATH . 'templates/admin/leads.php';
    }

    public function render_products_page() { $this->assert_manage_options(); include AGP_TOTEM_PATH . 'templates/admin/products.php'; }
    public function render_categories_page() { $this->assert_manage_options(); include AGP_TOTEM_PATH . 'templates/admin/categories.php'; }

    public function render_settings_page() {
        $this->assert_manage_options();
        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['agp_totem_settings_nonce'])) { check_admin_referer('agp_totem_save_settings', 'agp_totem_settings_nonce'); $input = wp_unslash($_POST); $current = AGP_Totem_Settings::get_all(); $updated = AGP_Totem_Settings::update($input); add_settings_error('agp_totem_messages', 'agp_totem_message', __('Configuración guardada.', 'totem-agrocampo'), 'updated'); if ($current['frontend_route'] !== $updated['frontend_route']) { add_settings_error('agp_totem_messages', 'agp_totem_route_updated', __('Ruta frontend actualizada. Los enlaces del tótem usarán la nueva ruta.', 'totem-agrocampo'), 'updated'); } }
        $settings = AGP_Totem_Settings::get_all(); settings_errors('agp_totem_messages'); include AGP_TOTEM_PATH . 'templates/admin/settings.php';
    }

    public function render_design_page() { $this->assert_manage_options(); include AGP_TOTEM_PATH . 'templates/admin/design.php'; }

    private function assert_manage_options() { if (!current_user_can('manage_options')) { wp_die(esc_html__('No tienes permisos para acceder a esta sección.', 'totem-agrocampo')); } }

    private function get_table_count($table_name) {
        global $wpdb;
        $table = $wpdb->prefix . $table_name;
        $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
        if ($exists !== $table) { return 0; }
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    }
}
