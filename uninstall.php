<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Intencionalmente no se eliminan tablas para evitar pérdida accidental de atenciones históricas.
delete_option('agp_totem_settings');
delete_option('agp_totem_db_version');

delete_option('agp_totem_flush_rewrite');
