<?php
/**
 * Plugin Name: Tótem Agrocampo
 * Description: Base técnica inicial para el sistema de autoatención Tótem Agrocampo.
 * Version: 0.1.1
 * Author: Agrocampo
 * Text Domain: totem-agrocampo
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AGP_TOTEM_VERSION', '0.1.1');
define('AGP_TOTEM_FILE', __FILE__);
define('AGP_TOTEM_PATH', plugin_dir_path(__FILE__));
define('AGP_TOTEM_URL', plugin_dir_url(__FILE__));

require_once AGP_TOTEM_PATH . 'includes/class-installer.php';
require_once AGP_TOTEM_PATH . 'includes/class-settings.php';
require_once AGP_TOTEM_PATH . 'includes/admin/class-admin-menu.php';
require_once AGP_TOTEM_PATH . 'includes/class-plugin.php';

register_activation_hook(AGP_TOTEM_FILE, array('AGP_Totem_Installer', 'activate'));

function agp_totem_init() {
    $plugin = new AGP_Totem_Plugin();
    $plugin->init();
}
add_action('plugins_loaded', 'agp_totem_init');
