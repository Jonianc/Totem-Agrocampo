<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Plugin {
    public function init() {
        load_plugin_textdomain('totem-agrocampo', false, dirname(plugin_basename(AGP_TOTEM_FILE)) . '/languages');

        if (is_admin()) {
            $admin_menu = new AGP_Totem_Admin_Menu();
            $admin_menu->init();
            add_action('admin_init', array($this, 'maybe_flush_rewrite_rules'));
        }

        $router = new AGP_Totem_Router();
        $router->init();
    }

    public function maybe_flush_rewrite_rules() {
        if ((int) get_option(AGP_Totem_Settings::FLUSH_REWRITE_OPTION, 0) !== 1) {
            return;
        }

        AGP_Totem_Router::flush_rules();
        delete_option(AGP_Totem_Settings::FLUSH_REWRITE_OPTION);
    }
}
