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
        }
    }
}
